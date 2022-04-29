<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Classes\DateTime;
use Synder\Analytics\Models\Referrer;
use Synder\Analytics\Models\Settings;


class SimpleReferrers extends ReportWidgetBase
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
                'title' => 'synder.analytics::lang.widgets.referrers.timeperiod',
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
                'title' => 'synder.analytics::lang.widgets.referrers.amount',
                'type' => 'string',
                'default' => '5',
                'placeholder' => '5',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'synder.analytics::lang.widgets.referrers.amount_error'
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
        $referrers = Referrer::selectRaw('SUM(views) as views, COUNT(id) as urls, host, MAX(last_view) as last_view')
            ->where('last_view', '>=', $date)
            ->groupBy('host')
            ->orderByDesc('views')
            ->orderByDesc('urls')
            ->orderByDesc('last_view')
            ->limit(is_int($limit)? $limit: 5)
            ->get()
            ->map(function($item) use ($format) {
                $item['last_viewed'] = date($format, strtotime($item['last_view']));
                return $item;
            })
            ->toArray();

        $this->vars['referrers'] = $referrers ?? [];
        return $this->makePartial('$/synder/analytics/widgets/referrers/_widget.htm');
    }
}
