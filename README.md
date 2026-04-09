# Laravel Modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)
[![GitHub Tests Workflow Status](https://img.shields.io/github/actions/workflow/status/mozex/laravel-modules/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mozex/laravel-modules/actions/workflows/tests.yml)
[![Docs](https://img.shields.io/badge/docs-mozex.dev-10B981?style=flat-square)](https://mozex.dev/docs/laravel-modules/v3)
[![License](https://img.shields.io/github/license/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)
[![Total Downloads](https://img.shields.io/packagist/dt/mozex/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/mozex/laravel-modules)

A zero-config modular architecture package for Laravel. Create a `Modules/` directory, drop in your module folders, and the package auto-discovers and registers everything: configs, routes, views, Blade components, migrations, seeders, commands, service providers, helpers, translations, models, factories, policies, events, listeners, Livewire components, Filament resources, and Nova resources.

> **[Read the full documentation at mozex.dev](https://mozex.dev/docs/laravel-modules/v3)**: searchable docs, version requirements, detailed changelog, and more.

## Table of Contents

- [Introduction](https://mozex.dev/docs/laravel-modules/v3)
- Features
  - [Blade Components](https://mozex.dev/docs/laravel-modules/v3/features/blade-components)
  - [Views](https://mozex.dev/docs/laravel-modules/v3/features/views)
  - [Routes](https://mozex.dev/docs/laravel-modules/v3/features/routes)
  - [Configs](https://mozex.dev/docs/laravel-modules/v3/features/configs)
  - [Migrations](https://mozex.dev/docs/laravel-modules/v3/features/migrations)
  - [Seeders](https://mozex.dev/docs/laravel-modules/v3/features/seeders)
  - [Commands](https://mozex.dev/docs/laravel-modules/v3/features/commands)
  - [Helpers](https://mozex.dev/docs/laravel-modules/v3/features/helpers)
  - [Models & Factories](https://mozex.dev/docs/laravel-modules/v3/features/models-factories)
  - [Policies](https://mozex.dev/docs/laravel-modules/v3/features/policies)
  - [Events & Listeners](https://mozex.dev/docs/laravel-modules/v3/features/events-listeners)
  - [Service Providers](https://mozex.dev/docs/laravel-modules/v3/features/service-providers)
  - [Translations](https://mozex.dev/docs/laravel-modules/v3/features/translations)
  - [Caching](https://mozex.dev/docs/laravel-modules/v3/features/caching)
  - [Listing Modules](https://mozex.dev/docs/laravel-modules/v3/features/listing)
  - [Livewire Components](https://mozex.dev/docs/laravel-modules/v3/features/livewire-components)
  - [Filament](https://mozex.dev/docs/laravel-modules/v3/features/filament)
  - [Nova Resources](https://mozex.dev/docs/laravel-modules/v3/features/nova-resources)
- Integrations
  - [PHPStan](https://mozex.dev/docs/laravel-modules/v3/integrations/phpstan)
  - [PHPUnit](https://mozex.dev/docs/laravel-modules/v3/integrations/phpunit)
  - [Pest](https://mozex.dev/docs/laravel-modules/v3/integrations/pest)

## Support This Project

I maintain this package along with [several other open-source PHP packages](https://mozex.dev/docs) used by thousands of developers every day.

If my packages save you time or help your business, consider [**sponsoring my work on GitHub Sponsors**](https://github.com/sponsors/mozex). Your support lets me keep these packages updated, respond to issues quickly, and ship new features.

Business sponsors get logo placement in package READMEs. [**See sponsorship tiers →**](https://github.com/sponsors/mozex)

## What You Get

**Convention over configuration.** Module directory names become view namespaces, Blade component prefixes, and translation keys. `Modules/Blog/` gives you `view('blog::home')`, `<x-blog::card />`, and `__('blog::messages.welcome')` with no setup.

**Everything auto-discovered.** Routes load with the right middleware groups based on filename (`web.php` gets `web` middleware, `api.php` gets `api` prefix and middleware). Service providers register themselves. Commands appear in Artisan. Migrations run with `php artisan migrate`. Factories and policies resolve from models automatically.

**Full Livewire v4 support.** Class-based components, single-file components (SFC), and multi-file components (MFC) all work with namespaced aliases like `<livewire:blog::editor />`.

**Filament v5 integration.** Resources, pages, widgets, and clusters register with panels based on directory structure. Put a resource in `Filament/Admin/Resources/` and it shows up in the `admin` panel.

**Fine-grained control when you need it.** Enable or disable individual modules, set load order, toggle specific features, and override discovery patterns. All from `config/modules.php`.

**Production-ready caching.** One command (`php artisan modules:cache`) caches all discovery results. No scanning on every request.

## Installation

> **Requires [PHP 8.3+](https://php.net/releases/)** - see [all version requirements](https://mozex.dev/docs/laravel-modules/v3/requirements)

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

For a complete module structure, configuration reference, facade API, and detailed feature documentation, visit the [documentation site](https://mozex.dev/docs/laravel-modules/v3).

## Resources

Visit the [documentation site](https://mozex.dev/docs/laravel-modules/v3) for searchable docs auto-updated from this repository.

- **[AI Integration](https://mozex.dev/docs/laravel-modules/v3/ai-integration)**: Use this package with AI coding assistants via Context7 and Laravel Boost
- **[Requirements](https://mozex.dev/docs/laravel-modules/v3/requirements)**: PHP, Laravel, and dependency versions
- **[Changelog](https://mozex.dev/docs/laravel-modules/v3/changelog)**: Release history with linked pull requests and diffs
- **[Contributing](https://mozex.dev/docs/laravel-modules/v3/contributing)**: Development setup, code quality, and PR guidelines
- **[Questions & Issues](https://mozex.dev/docs/laravel-modules/v3/questions-and-issues)**: Bug reports, feature requests, and help
- **[Security](mailto:hello@mozex.dev)**: Report vulnerabilities directly via email

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
