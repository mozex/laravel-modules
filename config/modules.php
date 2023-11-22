<?php

return [
    'migration_patterns' => [
        'Modules/*/Database/Migrations',
        [
            'pattern' => 'Modules/*/Database/Migrations/*',
            'flags' => GLOB_ONLYDIR,
        ],
    ],
    'seeder_patterns' => [
        'Modules/*/Database/Seeders/*DatabaseSeeder.php',
    ],
    'route_patterns' => [
        'Modules/*/Routes/*.php',
        'routes/Modules/*/*.php',
    ],
    'view_patterns' => [
        'Modules/*/Resources/views',
        'resources/Modules/*/views',
    ],
    'translation_patterns' => [
        'Modules/*/Resources/lang',
        'resources/Modules/*/lang',
        'lang/Modules/*/',
    ],
    'config_patterns' => [
        'Modules/*/Config/*.php',
        'config/Modules/*/*.php',
    ],
    'command_patterns' => [
        'Modules/*/Console/Commands/*.php',
    ],
    'nova_resource_patterns' => [
        'Modules/*/Nova/*.php',
    ],
    'livewire_component_patterns' => [
        'Modules/*/Livewire/*.php',
    ],
    'helper_patterns' => [
        'Modules/*/Helpers/*.php',
        'Modules/*/Helpers.php',
    ],
    'service_provider_patterns' => [
        'Modules/*/Providers/*.php',
    ],
    'kernel_patterns' => [
        'Modules/*/Console/Kernel.php',
    ],
    'api_middleware' => [
        'api',
    ],
    'default_middleware' => [
        'web',
    ],
    'route_groups' => [],
    'modules' => [],
];
