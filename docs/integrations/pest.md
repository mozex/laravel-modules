# Pest Integration

## Overview

Run tests across your application and all Modules using Pest. Pest uses `phpunit.xml` under the hood, so keep the PHPUnit configuration from the [PHPUnit integration](./phpunit.md) and add a `Pest.php` bootstrap to include module tests.

## Setup

1. Keep or create `phpunit.xml` with a Modules suite (see [PHPUnit integration](./phpunit.md)):
   `<directory>./Modules/*/Tests</directory>`
2. Create or update `tests/Pest.php`:

```php
<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class)->in('Feature', 'Unit');

// Apply TestCase to ALL module tests
// Path is relative to tests/Pest.php
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/*/*');
```

## Running tests

```bash
# All tests
./vendor/bin/pest

# Only module tests
./vendor/bin/pest --testsuite Modules

# Specific module
./vendor/bin/pest Modules/Blog/Tests
```

## Troubleshooting

- **No tests found**: verify `phpunit.xml` has the `Modules/*/Tests` suite and `Pest.php` globs point to actual test files.
- **Wrong path base**: `->in()` paths are relative to `tests/Pest.php` — adjust the `../` prefix if the file is moved.

## See also

- [PHPUnit Integration](./phpunit.md)
- [Migrations](../features/migrations.md)
