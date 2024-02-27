<?php

namespace Modules\Second\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Search extends Component
{
    public function __construct(
        public string $name,
    ) {
        $this->name .= ' Component';
    }

    public function render(): View
    {
        return view('second::components.search');
    }
}
