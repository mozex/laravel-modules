<?php

namespace Modules\First\Livewire\Nested;

use Illuminate\View\View;
use Livewire\Component;

class NestedUsers extends Component
{
    public function render(): View
    {
        return view('first::livewire.nested.nested-users', [
            'name' => 'Nested Users Livewire Component',
        ]);
    }
}
