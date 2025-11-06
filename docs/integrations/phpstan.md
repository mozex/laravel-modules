# PHPStan Integration

## Overview

Run PHPStan across your Laravel app and all Modules using a single, dynamic configuration. By switching to a PHP config file (phpstan.php), you can glob your module directories at runtime so new modules are picked up automatically.

## Requirements

- PHPStan installed in your project (dev dependency)
- Optional: a baseline file (phpstan-baseline.php) if you want to suppress known issues

## Setup

1) Create `phpstan.php` at the project root (or replace your existing `phpstan.neon`/`phpstan.neon.dist`).
2) Paste the configuration below (adjust levels and paths as you prefer).

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
        'reportUnmatchedIgnoredErrors' => false,
        'ignoreErrors' => [
            ['identifier' => 'trait.unused'],
            ['identifier' => 'typeCoverage.paramTypeCoverage'],
        ],
    ],
];
```

Notes
- `paths`: analyses your core app/config and every module directory dynamically.
- `excludePaths.analyseAndScan`: skips module Tests, Database, and Resources directories (tune as needed).
- `databaseMigrationsPath`: points PHPStan/Laravel helpers at your module migration directories.
- The array spread (`...glob(...)`) requires PHP 8.1+.

## Run PHPStan

From your project root:

```bat
phpstan analyse -c phpstan.php
```

If you prefer the Composer-installed binary:

```bat
vendor\bin\phpstan analyse -c phpstan.php
```

## Baseline (optional)

Generate or update a baseline to suppress existing issues:

```bat
phpstan analyse -c phpstan.php --allow-empty-baseline --generate-baseline phpstan-baseline.php
```

## Troubleshooting

- “Path not analysed”: confirm the `paths` glob finds your module directories (run `php -r "print_r(glob(__DIR__ . '/Modules/*', GLOB_ONLYDIR));"`).
- “Too many findings from Resources/Database/Tests”: adjust `excludePaths.analyseAndScan` to suit your project.
- Windows path separators: use `__DIR__` and absolute paths as shown to avoid slash issues.

## See also

- PHPStan docs: https://phpstan.org/

