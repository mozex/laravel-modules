# PHPUnit Integration

## Overview

Run your test suite across the core application and every Module by adding a Modules test suite to `phpunit.xml`.

## Recommended configuration

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

- `<directory>./Modules/*/Tests</directory>` discovers tests inside each module.
- `<source>` includes both `app` and `Modules` for coverage.

## Running tests

```bash
# All tests
./vendor/bin/phpunit

# Only module tests
./vendor/bin/phpunit --testsuite Modules
```

## See also

- [Pest Integration](./pest.md)
