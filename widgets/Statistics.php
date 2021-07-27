<?php

namespace Synder\Analytics\Widgets;

use Backend\Classes\ReportWidgetBase;


class Statistics extends ReportWidgetBase
{
    /**
     * Render Widget
     *
     * @return mixed
     */
    public function render()
    {
        return $this->makePartial('$/synder/analytics/widgets/statistics/_widget.htm');
    }
}
