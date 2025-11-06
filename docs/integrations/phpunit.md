# PHPUnit Integration

## Overview

Run your test suite across the core application and every Module by adding a Modules test suite to `phpunit.xml` and including module code in the source include paths.

## Requirements

- PHPUnit installed in your project (typically via Composer)

## Setup

1) Create or update `phpunit.xml` at the project root.
2) Add a dedicated Modules test suite and include both `app` and `Modules` in the `<source>` section.

## Recommended configuration (phpunit.xml)

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

    <php>
        <!-- Your environment variables go here (DB_*, APP_ENV, etc.) -->
    </php>

    <source>
        <include>
            <directory>./app</directory>
            <directory>./Modules</directory>
        </include>
    </source>
</phpunit>
```

Notes
- `<directory>./Modules/*/Tests</directory>` discovers tests inside each module under `Modules/{Module}/Tests`.
- `<source>` includes both `app` and `Modules` code for coverage and analysis.

## Run PHPUnit

From your project root:

```bat
vendor\bin\phpunit -c phpunit.xml
```

If you have a global phpunit binary on PATH:

```bat
phpunit -c phpunit.xml
```

Filter to only module tests:

```bat
vendor\bin\phpunit -c phpunit.xml --testsuite Modules
```

## Troubleshooting

- “No tests found”: verify your module test paths match `Modules/*/Tests` and files/classes follow PHPUnit naming conventions.

## See also

- PHPUnit docs: https://phpunit.de/documentation.html
- Laravel testing docs: https://laravel.com/docs/testing

