# Laravel Modules - Documentation

Feature-by-feature documentation for the `mozex/laravel-modules` package.

## Index

- [Requirements, installation, quick start: main README](../README.md)
- Features
  - [Blade Components](./features/blade-components.md) — class-based components
  - [Views](./features/views.md) — namespacing and anonymous components
  - [Routes](./features/routes.md) — groups, broadcasting, console routes
  - [Configs](./features/configs.md) — merge strategy and priority
  - [Migrations](./features/migrations.md)
  - [Seeders](./features/seeders.md)
  - [Commands](./features/commands.md)
  - [Helpers](./features/helpers.md)
  - [Models & Factories](./features/models-factories.md)
  - [Policies](./features/policies.md)
  - [Events & Listeners](./features/events-listeners.md)
  - [Service Providers](./features/service-providers.md)
  - [Translations](./features/translations.md)
  - [Caching](./features/caching.md)
  - [Schedules](./features/schedules.md)
  - [Livewire Components](./features/livewire-components.md)
  - [Filament](./features/filament.md) — Resources, Pages, Widgets, Clusters
  - [Nova Resources](./features/nova-resources.md)
- Integrations
  - [PHPStan](./integrations/phpstan.md)
  - [PHPUnit](./integrations/phpunit.md)
  - [Pest](./integrations/pest.md)

## Conventions

- Modules root: `Modules/` (configure via `modules_directory` in `config/modules.php`)
- Module namespace: `Modules\\` (configure via `modules_namespace`)
- Per-feature toggles and patterns live in `config/modules.php` under each feature section

## Module activation & ordering

```php
'modules' => [
    'Shared' => ['active' => true, 'order' => 1],  // lower order = earlier load
    'Blog'   => ['active' => true, 'order' => 2],
    'Legacy' => ['active' => false],                // fully disabled
],
```

- **active**: `false` disables the module entirely (no asset discovery)
- **order**: lower values load first; default: `0`

## Modules Facade API

```php
use Mozex\Modules\Facades\Modules;

// Path helpers
Modules::basePath('path/to/file');
Modules::modulesPath('Blog/Config');

// Module name extraction
Modules::moduleNameFromNamespace('Modules\\Blog\\Models\\Post');  // 'Blog'
Modules::moduleNameFromPath('/var/www/Modules/Blog/file.php');    // 'Blog'

// Seeders
Modules::seeders();  // Array of {Module}DatabaseSeeder class names

// Route customization (call from service provider register())
Modules::routeGroup('admin', prefix: 'admin', middleware: ['web', 'auth'], as: 'admin::');
Modules::registerRoutesUsing('localized', fn ($attrs, $routes) => /* ... */);

// Inspect registered groups
Modules::getRouteGroups();
Modules::getRegisterRoutesUsing();

// Testing override
Modules::setBasePath('/custom/path');
```
