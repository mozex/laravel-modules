<?php

namespace Modules\Second\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ListUsers extends Component
{
    public function render(): View
    {
        return view('second::livewire.list-users', [
            'name' => 'List Users Livewire Component',
        ]);
    }
}
