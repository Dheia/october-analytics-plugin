<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Classes\DateTime;
use Synder\Analytics\Models\Request;
use Synder\Analytics\Models\Settings;


class SimpleStatistics extends ReportWidgetBase
{
    /**
     * Register Widget Settings
     *
     * @return void
     */
    public function defineProperties()
    {
        return [
            'show_counts' => [
                'title' => 'synder.analytics::lang.widgets.statistics.show_counts',
                'type' => 'checkbox',
                'default' => 'false'
            ],
            'color_view' => [
                'title' => 'synder.analytics::lang.widgets.statistics.color_views',
                'type' => 'string',
                'default' => '#86CB43',
                'placeholder' => '#86CB43',
                'validationPattern' => '^\#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$',
                'validationMessage' => 'synder.analytics::lang.widgets.statistics.color_error'
            ],
            'color_visit' => [
                'title' => 'synder.analytics::lang.widgets.statistics.color_visits',
                'type' => 'string',
                'default' => '#008dc9',
                'placeholder' => '#008dc9',
                'validationPattern' => '^\#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$',
                'validationMessage' => 'synder.analytics::lang.widgets.statistics.color_error'
            ],
            'color_visitor' => [
                'title' => 'synder.analytics::lang.widgets.statistics.color_visitors',
                'type' => 'string',
                'default' => '#FF2D20',
                'placeholder' => '#FF2D20',
                'validationPattern' => '^\#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$',
                'validationMessage' => 'synder.analytics::lang.widgets.statistics.color_error'
            ]
        ];
    }

    /**
     * Add Widget Assets
     * 
     * @return void
     */
    protected function loadAssets()
    {
        $this->addCss('../../assets/css/statistics.css');
        $this->addJs('../../assets/js/statistics.js');
    }

    /**
     * Prepare Chart Options
     * 
     * @return string
     */
    public function prepareChartOptions($data)
    {
        $config = [];
        $config[] = "xaxis: {mode: 'time', minTickSize: [1, 'day'], timeformat: '%Y/%m/%d'}";
        $config[] = "yaxis: {min: 0, minTickSize: 1, tickDecimals: 0}";
        $config[] = "legend: { show: false }";

        if ($this->property('show_counts') === 1) {
            $this->vars['counts'] = [
                'views' => [array_sum(array_column($data, 'views')), current($data)['views']],
                'visits' => [array_sum(array_column($data, 'visits')), current($data)['visits']],
                'visitors' => [array_sum(array_column($data, 'visitors')), current($data)['visitors']]
            ];
        }

        $this->vars['config'] = implode(', ', $config);
        $this->vars['colors'] = [
            'view' => $this->property('color_view', '#86CB43'),
            'visit' => $this->property('color_visit', '#008dc9'),
            'visitor' => $this->property('color_visitor', '#FF2D20')
        ];
    }

    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        $stats = Request::selectRaw('
                SUM(synder_analytics_requests.views)                    AS  views, 
                COUNT(synder_analytics_requests.id)                     AS  visits, 
                COUNT(DISTINCT(synder_analytics_requests.visitor_id))   AS  visitors, 
                DATE(synder_analytics_requests.created_at)              AS  date
            ')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->join('synder_analytics_visitors', 'synder_analytics_visitors.id', '=', 'synder_analytics_requests.visitor_id')
            ->where('synder_analytics_visitors.bot', '<', intval(Settings::get('bot_filter', 4.2)))
            ->limit(7)
            ->get()
            ->mapWithKeys(fn ($item, $key) => [$item['date'] => $item])
            ->toArray();
        
        // Prepare Array
        $result = [];
        $views = [];
        $visits = [];
        $visitors = [];

        $datetime = new DateTime();
        foreach ($datetime->each('-P1D', 7, 'Y-m-d') AS $key) {
            if (array_key_exists($key, $stats)) {
                $item = $stats[$key];
            } else {
                $item = [
                    'views' => 0,
                    'visits' => 0,
                    'visitors' => 0,
                    'date' => $key
                ];
            }

            $timestamp = strtotime($key . ' 00:00:00');
            $result[$key] = $item;
            $views[] = '[' . ($timestamp * 1000) . ', ' . $item['views'] . ']';
            $visits[] = '[' . ($timestamp * 1000) . ', ' . $item['visits'] . ']';
            $visitors[] = '[' . ($timestamp * 1000) . ', ' . $item['visitors'] . ']';
        }
        
        // Set and Render
        $this->prepareChartOptions($result);
        $this->vars['stats'] = array_reverse($result) ?? [];
        $this->vars['views'] = implode(', ', array_reverse($views));
        $this->vars['visits'] = implode(', ', array_reverse($visits));
        $this->vars['visitors'] = implode(', ', array_reverse($visitors));
        return $this->makePartial('$/synder/analytics/widgets/statistics/_widget.htm');
    }
}
