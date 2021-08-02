<?php

namespace Synder\Analytics;

use Backend;
use Event;
use Backend\Classes\NavigationManager;
use Cms\Classes\CmsController;
use Cms\Classes\Page as RequestPage;
use Synder\Analytics\FormWidgets\Slider;
use System\Classes\PluginBase;

use Synder\Analytics\Middleware\AnalyticsMiddleware;
use Synder\Analytics\Models\Page;
use Synder\Analytics\Models\Settings;
use Synder\Analytics\Widgets\SimplePages;
use Synder\Analytics\Widgets\SimpleReferrers;
use Synder\Analytics\Widgets\SimpleStatistics;
use Synder\Analytics\Widgets\SimpleSystems;


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
     * @todo
     *
     * @return void
     */
    public function boot()
    {
        CmsController::extend(function($controller) {
            $controller->middleware(AnalyticsMiddleware::class);
        });

        RequestPage::extend(fn($model) => $this->extendPageModel($model));

        //@todo
        //Event::listen('backend.menu.extendItems', function (NavigationManager $manager) {
        //    $manager->addSideMenuItem('October.Backend', 'dashboard', 'statistics', [
        //        'label'       => 'synder.analytics::lang.backend.title',
        //        'icon'        => 'icon-dashboard',
        //        'iconSvg'     => 'modules/backend/assets/images/dashboard-icon.svg',
        //        'url'         => Backend::url('backend'),
        //        'permissions' => ['backend.access_dashboard'],
        //        'order'       => 10
        //    ]);
        //});
    }

    /**
     * Extend Post Model
     *
     * @param \Cms\Classes\Page $model
     * @return void
     */
    protected function extendPageModel(RequestPage $model)
    {
        $model->bindEvent('model.afterFetch', function() use ($model) {
            $method = strtoupper(request()->getRealMethod());
            $handle = strtolower(request()->getPathInfo());

            $page = Page::where('hash', '=', sha1($method . ' ' . $handle))->first();
            if ($page) {
                $stats = [
                    'views' => $page->views,
                    'visits' => $page->visits
                ];
            } else {
                $stats = [
                    'views' => 1,
                    'visits' => 1
                ];
            }

            $model->addDynamicProperty('synderstats', $stats);
        });
    }

    /**
     * Register Form Widgets
     *
     * @return void
     */
    public function registerFormWidgets()
    {
        return [
            Slider::class => 'synder-slider'
        ];
    }
    
    /**
     * Register Plugin Settings
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'synder.analytics::lang.config._label',
                'description' => 'synder.analytics::lang.config._description',
                'category'    => 'system::lang.system.categories.misc',
                'icon'        => 'icon-bar-chart',
                'class'       => Settings::class,
                'order'       => 500,
                'keywords'    => 'analytics statistics traffic synder'
            ]
        ];
    }

    /**
     * Register Dashboard Widget
     *
     * @return array
     */
    public function registerReportWidgets()
    {
        return [
            SimpleStatistics::class => [
                'label'     => 'synder.analytics::lang.widgets.statistics.label',
                'context'   => 'dashboard'
            ],
            SimpleReferrers::class => [
                'label'     => 'synder.analytics::lang.widgets.referrers.label',
                'context'   => 'dashboard'
            ],
            SimpleSystems::class => [
                'label'     => 'synder.analytics::lang.widgets.systems.label',
                'context'   => 'dashboard'
            ],
            SimplePages::class => [
                'label'     => 'synder.analytics::lang.widgets.pages.label',
                'context'   => 'dashboard'
            ]
        ];
    }

    /**
     * Register Twig Markup
     * 
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'synderviews' => [Page::class, 'markupViews'],
                'syndervisits' => [Page::class, 'markupVisits'],
            ]
        ];
    }
}
