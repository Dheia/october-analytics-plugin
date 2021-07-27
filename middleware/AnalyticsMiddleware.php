<?php 

namespace Synder\Analytics\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Synder\Analytics\Models\Page;
use Synder\Analytics\Models\Referrer;
use Synder\Analytics\Models\View;
use Synder\Analytics\Models\Visitor;


class AnalyticsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $this->perform($request, $response);
        } catch(Exception $exception) {

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
    public function perform(Request $request, $response)
    {
        $method = strtoupper($request->getRealMethod());
        $handle = strtolower($request->getPathInfo());
        $unique = Visitor::generateHash();

        $newpage = false;
        $newuser = false;
        $newview = false;

        // Handle Page
        $page = Page::where('method', $method)->where('path', '=', $handle)->first();
        if ($page === null) {
            $page = Page::create([
                'hash'      => sha1($method . ' ' . $handle),
                'method'    => $method,
                'path'      => $handle,
                'views'     => 0,
                'unique'    => 0
            ]);
            $newpage = true;
        }

        // Handle Visitor
        $user = $page->visitors()->where('hash', '=', $unique)->first();
        if (empty($user)) {
            $user = new Visitor([
                'hash'      => $unique,
                'bot'       => 0.0,
                'agent'     => null,
                'visits'    => 0
            ]);
            $user->save();
            $newuser = true;
        }

        // Handle View
        $view = $page->views()->where([
            ['analytics_id', '=', $page->id],
            ['visitor_id', '=', $user->id]
        ])->first();
        if (empty($view)) {
            $view = new View([
                'type'              => 'untracked',
                'order'             => 0,
                'visits'            => 0,
                'request'           => [],
                'referer'           => null,
                'response'          => $response->headers->all(),
                'response_status'   => $response->getStatusCode()
            ]);
            $view->page()->associate($page);
            $view->visitor()->associate($user);
            $page->views()->save($view);
            $newview = true;
        }

        // Update Page
        $page->unique += ($newpage || $newuser || $newview)? 1: 0;
        $page->views += 1;
        $page->save();

        // Update User
        $user->agent = $_SERVER['HTTP_USER_AGENT'] ?? $user->agent ?? null;
        $user->visits += 1;
        $user->save();

        // Update View
        $view->request = $request->headers->all();
        $view->referer = $_SERVER['HTTP_REFERER'] ?? $view->referer ?? null;
        $view->response = $response->headers->all();
        $view->response_status = $response->getStatusCode();
        $view->visits += 1;
        $view->save();

        // Handle Referrers
        $referrer = filter_var($_SERVER['HTTP_REFERER'] ?? '', \FILTER_VALIDATE_URL);
        if ($referrer && ($referrer = filter_var($referrer, \FILTER_SANITIZE_URL))) {
            $hash = sha1(strtolower($referrer));
            $item = Referrer::where('hash', $hash)->first();
            if ($item === null) {
                $item = Referrer::create([
                    'hash'      => $hash,
                    'host'      => parse_url($referrer, \PHP_URL_HOST),
                    'url'       => $referrer,
                    'visits'    => 0
                ]);
            }

            $item->visits += 1;
            $item->save();
        }
    }
}
