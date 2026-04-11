# Laravel Modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)
[![GitHub Tests Workflow Status](https://img.shields.io/github/actions/workflow/status/mozex/laravel-modules/tests.yml?branch=2.x&label=tests&style=flat-square)](https://github.com/mozex/laravel-modules/actions/workflows/tests.yml)
[![Docs](https://img.shields.io/badge/docs-mozex.dev-10B981?style=flat-square)](https://mozex.dev/docs/laravel-modules/v2)
[![License](https://img.shields.io/github/license/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)
[![Total Downloads](https://img.shields.io/packagist/dt/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)

A zero-config modular architecture package for Laravel. Create a `Modules/` directory, drop in your module folders, and the package auto-discovers and registers everything: configs, routes, views, Blade components, migrations, seeders, commands, service providers, helpers, translations, models, factories, policies, events, listeners, schedules, Livewire components, Filament resources, and Nova resources.

> **[Read the full documentation at mozex.dev](https://mozex.dev/docs/laravel-modules/v2)**: searchable docs, version requirements, detailed changelog, and more.

> **This is the 2.x branch**, which targets Livewire v3 and Filament v3/v4. For Livewire v4 or Filament v5 support, see the [latest version](https://github.com/mozex/laravel-modules).

## Table of Contents

- [Introduction](https://mozex.dev/docs/laravel-modules/v2)
- Features
  - [Blade Components](https://mozex.dev/docs/laravel-modules/v2/features/blade-components)
  - [Views](https://mozex.dev/docs/laravel-modules/v2/features/views)
  - [Routes](https://mozex.dev/docs/laravel-modules/v2/features/routes)
  - [Configs](https://mozex.dev/docs/laravel-modules/v2/features/configs)
  - [Migrations](https://mozex.dev/docs/laravel-modules/v2/features/migrations)
  - [Seeders](https://mozex.dev/docs/laravel-modules/v2/features/seeders)
  - [Commands](https://mozex.dev/docs/laravel-modules/v2/features/commands)
  - [Helpers](https://mozex.dev/docs/laravel-modules/v2/features/helpers)
  - [Models & Factories](https://mozex.dev/docs/laravel-modules/v2/features/models-factories)
  - [Policies](https://mozex.dev/docs/laravel-modules/v2/features/policies)
  - [Events & Listeners](https://mozex.dev/docs/laravel-modules/v2/features/events-listeners)
  - [Schedules](https://mozex.dev/docs/laravel-modules/v2/features/schedules)
  - [Service Providers](https://mozex.dev/docs/laravel-modules/v2/features/service-providers)
  - [Translations](https://mozex.dev/docs/laravel-modules/v2/features/translations)
  - [Caching](https://mozex.dev/docs/laravel-modules/v2/features/caching)
  - [Listing Modules](https://mozex.dev/docs/laravel-modules/v2/features/listing)
  - [Livewire Components](https://mozex.dev/docs/laravel-modules/v2/features/livewire-components)
  - [Filament](https://mozex.dev/docs/laravel-modules/v2/features/filament)
  - [Nova Resources](https://mozex.dev/docs/laravel-modules/v2/features/nova-resources)
- Integrations
  - [PHPStan](https://mozex.dev/docs/laravel-modules/v2/integrations/phpstan)
  - [PHPUnit](https://mozex.dev/docs/laravel-modules/v2/integrations/phpunit)
  - [Pest](https://mozex.dev/docs/laravel-modules/v2/integrations/pest)
  - [Inertia](https://mozex.dev/docs/laravel-modules/v2/integrations/inertia)

## Support This Project

I maintain this package along with [several other open-source PHP packages](https://mozex.dev/docs) used by thousands of developers every day.

If my packages save you time or help your business, consider [**sponsoring my work on GitHub Sponsors**](https://github.com/sponsors/mozex). Your support lets me keep these packages updated, respond to issues quickly, and ship new features.

Business sponsors get logo placement in package READMEs. [**See sponsorship tiers →**](https://github.com/sponsors/mozex)

## What You Get

**Convention over configuration.** Module directory names become view namespaces, Blade component prefixes, and translation keys. `Modules/Blog/` gives you `view('blog::home')`, `<x-blog::card />`, and `__('blog::messages.welcome')` with no setup.

**Everything auto-discovered.** Routes load with the right middleware groups based on filename (`web.php` gets `web` middleware, `api.php` gets `api` prefix and middleware). Service providers register themselves. Commands appear in Artisan. Migrations run with `php artisan migrate`. Factories and policies resolve from models automatically.

**Livewire v3 support.** Class-based Livewire components inside a module's `Livewire/` directory get registered with namespaced aliases like `<livewire:blog::editor />`.

**Filament v3 and v4 integration.** Resources, pages, widgets, and clusters register with panels based on directory structure. Put a resource in `Filament/Admin/Resources/` and it shows up in the `admin` panel.

**Per-module schedules.** Define a `Console/Kernel.php` class in a module and the package hands Laravel's scheduler to it at boot time. Keeps scheduling logic next to the code it runs.

**Fine-grained control when you need it.** Enable or disable individual modules, set load order, toggle specific features, and override discovery patterns. All from `config/modules.php`.

**Production-ready caching.** One command (`php artisan modules:cache`) caches all discovery results. No scanning on every request.

## Installation

> **Requires [PHP 8.2+](https://php.net/releases/)** - see [all version requirements](https://mozex.dev/docs/laravel-modules/v2/requirements)

```bash
composer require mozex/laravel-modules
```

Register the `Modules` namespace in your project's `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Modules\\": "Modules/"
        }
    }
}
```

Regenerate the autoloader:

```bash
composer dump-autoload
```

Optionally publish the config to change defaults:

```bash
php artisan vendor:publish --tag=laravel-modules-config
```

## Quick Start

Create a module with a route and a view:

```
Modules/
└── Blog/
    ├── Routes/
    │   └── web.php
    └── Resources/
        └── views/
            └── index.blade.php
```

The route file works like any Laravel route file:

```php
use Illuminate\Support\Facades\Route;

Route::get('/blog', fn () => view('blog::index'));
```

That's a working module. The route loads with the `web` middleware group (because the file is named `web.php`) and the view is accessible through the `blog::` namespace. Add more directories as you need them: `Models/`, `Livewire/`, `Filament/Admin/Resources/`, and so on. The package picks up each one based on the conventions documented in the feature guides.

For a complete module structure, configuration reference, facade API, and detailed feature documentation, visit the [documentation site](https://mozex.dev/docs/laravel-modules/v2).

## Resources

Visit the [documentation site](https://mozex.dev/docs/laravel-modules/v2) for searchable docs auto-updated from this repository.

- **[AI Integration](https://mozex.dev/docs/laravel-modules/v2/ai-integration)**: Use this package with AI coding assistants via Context7 and Laravel Boost
- **[Requirements](https://mozex.dev/docs/laravel-modules/v2/requirements)**: PHP, Laravel, and dependency versions
- **[Changelog](https://mozex.dev/docs/laravel-modules/v2/changelog)**: Release history with linked pull requests and diffs
- **[Contributing](https://mozex.dev/docs/laravel-modules/v2/contributing)**: Development setup, code quality, and PR guidelines
- **[Questions & Issues](https://mozex.dev/docs/laravel-modules/v2/questions-and-issues)**: Bug reports, feature requests, and help
- **[Security](mailto:hello@mozex.dev)**: Report vulnerabilities directly via email

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
