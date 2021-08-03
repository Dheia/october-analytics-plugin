<?php

namespace Synder\Analytics;

use Event;
use Cms\Classes\CmsController;
use Cms\Classes\Page as RequestPage;
use October\Rain\Exception\SystemException;
use Synder\Analytics\FormWidgets\Slider;
use System\Classes\PluginBase;
use System\Models\PluginVersion;

use Synder\Analytics\Middleware\AnalyticsMiddleware;
use Synder\Analytics\Models\Page;
use Synder\Analytics\Models\Settings;
use Synder\Analytics\Models\Visitor;
use Synder\Analytics\Widgets\SimplePages;
use Synder\Analytics\Widgets\SimpleReferrers;
use Synder\Analytics\Widgets\SimpleStatistics;
use Synder\Analytics\Widgets\SimpleSystems;


class Plugin extends PluginBase
{
    const VERSION = '1.1.2';

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
        $installedVersion = PluginVersion::where('code', 'Synder.Analytics')->first();
        if ($installedVersion === null || !version_compare($installedVersion->version, self::VERSION, '=')) {
            if (php_sapi_name() === 'cli') {
                return;
            }
            throw new SystemException('Please run "php artisan october:migrate" to install and update Synder.Analytics.');
        }

        // Check Internal Version
        $version = Settings::get('version', '1.0.2');
        if (version_compare($version, self::VERSION, "<")) {
            $this->upgrade($version);
        }

        // Re-Evaluate Visitors
        if (Settings::get('dev_reevaluate', 1) === 1) {
            $items = Visitor::all();
            foreach ($items AS $item) {
                $item->evaluate(true);
            }
            Settings::set('dev_reevaluate', 0);
        }

        // Extend Mohsin.Txt Plugin
        if (Settings::instance()->getRobotsTxtProvider() === 'mohsin.txt') {
            if (Settings::get('bot_robots') === '1') {
                \Mohsin\Txt\Models\Robot::extend(function($model) {
                    $model->bindEvent('model.afterFetch', function() use ($model) {
                        if ($model->agent === '*:synder') {
                            $model->agent = '*';
                        }
                    });
                });
            }
        }

        // Add Invisible Link
        if (Settings::get('bot_inlink') === '1') {
            Event::listen('cms.template.processTwigContent', function($object, &$dataHolder) {
                $data = '<div style="top:0;color:transparent;position:absolute;text-indent:-9999999px;"><a href="{{ "'.Settings::get('bot_inlink_link').'"|app }}">Synder System</a></div>';

                if (($offset = strpos($dataHolder->content, '</body>')) !== false) {
                    $dataHolder->content = substr($dataHolder->content, 0, $offset) . $data . substr($dataHolder->content, $offset);
                }
            });
        }

        // Extend CMS Controller
        CmsController::extend(function($controller) {
            $controller->middleware(AnalyticsMiddleware::class);
        });

        // Extend Page Model
        RequestPage::extend(fn($model) => $this->extendPageModel($model));

        // Extend Backend
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
     * Upgrade Plugin
     *
     * @return void
     */
    protected function upgrade($version)
    {
        if (version_compare($version, '1.1.0', '<')) {
            $instance = Settings::instance();
            $instance->initSettingsData();
            $instance->version = '1.1.0';
            $instance->save();

            $autoHide = [
                '/favicon.ico',
                '/robots.txt',
                '/humans.txt',
                '/sitemaps.xml',
                '/sitemap.xml.gz',
                '/sitemap.xml',
                '/sitemap.txt',
                '/sitemap_index.txt',
                '/atom.xml',
                '/rss.xml'
            ];
            foreach ($autoHide AS $hide) {
                $page = Page::where('path', '=', $hide)->first();
                if (!empty($page)) {
                    $page->hide = 1;
                    $page->save();
                }
            }
        }

        if (version_compare($version, '1.1.2', '<')) {
            Settings::set('version', '1.1.2');
        }
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

    /**
     * Register Scheduled Tasks
     * 
     * @return void
     */
    public function registerSchedule($schedule)
    {
        if (Settings::get('bot_robots') === '0' && Settings::get('bot_inlink') === '0') {
            return;
        }
        if (Settings::get('bot_robots_relocate_cron') === '0' && Settings::get('bot_inlink_relocate_cron') === '0') {
            return;
        }

        $schedule->call(function () {
            $instance = \Synder\Analytics\Models\Settings::instance();

            if (time() - $instance->get('bot_robots_time') > 90 * 24 * 60 * 60) {
                $instance->set('bot_robots_link', '0');
            }
            if (time() - $instance->get('bot_inlink_time') > 90 * 24 * 60 * 60) {
                $instance->set('bot_inlink_link', '0');
            }
        })->monthly();
    }
}
