<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;
use Synder\Analytics\Models\Visitor;

class SimpleSystems extends ReportWidgetBase
{
    /**
     * Add Widget Assets
     * 
     * @return void
     */
    protected function loadAssets()
    {
        $this->addCss('../../assets/systems.css');
    }

    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        $date = date('Y-m-d', time() - 14 * 24 * 60 * 60) . ' 00:00:00';
        $data = Visitor::select('agent')->where('first_visit', '>=', $date)->get();

        $this->vars['counters'] = [0, 0];
        $this->vars['browserlist'] = [];
        $this->vars['oslist'] = [];

        foreach ($data AS $item) {
            if (empty($item->agent)) {
                continue;
            }

            if (!empty($item->agent['client'])) {
                $browser = $item->agent['client']['name'];
                if (!array_key_exists($browser, $this->vars['browserlist'])) {
                    $this->vars['browserlist'][$browser] = 0;
                }
                $this->vars['browserlist'][$browser]++;
                $this->vars['counters'][0]++;
            }

            if (!empty($item->agent['os'])) {
                $os = $item->agent['os']['name'] . ' ' . $item->agent['os']['version'];
                if (!array_key_exists($os, $this->vars['oslist'])) {
                    $this->vars['oslist'][$os] = 0;
                }
                $this->vars['oslist'][$os]++;
                $this->vars['counters'][1]++;
            }
        }

        return $this->makePartial('$/synder/analytics/widgets/systems/_widget.htm');
    }
}
