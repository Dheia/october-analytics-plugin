<?php 

namespace Synder\Analytics;

use Cms\Classes\CmsController;
use System\Classes\PluginBase;

use Synder\Analytics\Middleware\AnalyticsMiddleware;
use Synder\Analytics\Widgets\SimpleReferrers;
use Synder\Analytics\Widgets\SimpleStatistics;

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

        //
        //  Simple Statistics
        //      -> Show Visits of last 30 days
        //      -> Show Unique of last 30 days 
        //
        //  Simple Referrer List
        //      -> Show List of most used referrers
        //
        //  Simple Hot Pages
        //      -> Show List of most visited links
        //  

        return [
            SimpleStatistics::class => [
                'label'     => 'synder.analytics::lang.widgets.statistics.label',
                'context'   => 'dashboard'
            ],
            SimpleReferrers::class => [
                'label'     => 'synder.analytics::lang.widgets.referrers.label',
                'context'   => 'dashboard'
            ]
        ];
    }
}
