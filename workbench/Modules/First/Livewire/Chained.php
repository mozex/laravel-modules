<?php

namespace Modules\First\Livewire;

use Illuminate\View\View;
use Override;

class Chained extends BaseChained
{
    #[Override]
    public function render(): View
    {
        return view('first::livewire.chained', [
            'name' => 'Chained Livewire Component',
        ]);
    }
}
