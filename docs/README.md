# Laravel Modules - Documentation

This directory contains feature-by-feature documentation for the mozex/laravel-modules package.

## Index

- Getting Started
  - [Requirements, installation, quick start: see the main README](../README.md)
- Features
  - [Blade Components: class-based components](./features/blade-components.md)
  - [Views: namespacing and anonymous components](./features/views.md)
  - [Routes: groups, broadcasting, console routes](./features/routes.md)
  - [Configs: merge strategy and priority](./features/configs.md)
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
  - [Filament Resources/Pages/Widgets/Clusters](./features/filament.md)
  - [Nova Resources](./features/nova-resources.md)
- Integrations
  - [PHPStan](./integrations/phpstan.md)
  - [PHPUnit](./integrations/phpunit.md)
  - [Pest](./integrations/pest.md)

## Conventions

- Modules root: by default `Modules`. Configure via `config/modules.php` key `modules_directory`.
- Module namespace: by default `Modules\\`. Configure via `config/modules.php` key `modules_namespace`.
- Per-asset feature toggles and patterns live in `config/modules.php` under the corresponding section (e.g. `blade-components`, `views`, `routes`, ...).

## Module Activation & Ordering

Control which modules are active and their load order via the `modules` key:

```php
'modules' => [
    'Shared' => [
        'active' => true,   // default: true if omitted
        'order' => 1,       // lower values load earlier; default: 0
    ],
    'Blog' => [
        'active' => true,
        'order' => 2,
    ],
    'Legacy' => [
        'active' => false,  // completely disabled
    ],
],
```

- **active**: Set `false` to disable a module entirely (its assets won't be discovered).
- **order**: Controls load sequence; modules with lower order values are processed first. Useful when one module depends on another's service provider or config.

## Modules Facade API

The `Mozex\Modules\Facades\Modules` facade provides utility methods:

```php
use Mozex\Modules\Facades\Modules;

// Path helpers
Modules::basePath('path/to/file');        // Project base path + suffix
Modules::modulesPath('Blog/Config');      // Modules directory path + suffix

// Module name extraction
Modules::moduleNameFromNamespace('Modules\\Blog\\Models\\Post');  // 'Blog'
Modules::moduleNameFromPath('/var/www/Modules/Blog/Models/Post.php');  // 'Blog'

// Seeders
Modules::seeders();  // Array of discovered seeder class names

// Route customization (call from service provider register())
Modules::routeGroup('admin', prefix: 'admin', middleware: ['web', 'auth'], as: 'admin::');
Modules::registerRoutesUsing('localized', fn ($attrs, $routes) => /* ... */);

// Inspect registered groups
Modules::getRouteGroups();
Modules::getRegisterRoutesUsing();

// Testing (override base path for workbench/tests)
Modules::setBasePath('/custom/path');
```
