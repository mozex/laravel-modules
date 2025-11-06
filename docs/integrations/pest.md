# Pest Integration

## Overview

Use Pest to run tests across your application and all Modules with a simple, readable setup. Pest uses your `phpunit.xml` under the hood, so keep the PHPUnit configuration from the [PHPUnit integration](./phpunit.md) and add a small `Pest.php` bootstrap to include module tests.

## Requirements

- Pest installed in your project (dev dependency)
- A `phpunit.xml` configured with a Modules test suite (see the [PHPUnit integration](./phpunit.md))

## Setup

1) Keep or create `phpunit.xml` with a Modules suite:
   - `<directory>./Modules/*/Tests</directory>`
2) Create or update `tests/Pest.php` to apply your base TestCase (and traits) to module tests.

### Example: `tests/Pest.php`

```php
<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Apply your base TestCase to app tests
uses(TestCase::class)->in('Feature', 'Unit');

// Apply TestCase (and optional traits) to ALL module tests
// (relative to tests/Pest.php → project-root/Modules/*/Tests/*/*)
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/*/*');
```

Notes
- The glob in `->in('../Modules/*/Tests/*/*')` is relative to `tests/Pest.php`. If your Pest bootstrap lives elsewhere, adjust the relative path accordingly (for example, from project root it would be `Modules/*/Tests`).
- You can add more `uses(...)` calls to target specific module folders if needed.

## Run Pest

From your project root (Windows cmd):

```bat
./vendor/bin/pest
```

Run only module tests (by testsuite):

```bat
./vendor/bin/pest -c phpunit.xml --testsuite Modules
```

Or filter by path:

```bat
./vendor/bin/pest Modules/Blog/Tests
```

## Troubleshooting

- “No tests found”: verify your `phpunit.xml` contains the `Modules/*/Tests` suite and your globs in `tests/Pest.php` point to actual test files.
- Wrong path base: remember `->in()` paths are relative to `tests/Pest.php`; adjust the `../` prefix if you move the file.
- Database state with RefreshDatabase: ensure module migrations are picked up (see the Migrations docs); Pest will reset between tests per Laravel’s testing traits.

## See also

- [PHPUnit Integration](./phpunit.md)
- [Migrations](../features/migrations.md)
- Pest docs: https://pestphp.com/docs/installation
- Laravel testing docs: https://laravel.com/docs/testing
