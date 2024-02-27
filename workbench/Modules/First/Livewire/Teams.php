<?php

namespace Modules\First\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Teams extends Component
{
    public function render(): View
    {
        return view('first::livewire.teams', [
            'name' => 'Teams Livewire Component',
        ]);
    }
}
