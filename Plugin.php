<?php 

namespace Synder\Analytics;

use Cms\Classes\CmsController;
use System\Classes\PluginBase;

use Synder\Analytics\Middleware\AnalyticsMiddleware;
use Synder\Analytics\Widgets\Statistics;


class Plugin extends PluginBase
{
    /**
     * Plugin dependencies
     * 
     * @var array
     */
    public $require = [];
    

    /**
     * Plugin Details
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'synder.analytics::lang.plugin.name',
            'description' => 'synder.analytics::lang.plugin.description',
            'author'      => 'Synder <october@synder.dev>',
            'homepage'    => 'https://octobercms.com/plugin/synder-analytics'
        ];
    }

    /**
     * Register Plugin
     *
     * @return void
     */
    public function register()
    {
        // Nothing to do here...
    }
    
    /**
     * Boot Plugin
     *
     * @return void
     */
    public function boot()
    {
        CmsController::extend(function($controller) {
            $controller->middleware(AnalyticsMiddleware::class);
        });
    }

    /**
     * Register Dashboard Widget
     *
     * @return array
     */
    public function registerReportWidgets()
    {
        return [
            Statistics::class => [
                'label'     => 'synder.analytics::lang.widget.label',
                'context'   => 'dashboard'
            ]
        ];
    }
}
