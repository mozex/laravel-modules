# PHPStan Integration

## Overview

Run PHPStan across your Laravel app and all Modules using a PHP config file that globs module directories at runtime.

## Recommended configuration (phpstan.php)

```php
<?php

return [
    'includes' => [
        './phpstan-baseline.php',
    ],
    'parameters' => [
        'level' => 5,
        'paths' => [
            __DIR__ . '/app',
            __DIR__ . '/config',
            ...glob(__DIR__ . '/Modules/*', GLOB_ONLYDIR),
        ],
        'excludePaths' => [
            'analyseAndScan' => [
                __DIR__ . '/Modules/*/Tests/*',
                __DIR__ . '/Modules/*/Database/*',
                __DIR__ . '/Modules/*/Resources/*',
            ],
        ],
        'databaseMigrationsPath' => glob('Modules/*/Database/Migrations', GLOB_ONLYDIR),
        'tmpDir' => 'storage/phpstan',
        'checkOctaneCompatibility' => true,
        'checkModelProperties' => true,
    ],
];
```

Key points:
- `...glob(...)` dynamically includes all module directories (requires PHP 8.1+).
- `excludePaths` skips Tests, Database, and Resources directories.
- `databaseMigrationsPath` points PHPStan at module migration directories.

## Running PHPStan

```bash
./vendor/bin/phpstan analyse -c phpstan.php
```

## Baseline

Generate a baseline to suppress existing issues:

```bash
./vendor/bin/phpstan analyse -c phpstan.php --allow-empty-baseline --generate-baseline phpstan-baseline.php
```
