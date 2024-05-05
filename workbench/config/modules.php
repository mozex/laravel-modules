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
    AssetType::Commands->value => [
        'active' => true,
        'patterns' => [
            '*/Console/Commands',
            '../app/Console/Commands',
        ],
    ],
];
