---
title: Introduction
weight: 0
---

Laravel Modules gives your Laravel application a modular directory structure with zero configuration. Create a `Modules/` directory at your project root, drop in your module folders, and the package handles the rest: it discovers and registers configs, service providers, helpers, Artisan commands, migrations, seeders, translations, views, Blade components, routes, schedules, events, listeners, Livewire components, Filament resources, and Nova resources.

No boilerplate. No manual registration arrays. Just follow the conventions and your modules work.

This is the 2.x version of the package. It supports PHP 8.2+, Laravel 10/11/12/13, Livewire v3, and Filament v3/v4. If you need Livewire v4 or Filament v5, use the [3.x version](https://github.com/mozex/laravel-modules) instead.

## How it works

The package scans your `Modules/` directory for assets that follow Laravel's standard conventions. A `Blog` module with a `Routes/web.php` file gets its routes loaded with the `web` middleware group. A `View/Components/Card.php` class becomes `<x-blog::card />`. A `Lang/` directory registers translations under the `blog` namespace.

Every asset type uses the same pattern: the module's directory name becomes a kebab-case namespace prefix. `Blog` becomes `blog`, `UserAdmin` becomes `user-admin`, `PWA` becomes `pwa`. You don't configure this; it's derived from the directory name.

The discovery runs once per request (or once at cache time in production). Behind the scenes, the package uses Spatie's StructureDiscoverer for class scanning and glob patterns for file and directory matching. The results can be cached with a single Artisan command, making production boot times fast.

## Installation

Install the package via Composer:

```bash
composer require mozex/laravel-modules
```

Then register the `Modules` namespace in your project's `composer.json` so PHP can autoload module classes:

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

That's it. The package auto-registers its service provider through Laravel's package discovery. All features are enabled by default.

If you want to change any defaults (disable features, adjust discovery patterns, set module load order), publish the config file:

```bash
php artisan vendor:publish --tag=laravel-modules-config
```

This creates `config/modules.php` with every option documented inline.

## Module structure

Modules live under `Modules/` at your project root. Each module is a PascalCase directory that mirrors Laravel's own application structure:

```
project-root/
├── app/
├── Modules/
│   ├── Blog/
│   │   ├── Config/
│   │   │   └── blog.php
│   │   ├── Console/
│   │   │   ├── Commands/
│   │   │   │   └── PublishPosts.php
│   │   │   └── Kernel.php
│   │   ├── Database/
│   │   │   ├── Factories/
│   │   │   │   └── PostFactory.php
│   │   │   ├── Migrations/
│   │   │   │   └── 2024_01_10_create_posts_table.php
│   │   │   └── Seeders/
│   │   │       └── BlogDatabaseSeeder.php
│   │   ├── Events/
│   │   │   └── PostPublished.php
│   │   ├── Filament/
│   │   │   └── Admin/
│   │   │       └── Resources/
│   │   │           └── PostResource.php
│   │   ├── Helpers/
│   │   │   └── formatting.php
│   │   ├── Lang/
│   │   │   ├── en/
│   │   │   │   └── messages.php
│   │   │   └── fr.json
│   │   ├── Listeners/
│   │   │   └── NotifyFollowers.php
│   │   ├── Livewire/
│   │   │   └── PostEditor.php
│   │   ├── Models/
│   │   │   └── Post.php
│   │   ├── Nova/
│   │   │   └── Post.php
│   │   ├── Policies/
│   │   │   └── PostPolicy.php
│   │   ├── Providers/
│   │   │   └── BlogServiceProvider.php
│   │   ├── Resources/
│   │   │   └── views/
│   │   │       ├── home.blade.php
│   │   │       ├── components/
│   │   │       │   └── alert.blade.php
│   │   │       └── livewire/
│   │   │           └── post-editor.blade.php
│   │   ├── Routes/
│   │   │   ├── web.php
│   │   │   ├── api.php
│   │   │   └── console.php
│   │   └── View/
│   │       └── Components/
│   │           └── Card.php
│   └── Shop/
│       └── ...
└── vendor/
```

You don't need all of these directories. Only create what your module needs. A module with just a `Routes/web.php` and a `Resources/views/` directory is perfectly valid.

Mature modules also tend to grow directories the package doesn't touch but that have become common Laravel conventions: `Actions/`, `Concerns/` (for traits), `Data/` or `DataTransferObjects/`, `Enums/`, `Exceptions/`, `Http/Controllers/`, `Http/Middleware/`, `Http/Requests/`, `Jobs/`, `Mail/`, `Notifications/`, `Settings/`, `Support/`, and so on. None of these are auto-discovered. They're just file locations that follow Laravel naming conventions, so the classes inside them autoload through the module's PSR-4 mapping like any other class. Use them the same way you'd use their `app/` counterparts.

## Quick start

Once installed, create your first module:

```
Modules/
└── Blog/
    ├── Models/
    │   └── Post.php
    ├── Resources/
    │   └── views/
    │       └── index.blade.php
    └── Routes/
        └── web.php
```

Your model uses the `Modules\Blog` namespace:

```php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body'];
}
```

Your route file works like any Laravel route file:

```php
use Illuminate\Support\Facades\Route;
use Modules\Blog\Models\Post;

Route::get('/blog', function () {
    return view('blog::index', ['posts' => Post::all()]);
});
```

And your view is accessible through the `blog::` namespace:

```blade
{{-- Modules/Blog/Resources/views/index.blade.php --}}
@foreach($posts as $post)
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->body }}</p>
@endforeach
```

That's a working module. The route file gets loaded with the `web` middleware group (because it's named `web.php`), the view is registered under the `blog` namespace, and the model is autoloaded through the PSR-4 mapping you set up during installation.

## Naming conventions

The package converts your module's directory name to a kebab-case prefix for all namespaced assets:

| Module directory | Prefix | Example usage |
|---|---|---|
| `Blog` | `blog` | `view('blog::home')` |
| `UserAdmin` | `user-admin` | `<x-user-admin::nav />` |
| `PWA` | `pwa` | `<livewire:pwa::icons />` |
| `CRM` | `crm` | `__('crm::messages.welcome')` |
| `MyAPI` | `my-api` | `view('my-api::dashboard')` |

This prefix applies to views, Blade components, anonymous components, Livewire components, and translations. Routes use the filename for grouping instead (see the [Routes](./features/routes.md) docs).

## Configuration

All settings live in `config/modules.php`. The file has three layers:

### Global settings

```php
'modules_directory' => 'Modules',     // where modules live (relative to project root)
'modules_namespace' => 'Modules\\',   // PSR-4 namespace prefix
```

If you rename your modules directory (say, to `src/Domains/`), update both of these and your `composer.json` autoload mapping to match.

### Per-module settings

Control which modules are active and the order they load:

```php
'modules' => [
    'Shared' => [
        'active' => true,
        'order' => 1,    // lower numbers load first
    ],
    'Blog' => [
        'active' => true,
        'order' => 2,
    ],
    'Legacy' => [
        'active' => false, // completely ignored during discovery
    ],
],
```

Modules not listed here default to `active: true` with an order of `9999`. Setting `active` to `false` skips the module entirely: no routes, no views, no migrations, nothing.

The `order` value controls the sequence in which discovered assets are registered. If your `Shared` module defines base configs that other modules depend on, give it a low order number so it loads first.

### Per-feature settings

Each feature has its own config section with an `active` toggle and discovery `patterns`:

```php
'views' => [
    'active' => true,
    'patterns' => [
        '*/Resources/views',
    ],
],
```

Setting `active` to `false` disables that feature for all modules. The `patterns` array controls which directories or files get scanned, using glob syntax relative to the modules directory.

Some features have extra options. Configs have a `priority` flag that controls merge direction. Routes have `commands_filenames` and `channels_filenames` arrays for special route files. Check the feature-specific documentation pages for details.

## Modules facade

The `Mozex\Modules\Facades\Modules` facade provides utility methods you can use in your application code:

### Path helpers

```php
use Mozex\Modules\Facades\Modules;

// Absolute path to the modules directory
Modules::modulesPath();
// e.g., /var/www/app/Modules

// Path to a specific location within modules
Modules::modulesPath('Blog/Config');
// e.g., /var/www/app/Modules/Blog/Config

// Project base path with optional suffix
Modules::basePath('storage');
// e.g., /var/www/app/storage
```

### Module name extraction

```php
// From a fully-qualified class name
Modules::moduleNameFromNamespace('Modules\\Blog\\Models\\Post');
// returns 'Blog'

// From a file path
Modules::moduleNameFromPath('/var/www/app/Modules/Blog/Models/Post.php');
// returns 'Blog'
```

### Seeders

```php
// Get all discovered module seeder classes
Modules::seeders();
// returns ['Modules\Blog\Database\Seeders\BlogDatabaseSeeder', ...]
```

### Route customization

The package already discovers `web.php`, `api.php`, `console.php`, and `channels.php` files inside each module's `Routes/` directory with sensible defaults. `web.php` gets the `web` middleware group, `api.php` gets the `api` prefix and middleware, and so on.

If you want to discover a different route file type, say `admin.php` or `localized.php`, you define a new route group using `Modules::routeGroup()`. The group name has to match the filename. Once defined, any module that drops a `Routes/admin.php` file will have those routes loaded with the attributes you configured:

```php
// Now every module's Routes/admin.php file loads with these attributes
Modules::routeGroup('admin',
    prefix: 'admin',
    middleware: ['web', 'auth', 'is-admin'],
    as: 'admin.',
);
```

Attribute values can be closures, which are useful when you need to pull values from config or other sources that aren't available at service provider registration time:

```php
Modules::routeGroup('api',
    prefix: fn () => config('app.api_prefix', 'api'),
    middleware: ['api'],
);
```

For groups that need more than a simple `Route::group()` wrapper, use `registerRoutesUsing()` to take over the registration entirely. The closure receives the group's attributes and the route file and can wrap them in whatever logic the group needs:

```php
// Every module's Routes/localized.php file will be wrapped in Route::localized()
Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(fn () => Route::group($attributes, $routes));
});
```

Call both `routeGroup()` and `registerRoutesUsing()` from a service provider's `register()` method so they're defined before route discovery runs.

## Features at a glance

Here's everything the package discovers and registers:

| Feature | What it does | Config key |
|---|---|---|
| [Configs](./features/configs.md) | Merges module config files into Laravel's config | `configs` |
| [Service Providers](./features/service-providers.md) | Registers module service providers | `service-providers` |
| [Helpers](./features/helpers.md) | Loads helper function files via `require_once` | `helpers` |
| [Commands](./features/commands.md) | Registers Artisan commands | `commands` |
| [Migrations](./features/migrations.md) | Adds migration paths to Laravel's migrator | `migrations` |
| [Seeders](./features/seeders.md) | Discovers module database seeders | `seeders` |
| [Translations](./features/translations.md) | Registers namespaced and JSON translations | `translations` |
| [Views](./features/views.md) | Registers view namespaces and anonymous components | `views` |
| [Blade Components](./features/blade-components.md) | Registers class-based Blade components | `blade-components` |
| [Routes](./features/routes.md) | Loads route files with group-based middleware | `routes` |
| [Models & Factories](./features/models-factories.md) | Wires model-factory name guessing | `models`, `factories` |
| [Policies](./features/policies.md) | Wires model-policy name guessing | `policies` |
| [Events & Listeners](./features/events-listeners.md) | Enables event auto-discovery in modules | `listeners` |
| [Schedules](./features/schedules.md) | Registers module Console Kernels for scheduled tasks | `schedules` |
| [Livewire](./features/livewire-components.md) | Registers Livewire v3 components | `livewire-components` |
| [Filament](./features/filament.md) | Registers resources, pages, widgets, clusters per panel | `filament-*` |
| [Nova](./features/nova-resources.md) | Registers Nova resources | `nova-resources` |

Livewire, Filament, and Nova features are only active when their respective packages are installed. You don't need to configure anything to skip them.

Each feature has its own documentation page with detailed configuration, directory layout, usage examples, and troubleshooting tips. Browse them in the sidebar.

## Integrations

Beyond the features the package discovers, there are integration guides for common tools you'll pair with a modular Laravel project:

- [Inertia](./integrations/inertia.md): module-scoped Inertia pages for Vue or React, Vite alias setup, TypeScript configuration, and typed props generated from PHP via Spatie TypeScript Transformer.
- [PHPStan](./integrations/phpstan.md): configuring PHPStan to analyse every module directory, plus composer scripts for the common commands.
- [PHPUnit](./integrations/phpunit.md): wiring module tests into your PHPUnit test suite.
- [Pest](./integrations/pest.md): applying Pest's TestCase and traits to module tests.
