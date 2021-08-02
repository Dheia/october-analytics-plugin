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
        $data = Visitor::select(['agent', 'browser', 'os'])
            ->where('bot', '<', intval(Settings::get('bot_filter')))
            ->where('first_visit', '>=', $date)
            ->get();

        $this->vars['counters'] = [0, 0];
        $this->vars['browserlist'] = [];
        $this->vars['oslist'] = [];

        foreach ($data AS $item) {
            if (empty($item->agent)) {
                continue;
            }

            if (!empty($item->browser)) {
                $browser = explode(' ', $item->browser);
                array_pop($browser);
                $browser = implode(' ', $browser);
                
                if (!array_key_exists($browser, $this->vars['browserlist'])) {
                    $this->vars['browserlist'][$browser] = 0;
                }
                $this->vars['browserlist'][$browser]++;
                $this->vars['counters'][0]++;
            }

            if (!empty($item->os)) {
                if (!array_key_exists($item->os, $this->vars['oslist'])) {
                    $this->vars['oslist'][$item->os] = 0;
                }
                $this->vars['oslist'][$item->os]++;
                $this->vars['counters'][1]++;
            }
        }

        $this->prepareChartOptions($data);
        return $this->makePartial('$/synder/analytics/widgets/systems/_widget.htm');
    }
}
