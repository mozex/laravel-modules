<?php

namespace Modules\Second\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListUsers extends Component
{
    public function render(): View
    {
        return view('second::livewire.list-users');
    }
}
