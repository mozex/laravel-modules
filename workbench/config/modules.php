<?php

use Mozex\Modules\Enums\AssetType;

return [
    AssetType::Routes->value => [
        'active' => true,
        'patterns' => [
            '*/Routes/*.php',
        ],
        'groups' => [
            'api' => [
                'prefix' => 'api',
                'middlewares' => ['api'],
            ],
            'web' => [
                'middlewares' => ['web'],
            ],
            'custom' => [
                'prefix' => 'custom',
                'as' => 'custom::',
                'middlewares' => ['web', 'api'],
            ],
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
];
