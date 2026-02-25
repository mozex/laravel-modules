<?php

namespace Modules\First\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class EdgeCase extends Component
{
    public function render(): View
    {
        return view('first::edge-case', [
            'name' => 'Edge Case Livewire Component',
        ]);
    }
}
