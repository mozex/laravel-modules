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
