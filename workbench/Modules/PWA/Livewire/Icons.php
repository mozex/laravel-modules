<?php

namespace Modules\PWA\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Icons extends Component
{
    public function render(): View
    {
        return view('pwa::livewire.icons', [
            'name' => 'PWA Icons Livewire Component',
        ]);
    }
}
