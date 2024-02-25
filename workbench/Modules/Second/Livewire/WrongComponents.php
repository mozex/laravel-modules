<?php

namespace Modules\Second\Livewire;

use Illuminate\Contracts\View\View;

class WrongComponents
{
    public function render(): View
    {
        return view('second::livewire.list-users');
    }
}
