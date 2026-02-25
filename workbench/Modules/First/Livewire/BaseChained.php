<?php

namespace Modules\First\Livewire;

use Filament\Pages\SimplePage;
use Illuminate\View\View;
use Override;

abstract class BaseChained extends SimplePage
{
    #[Override]
    public function render(): View
    {
        return view('first::livewire.chained', [
            'name' => 'Base Chained Livewire Component',
        ]);
    }
}
