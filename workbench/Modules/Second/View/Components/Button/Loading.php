<?php

namespace Modules\Second\View\Components\Button;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Loading extends Component
{
    public function __construct(
        public string $name,
    ) {
        $this->name .= ' Component';
    }

    public function render(): View
    {
        return view('second::components.button.loading');
    }
}
