<?php

namespace Synder\Analytics\Middleware;

use Closure;
use Illuminate\Http\Request as HttpRequest;
use System\Classes\PluginManager;

///@todo
class BotsMiddleware {

    /**
     * Generate URL
     * 
     * @return string
     */

    /**
     * Generate RobotsTXT Entry
     *
     * @return string
     */
    static protected function generateRobots()
    {
        $value = "\n\n";
        $value .= '#[synder time='.time().']' . "\n";
        $value .= 'User-Agent: *' . "\n";
        $value .= 'Disallow: /12345' . "\n";
        $value .= '#[/synder]' . "\n\n";
        return $value;
    }

    /**
     * Update RobotsTXT File
     *
     * @return void
     */
    static public function updateRobots($remove = false)
    {
        if (PluginManager::instance()->exists('zen.robots')) {
            $content = \Zen\Robots\Models\Settings::get('content');
            $update = function($newContent) {
                \Zen\Robots\Models\Settings::set('content', $newContent);
            };
        }

        // Update
        [$start, $end] = [strpos($content, '#[synder'), strpos($content, '#[/synder]')];
        if ($start !== false) {
            $content = substr($content, 0, $start) . substr($content, $end + 12);
        }
        $content = trim($content) . self::generateRobots();

        // Store
        $update($content);
    }


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(HttpRequest $request, Closure $next)
    {
        return $next($request);
    }
}
