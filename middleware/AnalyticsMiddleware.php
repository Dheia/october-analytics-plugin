<?php

namespace Synder\Analytics\Middleware;

use BackendAuth;
use Closure;
use Exception;
use Log;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Str;

use Synder\Analytics\Models\Page;
use Synder\Analytics\Models\Referrer;
use Synder\Analytics\Models\Request;
use Synder\Analytics\Models\Settings;
use Synder\Analytics\Models\Visitor;


class AnalyticsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(HttpRequest $request, Closure $next)
    {
        $settings = Settings::instance();

        // robots.txt calls
        if ($settings->getRobotsTxtProvider() === 'synder.analytics') {
            $response = $this->handleRobotsTxt($request);
        }
        $path = ltrim(explode('?', $request->getRequestUri())[0], '/');
        
        // robots.txt Honeypot
        if ($settings->get('bot_robots') === '1' && $settings->get('bot_robots_link') === $path) {
            $response = $this->handleHoneypot($request, 'robots');
        }

        // Invisible Link Honeypot
        if ($settings->get('bot_inlink') === '1' && $settings->get('bot_inlink_link') === $path) {
            $response = $this->handleHoneypot($request, 'inlink');
        }

        // Handle other Responses
        if (!isset($response) || $response === null) {
            $response = $next($request);
        }

        // Do some Analytics Stuff
        if ($response->headers->get('X-Synder-Analytics') === null) {
            try {
                $this->perform($request, $response);
            } catch(Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
        
        // Return
        return $response;
    }

    /**
     * Handle robots.txt Calls
     * 
     * @param HttpRequest $request
     * @return \Illuminate\Http\Response|null
     */
    protected function handleRobotsTxt(HttpRequest $request)
    {
        if (Settings::get('bot_robots') === '0') {
            return null;
        }
        $path = explode('?', $request->getRequestUri())[0];

        if ($path !== '/robots.txt') {
            return null;
        } else {
            $content = Settings::instance()->generateRobotsTxt();
            return HttpResponse::create($content, 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Content-Length' => strlen($content)
            ]);
        }
    }

    /**
     * Handle Honeypot Calls
     * 
     * @param HttpRequest $request
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    protected function handleHoneypot(HttpRequest $request, string $type)
    {
        if (($user = $this->getUser($request, null)) === false) {
            return;
        }
        $user->addBotDetail($type . '_trap', true);
        $user->evaluate(false);
        $user->save();

        // Update Link
        if (Settings::get('bot_' . $type . '_relocate') === '1') {
            if (time() - Settings::get('bot_' . $type . '_time') > 90 * 24 * 60 * 60) {
                Settings::set('bot_' . $type . '_link', '0');
            }
        }

        // Return Redirect
        return HttpResponse::create('', 303, [
            'Location' => $request->getUriForPath('/'),
            'X-Synder-Analytics' => 1
        ]);
    }

    /**
     * Perform Action
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    public function perform(HttpRequest $request, $response)
    {

        // Get Page
        if (($page = $this->getPage($request, $response)) === false) {
            return;
        }

        // Get Visitor
        if (($user = $this->getUser($request, $response, $page)) === false) {
            return;
        }

        // Get View
        if (($view = $this->getView($request, $response, $page, $user)) === false) {
            return;
        }

        // Prepare Visitor
        if (empty($user->agent)) {
            $user->agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        }
        if (empty($user->agent_details)) {
            if (Settings::get('bot_lazy') === '0') {
                $user->evaluate();
            }
        }

        // New Status
        $is_new = !$page->id || !$user->id || !$view->id;

        // Update Page
        $page->views += 1;
        $page->visits += $is_new? 1: 0;
        $page->save();

        // Update User
        $user->bot = $is_new? 0.0: $user->bot;
        $user->views += 1;
        $user->visits += $is_new? 1: 0;
        $user->last_visit = $is_new? date('Y-m-d H:i:s'): $user->last_visit;
        $user->save();

        // Update View
        $view->analytics_id = $is_new? $page->id: $view->analytics_id;
        $view->visitor_id = $is_new? $user->id: $view->visitor_id;
        $view->request = $request->headers->all();
        $view->referrer = $_SERVER['HTTP_REFERER'] ?? $view->referer ?? null;
        $view->response = $response->headers->all();
        $view->response_status = $response->getStatusCode();
        $view->views += 1;
        $view->save();

        // Handle Referrers
        $referrer = filter_var($_SERVER['HTTP_REFERER'] ?? '', \FILTER_VALIDATE_URL);
        if ($referrer && ($referrer = filter_var($referrer, \FILTER_SANITIZE_URL)) !== false) {
            $refhost = str_replace('www.', '', parse_url($referrer, \PHP_URL_HOST) ?? '');
            $ownhost = str_replace('www.', '', parse_url(url('/'), \PHP_URL_HOST) ?? '');
            if (strtolower($refhost) === strtolower($ownhost)) {
                return;
            }

            // Store Referrer
            $item = Referrer::firstOrNew([
                'hash'      => sha1(strtolower($referrer))
            ], [
                'host'      => $refhost,
                'url'       => $referrer,
                'views'     => 0
            ]);
            $item->views += 1;
            $item->save();
        }
    }

    /**
     * Get Page based on current request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return \Synder\Analytics\Models\Page|false
     */
    public function getPage(HttpRequest $request, $response)
    {
        $method = strtoupper($request->getRealMethod());
        $handle = strtolower($request->getPathInfo());

        // Skip irrelevant methods
        if (in_array($method, ['HEAD', 'OPTIONS', 'CONNECT', 'TRACE'])) {
            return false;
        }

        // Hide URL 
        $hide = false;
        $autoHide = [
            'favicon.ico',
            'robots.txt',
            'humans.txt',
            'sitemaps.xml',
            'sitemap.xml.gz',
            'sitemap.xml',
            'sitemap.txt',
            'sitemap_index.txt',
            'atom.xml',
            'rss.xml'
        ];
        foreach ($autoHide AS $path) {
            if (Str::endsWith($handle, $path)) {
                $hide = true;
                break;
            }
        }

        // Return Page
        return Page::firstOrNew([
            'hash'      => sha1($method . ' ' . $handle),
        ], [
            'method'    => $method,
            'path'      => $handle,
            'hide'      => $hide,
            'views'     => 0,
            'visits'    => 0
        ]);
    }

    /**
     * Get current User
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response|null $response
     * @param \Synder\Analytics\Models\Page|null $page
     * @return \Synder\Analytics\Models\Visitor|false
     */
    public function getUser(HttpRequest $request, $response = null, $page = null)
    {
        // Skip logged in backend users
        if (Settings::get('filter_backend_users') && BackendAuth::check()) {
            return false;
        }

        // Return User
        return Visitor::firstOrNew([
            'hash'      => Visitor::generateHash()
        ], [
            'bot'       => 0.0,
            'agent'     => null,
            'views'     => 0,
            'visits'    => 0,
            'last_visit'=> date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get current View
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @param \Synder\Analytics\Models\Page $page
     * @param \Synder\Analytics\Models\Visitor $user
     * @return \Synder\Analytics\Models\Request|false
     */
    public function getView(HttpRequest $request, $response, $page, $user)
    {
        $view = $page->requests()->where([
            ['analytics_id', '=', $page->id],
            ['visitor_id', '=', $user->id]
        ])->first();

        // Reset if the visit has been created on the previous day.
        if (!empty($view)) {
            $time = strtotime($view->created_at);
            $date = strtotime(explode(' ', $view->created_at)[0] . ' 00:00:00');

            if ($time < $date || $time > $date + 24*60*60) {
                $view = null;
            }
        }

        // Create new View
        if (empty($view)) {
            return new Request([
                'type'              => 'untracked',
                'order'             => 0,
                'views'             => 0,
                'request'           => [],
                'referrer'          => null,
                'response'          => $response->headers->all(),
                'response_status'   => $response->getStatusCode()
            ]);
        } else {
            return $view;
        }
    }
}
