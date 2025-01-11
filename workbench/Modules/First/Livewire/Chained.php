<?php

namespace Modules\First\Livewire;

use Illuminate\View\View;

class Chained extends BaseChained
{
    public function render(): View
    {
        return view('first::livewire.chained', [
            'name' => 'Chained Livewire Component',
        ]);
    }
}
