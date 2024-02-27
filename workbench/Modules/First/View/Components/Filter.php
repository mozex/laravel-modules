<?php

namespace Modules\First\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Filter extends Component
{
    public function __construct(
        public string $name,
    ) {
        $this->name .= ' Component';
    }

    public function render(): View
    {
        return view('first::components.filter');
    }
}
