---
title: PHPUnit
weight: 2
---

To include module tests in your PHPUnit test suite, add a `Modules` test suite to `phpunit.xml` that points at each module's `Tests/` directory. The wildcard pattern `./Modules/*/Tests` picks up test directories from all modules automatically.

## Configuration

Add (or update) the `<testsuites>` and `<source>` sections in your `phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
    colors="true"
    cacheDirectory="storage/.phpunit.cache"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>./tests/Feature</directory>
        </testsuite>
        <testsuite name="Modules">
            <directory>./Modules/*/Tests</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>./app</directory>
            <directory>./Modules</directory>
        </include>
    </source>
</phpunit>
```

The `<source>` block tells PHPUnit to include `Modules/` when calculating code coverage, alongside `app/`.

## Running tests

```bash
# Run everything (app tests + module tests)
./vendor/bin/phpunit

# Run only module tests
./vendor/bin/phpunit --testsuite Modules

# Run tests for a specific module
./vendor/bin/phpunit Modules/Blog/Tests
```

## Module test structure

Tests inside modules follow the same conventions as application tests:

```
Modules/Blog/
└── Tests/
    ├── Feature/
    │   └── PostControllerTest.php
    └── Unit/
        └── PostServiceTest.php
```

Test classes extend your project's `TestCase` (or PHPUnit's `TestCase` for pure unit tests), just like they would in `tests/`.

## Custom modules directory

If you've changed `modules_directory`, update the `phpunit.xml` paths to match:

```xml
<directory>./src/Domains/*/Tests</directory>
```
