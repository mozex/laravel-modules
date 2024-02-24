<?php

use Mozex\Modules\Enums\AssetType;

return [
    'modules_directory' => 'Modules',
    'modules_namespace' => 'Modules\\',
    'assets' => [
        AssetType::Commands->value => [
            'active' => true,
            'patterns' => [
                '*/Console/Commands',
            ],
        ],
        AssetType::Migrations->value => [
            'active' => true,
            'patterns' => [
                '*/Database/Migrations',
            ],
        ],
        AssetType::Helpers->value => [
            'active' => true,
            'patterns' => [
                '*/Helpers/*.php',
                '*/Helpers.php',
            ],
        ],
        AssetType::ServiceProviders->value => [
            'active' => true,
            'patterns' => [
                '*/Providers',
            ],
        ],
        AssetType::Seeders->value => [
            'active' => true,
            'patterns' => [
                '*/Database/Seeders',
            ],
        ],
        AssetType::Translations->value => [
            'active' => true,
            'patterns' => [
                '*/Resources/lang',
            ],
        ],
        AssetType::Schedules->value => [
            'active' => true,
            'patterns' => [
                '*/Console',
            ],
        ],
        AssetType::Configs->value => [
            'active' => true,
            'patterns' => [
                '*/Config/*.php',
            ],
        ],
        AssetType::Views->value => [
            'active' => true,
            'patterns' => [
                '*/Resources/views',
            ],
        ],
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
                //                'localized' => [
                //                    'middlewares' => [
                //                        'web',
                //                        'localeSessionRedirect',
                //                        'localizationRedirect',
                //                    ],
                //                    'prefix' => LaravelLocalization::setLocale()
                //                ]
            ],
        ],
        AssetType::Models->value => [
            'namespace' => 'Models\\',
        ],
        AssetType::Factories->value => [
            'active' => true,
            'namespace' => 'Database\\Factories\\',
        ],
        AssetType::Policies->value => [
            'active' => true,
            'namespace' => 'Policies\\',
        ],
        AssetType::LivewireComponents->value => [
            'active' => false,
            'patterns' => [
                '*/Livewire',
            ],
        ],
        AssetType::NovaResources->value => [
            'active' => false,
            'patterns' => [
                '*/Nova',
            ],
        ],
    ],
    'modules' => [
        // 'Shared' => [
        //     'active' => true,
        //     'order' => 1, // Optional
        // ],
    ],
];
