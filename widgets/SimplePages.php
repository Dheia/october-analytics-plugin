<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Models\Page;


class SimplePages extends ReportWidgetBase
{
    /**
     * Register Widget Settings
     *
     * @return void
     */
    public function defineProperties()
    {
        return [
            'timeperiod' => [
                'title' => 'synder.analytics::lang.widgets.pages.config.timeperiod',
                'type' => 'dropdown',
                'default' => 'all',
                'options' => [
                    'all' => trans('synder.analytics::lang.shared.time_all'),
                    '7days' => trans('synder.analytics::lang.shared.time_7days'),
                    '14days' => trans('synder.analytics::lang.shared.time_14days'),
                    '31days' => trans('synder.analytics::lang.shared.time_31days'),
                    'week' => trans('synder.analytics::lang.shared.time_week'),
                    'month' => trans('synder.analytics::lang.shared.time_month')
                ],
                'showSearch' => false
            ]
        ];
    }


    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        $date = date('Y-m-d', time() - 14 * 24 * 60 * 60) . ' 00:00:00';
        $pages = Page::selectRaw('method, path, views, visits, updated_at AS last_viewed')
            ->where('created_at', '>=', $date)
            ->orderByDesc('visits')
            ->orderByDesc('views')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->toArray();
        
        $this->vars['pages'] = $pages ?? [];
        return $this->makePartial('$/synder/analytics/widgets/pages/_widget.htm');
    }
}
