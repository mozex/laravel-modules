<?php

namespace Modules\First\Components;

use Illuminate\View\Component;

class WithoutView extends Component
{
    public function __construct(
        public string $name,
    ) {
        $this->name .= ' Component';
    }

    public function render(): string
    {
        return <<<'blade'
            {{ $name }}
        blade;
    }
}
