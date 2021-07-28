<?php 

namespace Synder\Analytics\Middleware;

use BackendAuth;
use Closure;
use Exception;
use Log;
use DeviceDetector\DeviceDetector;
use Illuminate\Http\Request as HttpRequest;
use Synder\Analytics\Models\Page;
use Synder\Analytics\Models\Referrer;
use Synder\Analytics\Models\Request;
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
        $response = $next($request);

        try {
            $this->perform($request, $response);
        } catch(Exception $exception) {
            Log::error($exception->getMessage());
        }

        return $response;
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

        // Parse User Agent
        $is_bot = false;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if (!empty($user_agent)) {
            $dd = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
            $dd->parse();
            
            $is_bot = $dd->isBot();
            $user_agent = [
                'agent'     => $_SERVER['HTTP_USER_AGENT'],
                'client'    => $dd->getClient(),
                'os'        => $dd->getOs(),
                'device'    => $dd->getDeviceName(),
                'brand'     => $dd->getBrandName(),
                'model'     => $dd->getModel()
            ];
        }

        // New Status
        $is_new = !$page->id || !$user->id || !$view->id;

        // Update Page
        $page->views += 1;
        $page->visits += $is_new? 1: 0;
        $page->save();

        // Update User
        $user->bot = 0.0;
        $user->agent = $user_agent ?? $user->agent ?? null;
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
        if ($referrer && ($referrer = filter_var($referrer, \FILTER_SANITIZE_URL))) {
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

        // Return Page
        return Page::firstOrNew([
            'hash'      => sha1($method . ' ' . $handle),
        ], [
            'method'    => $method, 
            'path'      => $handle,
            'views'     => 0,
            'visits'    => 0
        ]);
    }

    /**
     * Get current User
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @param \Synder\Analytics\Models\Page $page 
     * @return \Synder\Analytics\Models\Visitor|false
     */
    public function getUser(HttpRequest $request, $response)
    {
        // Skip logged in backend users
        if (BackendAuth::check()) {
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
