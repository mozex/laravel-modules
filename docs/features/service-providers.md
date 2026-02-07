# Service Providers

## Overview

Discovers and auto-registers Laravel service provider classes from modules during the application registration phase (`register()` lifecycle). No need to list them in `bootstrap/providers.php`.

## What gets discovered

- Non-abstract classes extending `Illuminate\Support\ServiceProvider`
- Located in directories matching configured patterns (default: `*/Providers`)

## Default configuration

```php
'service-providers' => [
    'active' => true,
    'patterns' => [
        '*/Providers',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Providers/
    ├── BlogServiceProvider.php         // discovered
    └── ViewServiceProvider.php         // discovered
```

## Usage

Create service providers extending `Illuminate\Support\ServiceProvider`. Use `register()` and `boot()` as you would in a normal app provider.

## Configuration

- Set `'service-providers.active' => false` to disable auto-registration.
- Edit `'service-providers.patterns'` to change discovery directories.

## Troubleshooting

- **Not registered**: ensure the class extends `Illuminate\Support\ServiceProvider` and lives under a discovered `Providers` directory.
- **Boot order**: control module load order via the `modules` config section (per-module `order` key).
