<?php

namespace Modules\First\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public function __construct(
        public string $name,
    ) {
        $this->name .= ' Component';
    }

    public function render(): View
    {
        return view('first::components.select');
    }
}
