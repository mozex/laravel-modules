---
title: Service Providers
weight: 12
---

Module service providers are discovered and registered during the application's `register()` phase, before any `boot()` methods run. This means you don't need to list them in `bootstrap/providers.php` or any other registration file. Put a class that extends `ServiceProvider` in your module's `Providers/` directory and it's registered automatically.

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
    ├── BlogServiceProvider.php
    ├── EventServiceProvider.php
    └── RouteServiceProvider.php
```

All non-abstract classes extending `Illuminate\Support\ServiceProvider` are discovered. Abstract base providers are ignored.

## Writing a module service provider

Module service providers work exactly like application service providers. Use `register()` for container bindings and `boot()` for anything that needs the application to be fully bootstrapped:

```php
namespace Modules\Blog\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Blog\Services\PostRepository;
use Modules\Blog\Contracts\PostRepositoryInterface;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }

    public function boot(): void
    {
        // Additional boot logic specific to this module
    }
}
```

## When to use module service providers

The package already handles discovery and registration for configs, routes, views, commands, and most other asset types. You don't need a service provider just to load those.

Module service providers are useful for:

- **Container bindings**: binding interfaces to implementations, registering singletons
- **Event subscribers**: registering subscriber classes via `Event::subscribe()`. Subscribers (classes with a `subscribe()` method that wires multiple event-listener mappings at once) aren't auto-discovered by Laravel or this package, so they need a manual registration call. This is different from the [Events & Listeners](./events-listeners) feature, which covers listener classes with typed `handle()` methods.
- **Macro definitions**: adding macros to Laravel classes
- **Third-party integrations**: configuring SDKs or external services specific to the module
- **Custom boot logic**: anything that needs to run during the boot phase that isn't covered by other features

## Registration order

Service providers from different modules register in module load order. If `Shared` has `order: 1` and `Blog` has `order: 2`, Shared's providers register first. Within a single module, the order depends on the discovery scanner.

This matters when one module's provider depends on bindings from another module. Use the `modules` config to set explicit `order` values for modules with inter-dependencies.

## Disabling

Set `'service-providers.active' => false` to disable auto-registration. You can still register module service providers manually in `bootstrap/providers.php` if needed.
