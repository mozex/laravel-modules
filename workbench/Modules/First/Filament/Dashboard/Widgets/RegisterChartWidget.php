<?php

namespace Modules\First\Filament\Dashboard\Widgets;

use Filament\Widgets\ChartWidget;

class RegisterChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'line';
    }
}
