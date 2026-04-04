---
title: Introduction
weight: 0
---

Laravel Modules brings a clean, zero-config modular structure to your Laravel app. Drop your modules into the `Modules/` directory and the package auto-discovers and wires everything: configs, service providers, helpers, artisan commands, migrations, translations, views, Blade components, routes, schedules, listeners, Livewire components, Filament resources, Nova resources, and more.

## What you get

- **Auto-discovery**: namespaces, view aliases, and route groups are derived from your module's directory name. No registration boilerplate.
- **Fine-grained control**: enable or disable modules, set load order, and override discovery patterns from `config/modules.php`.
- **Built-in caching**: a single discovery cache keeps scanning fast in production.

## Installation

Install with Composer:

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

Then regenerate the autoloader:

```bash
composer dump-autoload
```

Publish the config file (optional, only if you want to change defaults):

```bash
php artisan vendor:publish --tag=laravel-modules-config
```

## Quick start

By default, modules live under `Modules/` at your project root. Each module follows a conventional directory structure:

```
Modules/
├── Blog/
│   ├── Config/
│   ├── Console/Commands/
│   ├── Database/
│   │   ├── Factories/
│   │   ├── Migrations/
│   │   └── Seeders/
│   ├── Filament/
│   ├── Lang/
│   ├── Listeners/
│   ├── Models/
│   ├── Policies/
│   ├── Providers/
│   ├── Resources/views/
│   ├── Routes/
│   └── View/Components/
└── Shop/
    └── ...
```

Use your assets through namespaced conventions:

- **Views**: `view('blog::post.show')` maps to `Modules/Blog/Resources/views/post/show.blade.php`
- **Class-based Blade components**: `<x-blog::post.card/>` maps to `Modules/Blog/View/Components/Post/Card.php`
- **Anonymous components**: `<x-blog::button.primary/>` maps to `Modules/Blog/Resources/views/components/button/primary.blade.php`
- **Routes**: files under `Modules/*/Routes/*.php` load automatically with sensible group defaults (web, api)

Browse the feature guides in the sidebar for detailed documentation on each capability.
