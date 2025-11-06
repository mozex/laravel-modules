# Laravel Modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)
[![GitHub Tests Workflow Status](https://img.shields.io/github/actions/workflow/status/mozex/laravel-modules/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mozex/laravel-modules/actions/workflows/tests.yml)
[![License](https://img.shields.io/github/license/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)
[![Total Downloads](https://img.shields.io/packagist/dt/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)

Laravel Modules brings a clean, zero‑config modular structure to your Laravel app. Place your modules under the project root `Modules/` directory and this package will auto‑discover and wire their assets: configs, service providers, helpers, artisan commands, migrations, translations, views, Blade components, routes (web/api/console/broadcasting), schedules, listeners/events, Livewire, Filament, Nova, and more.

> Sensible conventions: namespaces, view/component aliases, and route groups are derived automatically.
> 
> Fine‑grained control: enable/disable modules, control load order, and override discovery patterns from `config/modules.php`.
> 
> Fast by default: built‑in caching makes discovery quick in all environments.


- [Support Us](#support-us)
- [Documentation](#documentation)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [Configuration overview](#configuration-overview)
- [Features](#features)
- [Caching](#caching)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

## Support us

Creating and maintaining open-source projects requires significant time and effort. Your support will help enhance the project and enable further contributions to the Laravel community.

Sponsorship can be made through the [GitHub Sponsors](https://github.com/sponsors/mozex) program. Just click the "**[Sponsor](https://github.com/sponsors/mozex)**" button at the top of this repository. Any amount is greatly appreciated, even a contribution as small as $1 can make a big difference and will go directly towards developing and improving this package.

Thank you for considering sponsoring. Your support truly makes a difference!

## Documentation

Detailed documentation, including examples and best practices, lives in the `docs` directory.

### Index

- Start here: [docs/README.md](./docs/README.md)

### Feature guides

- [Blade Components: class-based components](./docs/features/blade-components.md)
- [Views: namespacing and anonymous components](./docs/features/views.md)
- [Routes: groups, broadcasting, console routes](./docs/features/routes.md)
- [Configs: merge strategy and priority](./docs/features/configs.md)
- [Migrations](./docs/features/migrations.md)
- [Seeders](./docs/features/seeders.md)
- [Commands](./docs/features/commands.md)
- [Helpers](./docs/features/helpers.md)
- [Models & Factories](./docs/features/models-factories.md)
- [Policies](./docs/features/policies.md)
- [Events & Listeners](./docs/features/events-listeners.md)
- [Schedules](./docs/features/schedules.md)
- [Livewire Components](./docs/features/livewire-components.md)
- [Filament Resources/Pages/Widgets/Clusters](./docs/features/filament.md)
- [Nova Resources](./docs/features/nova-resources.md)

## Requirements

- PHP: ^8.1
- Laravel: ^10.34.2 | ^11.29.0 | ^12.0

## Installation

Install via Composer:

```bash
composer require mozex/laravel-modules
```

Publish the config file (optional, only if you want to tweak defaults):

```bash
php artisan vendor:publish --tag=laravel-modules-config
```

This will publish `config/modules.php`.

## Quick start

By default, modules live under the project root `Modules/` directory. Each module contains a conventional structure, for example:

```
project-root/
├── app/
├── bootstrap/
├── config/
├── database/
├── Modules/
│   ├── Blog/
│   │   ├── Config/
│   │   ├── Console/
│   │   ├── Database/
│   │   │   ├── Factories/
│   │   │   ├── Migrations/
│   │   │   └── Seeders/
│   │   ├── Filament/
│   │   ├── Lang/
│   │   ├── Listeners/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Providers/
│   │   ├── Resources/
│   │   │   └── views/
│   │   ├── Routes/
│   │   └── View/
│   │       └── Components/
│   └── Shop/
│       └── ...
└── vendor/
```

Out of the box, the package will automatically discover and register the following assets inside your modules: configs, service providers, helpers, artisan commands, migrations, translations, views, Blade components, models, factories, policies, routes, schedules, listeners/events, Livewire, Filament, and Nova resources.

Use your assets via namespaced conventions:

- Views: `view('blog::post.show')` maps to `Modules/Blog/Resources/views/post/show.blade.php`
- Class-based Blade components: `<x-blog::post.card/>` for `Modules/Blog/View/Components/Post/Card.php`
- Anonymous Blade components (from views): `<x-blog::button.primary/>` for `Modules/Blog/Resources/views/components/button/primary.blade.php`
- Routes: drop files under `Modules/*/Routes/*.php` and they will be loaded under sensible groups (web, api). See the Routes feature docs for customization.

Note for contributors: the test workbench lives in `workbench/` and follows the same conventions (`workbench/Modules/*`).

## Configuration overview

All options live in `config/modules.php`.

- modules_directory: default `Modules` (relative to the project base path). Change where your modules are stored.
- modules_namespace: default `Modules\\`. Change the PSR-4 base namespace for your modules.
- modules: per-module activation and ordering. Example:
  ```php
  'modules' => [
      'Shared' => [
          'active' => true,
          'order' => 1, // lower loads earlier
      ],
  ],
  ```
- Per-asset sections: each feature can be enabled/disabled and configured with glob patterns and other options. For example, Blade Components:
  ```php
  'blade-components' => [
      'active' => true,
      'patterns' => [
          '*/View/Components',
      ],
  ],
  ```
- Configs merging priority: when `'configs.priority' => true`, values from your modules override the app config; when false, app config wins and modules provide defaults.

You can disable any feature by setting `'active' => false` in its section.

## Features

This package discovers and wires many module assets. We’ll document them in depth, one by one. Suggested reading order:

1) Blade Components (class-based)
2) Views (namespaces, anonymous components)
3) Routes (groups, api/web, broadcasting, commands)
4) Configs (merging strategy, priority)
5) Migrations
6) Seeders
7) Commands
8) Helpers
9) Models & Factories
10) Policies
11) Events & Listeners
12) Schedules
13) Livewire Components
14) Filament (Resources, Pages, Widgets, Clusters)
15) Nova Resources

## Caching

This package supports a single discovery cache that speeds up scanning your Modules directory. Use these commands:

- Build the discovery cache:
  ```bash
  php artisan modules:cache
  ```
- Clear the discovery cache:
  ```bash
  php artisan modules:clear
  ```

Tip: after adding, renaming, or moving module assets, clear any relevant Laravel caches and rebuild the modules discovery cache as needed.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mozex](https://github.com/mozex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
