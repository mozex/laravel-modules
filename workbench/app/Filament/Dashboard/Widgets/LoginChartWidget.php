<?php

declare(strict_types=1);

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\ChartWidget;

class LoginChartWidget extends ChartWidget
{
    protected function getType(): string
    {
        return 'line';
    }
}
