<?php

namespace Modules\First\Livewire;

use Filament\Pages\SimplePage;
use Illuminate\View\View;

abstract class BaseChained extends SimplePage
{
    public function render(): View
    {
        return view('first::livewire.chained', [
            'name' => 'Base Chained Livewire Component',
        ]);
    }
}
