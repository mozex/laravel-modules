# Service Providers

## Overview

Place Laravel service providers inside each module to bootstrap bindings, events, or any module-specific bootstrapping. The package discovers provider classes and registers them automatically during the application boot.

## What gets discovered

- Classes that extend `Illuminate\Support\ServiceProvider`
- Located in directories matching the configured patterns (default: `*/Providers` under each module)
- Non-provider classes are ignored

## Default configuration

In `config/modules.php`:

```php
'service-providers' => [
    'active' => true,
    'patterns' => [
        '*/Providers',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Providers/
    ├── BlogServiceProvider.php         // extends Illuminate\Support\ServiceProvider (discovered)
    └── ViewServiceProvider.php         // extends Illuminate\Support\ServiceProvider (discovered)

Modules/Shop/
└── Providers/
    └── ShopServiceProvider.php         // discovered
```

## Usage

- Create service providers per module and extend `Illuminate\Support\ServiceProvider`.
- Register singletons, bindings, observers, event subscribers, or publish resources in `register()`/`boot()` as you would in a normal app provider.
- Providers are auto-registered; no need to list them in `bootstrap/providers.php`.

## Configuration options

- Toggle discovery
  - Set `'service-providers.active' => false` to disable auto-registration.
- Change discovery patterns
  - Edit `'service-providers.patterns'` to add/remove directories, relative to each module root.

## Troubleshooting

- Not registered: ensure the class extends `Illuminate\Support\ServiceProvider` and lives under a discovered `Providers` directory.
- Boot order concerns: if you have inter-module dependencies, control module load order via the `modules` section (per-module `order`), and keep providers idempotent.
- Missing bindings: confirm the provider runs in the expected app lifecycle (check `register()` vs `boot()`), and verify autoloading (`composer dump-autoload`).

## See also

- [Configs](./configs.md)
- [Routes](./routes.md)
- [Events & Listeners](./events-listeners.md)

