<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;
use Synder\Analytics\Models\Request;

class SimpleStatistics extends ReportWidgetBase
{
    /**
     * @inheritDoc
     */
    public function __construct($controller, $properties = [])
    {
        parent::__construct($controller, $properties);
    }

    /**
     * Add Widget Assets
     * 
     * @return void
     */
    protected function loadAssets()
    {
        $this->addCss('../../assets/statistics.css');
    }

    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        $views = [];
        $visits = [];
        $visitors = [];
        $firsttime = 0;
        $stats = Request::selectRaw('SUM(views) as views, COUNT(id) as visits, COUNT(DISTINCT(visitor_id)) as visitors, DATE(created_at) AS date')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->limit(7)
            ->get()
            ->map(function ($item, $key) use (&$firsttime, &$views, &$visits, &$visitors) {
                $item['timestamp'] = strtotime($item['date'] . ' 00:00:00');
                if ($firsttime === 0) {
                    $firsttime = $item['timestamp'];
                }

                array_unshift($views, '[' . ($item['timestamp']*1000) . ', ' . $item['views'] . ']');
                array_unshift($visits, '[' . ($item['timestamp']*1000) . ', ' . $item['visits'] . ']');
                array_unshift($visitors, '[' . ($item['timestamp']*1000) . ', ' . $item['visitors'] . ']');
                return $item;
            })
            ->toArray();
        
        if (count($views) < 7 || count($visits) < 7) {
            if ($firsttime === 0) {
                $firsttime = strtotime(date('Y-m-d'));
            }

            for ($i = 7 - count($views); $i > 0; $i--) {
                $firsttime = $firsttime - 24*60*60;

                array_unshift($views, '[' . ($firsttime*1000) . ', 0]');
                array_unshift($visits, '[' . ($firsttime*1000) . ', 0]');
                array_unshift($visitors, '[' . ($firsttime*1000) . ', 0]');
            }
        } 
        
        $this->vars['stats'] = $stats ?? [];
        $this->vars['views'] = implode(', ', $views);
        $this->vars['visits'] = implode(', ', $visits);
        $this->vars['visitors'] = implode(', ', $visitors);
        return $this->makePartial('$/synder/analytics/widgets/statistics/_widget.htm');
    }
}
