<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;


class SimpleReferrers extends ReportWidgetBase
{
    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        return $this->makePartial('$/synder/analytics/widgets/referrers/_widget.htm');
    }
}
