<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Classes\DateTime;
use Synder\Analytics\Models\Page;
use Synder\Analytics\Models\Settings;


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
                'title' => 'synder.analytics::lang.widgets.pages.timeperiod',
                'type' => 'dropdown',
                'default' => '14days',
                'options' => [
                    'all' => trans('synder.analytics::lang.shared.time_all'),
                    '7days' => trans('synder.analytics::lang.shared.time_7days'),
                    '14days' => trans('synder.analytics::lang.shared.time_14days'),
                    '31days' => trans('synder.analytics::lang.shared.time_31days'),
                    'week' => trans('synder.analytics::lang.shared.time_week'),
                    'month' => trans('synder.analytics::lang.shared.time_month')
                ],
                'showSearch' => false
            ],
            'amount' => [
                'title' => 'synder.analytics::lang.widgets.pages.amount',
                'type' => 'string',
                'default' => '5',
                'placeholder' => '5',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'synder.analytics::lang.widgets.pages.amount_error'
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
        $period = $this->property('timeperiod', '14days');
        if ($period === 'all') {
            $date = date('Y-m-d', 0);
        } else if ($period === '7days') {
            $date = date('Y-m-d', time() - 7 * 24 * 60 * 60) . ' 00:00:00';
        } else if ($period === '14days') {
            $date = date('Y-m-d', time() - 14 * 24 * 60 * 60) . ' 00:00:00';
        } else if ($period === '31days') {
            $date = date('Y-m-d', time() - 31 * 24 * 60 * 60) . ' 00:00:00';
        } else if ($period === 'week') {
            $date = (new DateTime())->getCurrentWeek('Y-m-d')['start'] . ' 00:00:00';
        } else if ($period === 'month') {
            $date = (new DateTime())->getCurrentMonth('Y-m-d')['start'] . ' 00:00:00';
        }

        $format = Settings::instance()->getDateTimeDefinition();
        $limit = intval($this->property('amount', 5));
        $pages = Page::selectRaw('method, path, views, visits, updated_at AS last_viewed')
            ->where('created_at', '>=', $date)
            ->where('hide', '=', 0)
            ->orderByDesc('visits')
            ->orderByDesc('views')
            ->orderByDesc('updated_at')
            ->limit(is_int($limit)? $limit: 5)
            ->get()
            ->map(function($item) use ($format) {
                $item['last_viewed'] = date($format, strtotime($item['last_viewed']));
                return $item;
            })
            ->toArray();
        
        $this->vars['pages'] = $pages ?? [];
        return $this->makePartial('$/synder/analytics/widgets/pages/_widget.htm');
    }
}
