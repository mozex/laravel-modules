<?php

use Livewire\Mechanisms\ComponentRegistry;
use Mozex\Modules\Scouts\LivewireComponentsScout;

it('can register livewire components', function () {
    $components = app(ComponentRegistry::class);

    LivewireComponentsScout::create()->collect()
        ->each(function (array $asset) use ($components) {
            expect($components->getName($asset['namespace']))->not->toBeNull();
        });
});
