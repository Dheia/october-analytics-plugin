<?php 

namespace Synder\Analytics\Middleware;

use Closure;
use Exception;
use Log;
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
        $method = strtoupper($request->getRealMethod());
        $handle = strtolower($request->getPathInfo());
        $unique = Visitor::generateHash();

        // Handle Page
        // Each URL receives its own entry, mainly used for linking, indexing and undetailed storage
        // of views and visits which are bypassed in the template.
        $page = Page::firstOrCreate([
            'method'    => $method, 
            'path'      => $handle
        ], [
            'hash'      => sha1($method . ' ' . $handle),
            'views'     => 0,
            'visits'    => 0
        ]);

        // Handle Visitor
        // Each Visitor receives his own entry, including the last-seen user agent and the number 
        // of generated views and visits / day. The unique hash cannot be traced back to the user.
        $user = Visitor::firstOrCreate([
            'hash'      => $unique
        ], [
            'bot'       => 0.0,
            'agent'     => null,
            'views'     => 0,
            'visits'    => 0,
            'last_visit'=> date('Y-m-d H:i:s')
        ]);

        // Handle View
        // Each single unique visit / day receives its own entry, including the last-seen request 
        // referrer, response, status code and number of views generated on one visit.
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
            $view = new Request([
                'type'              => 'untracked',
                'order'             => 0,
                'views'             => 0,
                'request'           => [],
                'referrer'          => null,
                'response'          => $response->headers->all(),
                'response_status'   => $response->getStatusCode()
            ]);
            $view->analytics_id = $page->id;
            $view->visitor_id = $user->id;
            $view->save();
        }

        // New Status
        $isNew = $page->wasRecentlyCreated || $user->wasRecentlyCreated || $view->wasRecentlyCreated;

        // Update Page
        $page->views += 1;
        $page->visits += $isNew? 1: 0;
        $page->save();

        // Update User
        $user->agent = $_SERVER['HTTP_USER_AGENT'] ?? $user->agent ?? null;
        $user->views += 1;
        $user->visits += $isNew? 1: 0;
        $user->last_visit = $isNew? date('Y-m-d H:i:s'): $user->last_visit;
        $user->save();

        // Update View
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
}
