---
title: PHPStan
weight: 1
---

PHPStan needs to know about your module directories to analyse them. The trick is using a PHP config file (`phpstan.php`) instead of the NEON format, which lets you `glob()` module directories at runtime. New modules are picked up automatically without editing the config.

## Configuration

Create a `phpstan.php` file at your project root:

```php
<?php

return [
    'includes' => [
        './vendor/larastan/larastan/extension.neon',
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
                ...glob(__DIR__ . '/Modules/*/Tests', GLOB_ONLYDIR),
                ...glob(__DIR__ . '/Modules/*/Database', GLOB_ONLYDIR),
                ...glob(__DIR__ . '/Modules/*/Resources', GLOB_ONLYDIR),
            ],
        ],
        'databaseMigrationsPath' => glob('Modules/*/Database/Migrations', GLOB_ONLYDIR),
        'tmpDir' => 'storage/phpstan',
        'checkOctaneCompatibility' => true,
        'checkModelProperties' => true,
    ],
];
```

### What each section does

**`paths`**: Uses `...glob()` (the spread operator, PHP 8.1+) to include every module directory. When you add a new module, it's automatically included in the next analysis.

**`excludePaths`**: Skips directories that shouldn't be analysed: test files, database seeders/migrations/factories, and Blade views. These either have their own analysis tools or contain code that PHPStan can't usefully check.

**`databaseMigrationsPath`**: Tells Larastan where to find migration files. This is how PHPStan knows about your database schema when checking model property access. Without this, it can't verify `$post->title` against the actual columns.

## Running PHPStan

Since the config file isn't named `phpstan.neon`, you have to pass `-c phpstan.php` on every invocation. That gets tedious fast. Add a couple of composer scripts so you can forget about the flag entirely:

```json
{
    "scripts": {
        "test:types": "phpstan analyse -c phpstan.php --memory-limit=-1 --ansi",
        "baseline": "@test:types --allow-empty-baseline --generate-baseline phpstan-baseline.php"
    }
}
```

Now you run:

```bash
composer test:types
```

To analyse a single module, pass the path through the script:

```bash
composer test:types -- Modules/Blog
```

## Baselines

If you're adding PHPStan to a project with existing modules, generate a baseline to suppress current issues and work from a clean slate. With the composer scripts above in place:

```bash
composer baseline
```

This runs `test:types` with the baseline flags appended, writing all current errors to `phpstan-baseline.php`.

Once the file exists, register it in the `includes` array of your `phpstan.php` so PHPStan actually applies it on the next run:

```php
return [
    'includes' => [
        './vendor/larastan/larastan/extension.neon',
        './phpstan-baseline.php',
    ],
    // ...
];
```

Only add this line after `phpstan-baseline.php` has been generated. Including a file that doesn't exist yet causes PHPStan to fail at startup, which creates a chicken-and-egg problem (you can't run PHPStan to generate the baseline because PHPStan won't start without the baseline).

With the baseline in place, new code gets checked at the configured level while existing issues stay suppressed in the baseline file. Fix them gradually and regenerate the baseline with `composer baseline` whenever you want to shrink the suppressed set.

## Custom modules directory

If you've changed `modules_directory` in the package config, update the glob patterns to match:

```php
// If modules live in src/Domains/
...glob(__DIR__ . '/src/Domains/*', GLOB_ONLYDIR),
```
