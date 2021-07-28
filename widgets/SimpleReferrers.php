<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;

use Synder\Analytics\Models\Referrer;


class SimpleReferrers extends ReportWidgetBase
{
    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        $referrers = Referrer::selectRaw('SUM(views) as views, COUNT(id) as urls, host, MAX(last_view) as last_view')
                        ->groupBy('host')
                        ->orderByDesc('views')
                        ->orderByDesc('urls')
                        ->limit(5)
                        ->get()
                        ->toArray();
        $this->vars['referrers'] = $referrers ?? [];

        return $this->makePartial('$/synder/analytics/widgets/referrers/_widget.htm');
    }
}
