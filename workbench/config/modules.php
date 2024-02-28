<?php

use Mozex\Modules\Enums\AssetType;

return [
    'modules' => [
        'First' => [
            'active' => true,
            'order' => 2,
        ],
        'Second' => [
            'active' => true,
            'order' => 1,
        ],
    ],
    AssetType::BladeComponents->value => [
        'active' => true,
        'patterns' => [
            '*/View/Components',
            '*/Components',
        ],
    ],
    AssetType::LivewireComponents->value => [
        'active' => true,
        'patterns' => [
            '*/Livewire',
        ],
    ],
    AssetType::NovaResources->value => [
        'active' => true,
        'patterns' => [
            '*/Nova',
        ],
    ],
];
