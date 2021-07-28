<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Models\Page;


class SimplePages extends ReportWidgetBase
{
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
            ->limit(5)
            ->get()
            ->toArray();
        
        $this->vars['pages'] = $pages ?? [];
        return $this->makePartial('$/synder/analytics/widgets/pages/_widget.htm');
    }
}
