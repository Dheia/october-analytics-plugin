<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Models\Settings;
use Synder\Analytics\Models\Visitor;


class SimpleSystems extends ReportWidgetBase
{
    /**
     * Register Widget Settings
     *
     * @return void
     */
    public function defineProperties()
    {
        return [
            'show_legend' => [
                'title' => 'synder.analytics::lang.widgets.systems.show_legend',
                'type' => 'checkbox',
                'default' => 'true'
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
        $this->addCss('../../assets/css/systems.css');
    }

    /**
     * Prepare Chart Options
     * 
     * @return string
     */
    public function prepareChartOptions($data)
    {
        $this->vars['hide_legend'] = $this->property('show_legend') === 0;
    }

    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        $date = date('Y-m-d', time() - 14 * 24 * 60 * 60) . ' 00:00:00';
        $data = Visitor::where('bot', '<', intval(Settings::get('bot_filter', 4.2)))
            ->where('first_visit', '>=', $date)
            ->get();

        $this->vars['counters'] = [0, 0];
        $this->vars['browserlist'] = [];
        $this->vars['oslist'] = [];

        foreach ($data AS $item) {
            if (empty($item->agent)) {
                continue;
            }
            if (($item->attributes['bot'] ?? 0.0) === 0.0) {
                if(($bot = $item->bot) >= intval(Settings::get('bot_filter', 4.2))) {
                     continue;
                }
            }

            if (!empty($item->browser)) {
                $browser = explode(' ', $item->browser);
                array_pop($browser);
                $browser = implode(' ', $browser);
            } else {
                $browser = 'Unknown Browser';
            }
            if (!array_key_exists($browser, $this->vars['browserlist'])) {
                $this->vars['browserlist'][$browser] = 0;
            }
            $this->vars['browserlist'][$browser]++;
            $this->vars['counters'][0]++;

            if (!empty($item->os)) {
                $os = $item->os;
            } else {
                $os = 'Unknown OS';
            }
            if (!array_key_exists($os, $this->vars['oslist'])) {
                $this->vars['oslist'][$os] = 0;
            }
            $this->vars['oslist'][$os]++;
            $this->vars['counters'][1]++;
        }

        $this->prepareChartOptions($data);
        return $this->makePartial('$/synder/analytics/widgets/systems/_widget.htm');
    }
}
