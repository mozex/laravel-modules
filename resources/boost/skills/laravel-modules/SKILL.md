---
name: laravel-modules
description: Detailed implementation patterns for mozex/laravel-modules. Activate when working with module routes (custom route groups, custom registrars, inline Route::localized, route file organization by third-party concern like fortify.php), Livewire components inside modules (class/SFC/MFC), Filament resources/pages/widgets/clusters across panels, module-level model factories or policies, config merging between modules and the app, overriding module views from the host application, event listener auto-discovery, Blade components inside modules, seeder wiring via Modules::seeders(), module service providers for morph maps and third-party model policies, when setting up PHPStan/Pest/PHPUnit for a modular Laravel project, or when organizing Inertia frontend assets per module with Vue or React (Vite alias, Inertia page resolver, cross-module imports, typed props from PHP via Spatie TypeScript Transformer). Also covers the full Mozex\Modules\Facades\Modules API.
---

# laravel-modules development

## When to use this skill

Activate this skill when the current task involves a specific laravel-modules feature beyond the basics covered in the guideline. The guideline already covers the PSR-4 setup, module directory structure, namespace prefixing, module activation and load order, and the three `modules:*` Artisan commands. This skill picks up from there with the detailed patterns.

Common triggers:
- Writing or customizing module routes (custom file types like `admin.php`, `localized.php`, or organizing by concern like `fortify.php`)
- Creating Livewire components inside a module (class-based, SFC, or MFC)
- Registering Filament resources, pages, widgets, or clusters with specific panels
- Wiring up model factories and policies that live inside modules
- Writing a module service provider for morph maps or third-party model policies
- Adding event listeners that need auto-discovery
- Overriding a module view without editing the module itself
- Setting up config merging between a module and the application
- Writing seeders and calling them from the host app
- Configuring PHPStan, Pest, or PHPUnit for a modular project
- Organizing Inertia frontend assets per module with Vue or React (Vite alias, Inertia page resolver, cross-module imports, typed props from PHP)
- Reaching for the `Mozex\Modules\Facades\Modules` facade

## Modules facade API

The facade is `Mozex\Modules\Facades\Modules`. It provides path helpers, module name extraction, route customization, and seeder access.

### Path helpers

```php
Modules::basePath('storage');
// project root + suffix, e.g. /var/www/app/storage

Modules::modulesPath('Blog/Config');
// modules directory + suffix, e.g. /var/www/app/Modules/Blog/Config

Modules::setBasePath('/custom/path');
// override the base path (used in tests)
```

### Module name extraction

```php
Modules::moduleNameFromNamespace('Modules\\Blog\\Models\\Post');
// returns 'Blog'

Modules::moduleNameFromPath('/var/www/app/Modules/Blog/Models/Post.php');
// returns 'Blog'
```

### Seeders

```php
Modules::seeders();
// returns array of {Module}DatabaseSeeder class names from active modules
```

### Route customization

```php
Modules::routeGroup('admin',
    prefix: 'admin',
    middleware: ['web', 'auth', 'is-admin'],
    as: 'admin.',
);

Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(fn () => Route::group($attributes, $routes));
});

Modules::getRouteGroups();          // inspect all defined route groups
Modules::getRegisterRoutesUsing();  // inspect custom registrars
```

Call `routeGroup()` and `registerRoutesUsing()` from a service provider's `register()` method so they're defined before route discovery runs.

## Routes

### Built-in groups

Two groups come pre-defined:

| Filename | Attributes |
|---|---|
| `web.php` | `middleware: ['web']` |
| `api.php` | `prefix: 'api', middleware: ['api']` |

### Every route file is discovered

Any `.php` file inside a module's `Routes/` directory gets loaded, even if the filename doesn't match a defined group. A file like `Routes/webhooks.php` will have its routes registered, but without any middleware, prefix, or name attributes wrapping them. If you want group-level configuration applied to a custom file type, define a matching group with `Modules::routeGroup()`.

### Adding new route file types

To discover and wrap `admin.php` files across modules with specific attributes:

```php
// In a service provider's register() method
Modules::routeGroup('admin',
    prefix: 'admin',
    middleware: ['web', 'auth', 'is-admin'],
    as: 'admin.',
);
```

The group name must match the filename exactly. Attributes accept closures for dynamic values:

```php
Modules::routeGroup('api',
    prefix: fn () => config('app.api_prefix', 'api'),
    middleware: ['api', 'throttle:api'],
);
```

### Custom registrars for non-standard wrapping

When `Route::group($attributes, $routes)` isn't enough (for example, localized routes that need `Route::localized()`), use `registerRoutesUsing` to take over registration:

```php
Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(function () use ($attributes, $routes) {
        Route::group($attributes, $routes);
    });
});

Modules::routeGroup('localized', middleware: ['web'], as: 'localized.');
```

### Inline `Route::localized()` alternative

A custom registrar wraps every route in a file. When you only need the wrapping for some routes, call the helper directly inside a regular route file:

```php
// Modules/Landing/Routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Landing\Livewire\BecomeASeller;

Route::get('/terms', [TermsController::class, 'show'])->name('terms');

Route::localized(function () {
    Route::get('become-a-seller', BecomeASeller::class)->name('landing.become-a-seller');
});
```

`/terms` stays plain, `become-a-seller` gets wrapped. No custom registrar needed. Reach for the custom registrar when every route in a dedicated file should share the same wrapping logic.

### Organizing route files by concern

Since every `.php` file inside `Routes/` is discovered, split routes by what they relate to rather than HTTP concerns. A `User` module integrating Jetstream and Fortify might have:

```
Modules/User/Routes/
├── fortify.php    // Fortify login, registration, password reset
├── jetstream.php  // Jetstream team management
└── web.php        // module's own public routes
```

Each file defines its own `Route::group()` internally with the middleware the third-party package needs:

```php
// Modules/User/Routes/fortify.php
Route::group([
    'middleware' => config('fortify.middleware', ['web']),
    'prefix' => 'dashboard',
], function () {
    Route::livewire('/login', Login::class)
        ->middleware(['guest:'.config('fortify.guard')])
        ->name('login');
});
```

`fortify.php` and `jetstream.php` don't match any defined route group, so the package loads each file without wrapping. The `Route::group()` inside each file does the real work. No facade-level route group needed.

### Console routes

Files listed in `config('modules.routes.commands_filenames')` (default: `['console']`) are registered with the console kernel via `addCommandRoutePaths()` instead of being loaded as web routes. Use them for Artisan commands and scheduling:

```php
// Modules/Blog/Routes/console.php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('blog:cleanup', fn () => Post::where('status', 'draft')->old()->delete());
Schedule::command('blog:cleanup')->daily();
```

### Broadcasting channels

Files listed in `config('modules.routes.channels_filenames')` (default: `['channels']`) define broadcast channel authorization. When any `channels.php` file exists in a module, `Broadcast::routes()` is called once after the app boots and all channel files are loaded.

```php
// Modules/Blog/Routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('blog.post.{postId}', function ($user, $postId) {
    return $user->can('view', Post::find($postId));
});
```

## Views and Blade components

### Named views

Each module's `Resources/views/` directory is registered as a namespace equal to the kebab-cased module name:

```php
view('blog::home');               // Modules/Blog/Resources/views/home.blade.php
view('blog::pages.show', [...]);  // Modules/Blog/Resources/views/pages/show.blade.php
```

All Blade directives work across namespaces: `@extends('blog::layouts.app')`, `@include('blog::partials.sidebar')`, `@each(...)`.

### Anonymous Blade components

Any `.blade.php` file in `Resources/views/components/` becomes an anonymous component. No PHP class needed.

```
Modules/Blog/Resources/views/components/
├── alert.blade.php          <x-blog::alert />
└── form/
    └── input.blade.php      <x-blog::form.input />
```

Use `@props([...])` at the top of the file to declare props, just like any Laravel anonymous component.

### Class-based Blade components

Classes extending `Illuminate\View\Component` in `View/Components/` get registered automatically:

```
Modules/Blog/View/Components/
├── Card.php                 <x-blog::card />
└── Post/
    └── Summary.php          <x-blog::post.summary />
```

The class alias follows the path: directory segments get kebab-cased and joined with dots.

### Overriding module views from the host application

Laravel's view finder checks all registered paths for a namespace in order. The package uses `loadViewsFrom()`, which automatically checks `resources/views/vendor/{module}/` BEFORE the module's own view directory. To override a module view without editing the module, drop a file at the vendor path:

```
resources/views/vendor/blog/pages/show.blade.php
```

A call to `view('blog::pages.show')` will now render this file instead of the module's version. This works for any view, is selective (only the files you override get replaced), and survives package updates.

## Livewire components

The package supports all three Livewire v4 component types. The feature is conditional on `class_exists(Livewire::class)`, so nothing happens if Livewire isn't installed.

### Class-based components

PHP class in `Livewire/`, view in `Resources/views/livewire/`:

```php
// Modules/Blog/Livewire/PostEditor.php
namespace Modules\Blog\Livewire;

use Livewire\Component;

class PostEditor extends Component
{
    public function render()
    {
        return view('blog::livewire.post-editor');
    }
}
```

Used as `<livewire:blog::post-editor />`. Nested directories become dot-separated aliases: `Livewire/Comments/List.php` → `<livewire:blog::comments.list />`.

### Single-file components (SFC)

One `.blade.php` file in `Resources/views/livewire/` combining PHP logic and markup:

```blade
{{-- Modules/Blog/Resources/views/livewire/counter.blade.php --}}
<?php

use Livewire\Component;

new class extends Component {
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }
};
?>

<div>
    <span>{{ $count }}</span>
    <button wire:click="increment">+</button>
</div>
```

Used as `<livewire:blog::counter />`.

### Multi-file components (MFC)

A directory inside `Resources/views/livewire/` containing matching `.php` and `.blade.php` files:

```
Modules/Blog/Resources/views/livewire/
└── toggle/
    ├── toggle.php
    └── toggle.blade.php
```

Used as `<livewire:blog::toggle />`. You can add `.js`, `.css`, and `.test.php` files to the same directory for co-located assets and tests.

### Config

```php
'livewire-components' => [
    'active' => true,
    'patterns' => ['*/Livewire'],
    'view_path' => 'Resources/views/livewire',  // relative to module root
],
```

## Filament

The package discovers Resources, Pages, Widgets, and Clusters across modules and registers them with the matching Filament panel. The feature is conditional on `class_exists(Filament::class)`.

### Panel mapping

The directory name between `Filament/` and the asset type directory becomes the panel ID:

```
Modules/Blog/Filament/Admin/Resources/PostResource.php
                      ^^^^^
                      Panel ID = "admin"
```

The match is case-insensitive. Your application must have a panel provider with `->id('admin')` for this to work. A module can have assets for multiple panels: `Filament/Admin/Resources/` and `Filament/Dashboard/Widgets/` register with different panels.

### Directory layout

The package's discovery only cares about the outer `Filament/{Panel}/{Resources|Pages|Widgets|Clusters}/` pattern. The internal structure of each resource is up to you (and depends on your Filament version). Both the flatter v3-style layout and the newer v4 convention with `Schemas/` and `Tables/` subdirectories work.

### Error behavior

If the scout can't extract a panel name from the path (for example, `Filament/Resources/` with no panel directory in between), it throws during asset discovery. If the extracted panel ID doesn't match any registered panel, the assets are silently skipped. Double-check directory names against your panel IDs.

### Config

```php
'filament-resources' => ['active' => true, 'patterns' => ['*/Filament/*/Resources']],
'filament-pages'     => ['active' => true, 'patterns' => ['*/Filament/*/Pages']],
'filament-widgets'   => ['active' => true, 'patterns' => ['*/Filament/*/Widgets']],
'filament-clusters'  => ['active' => true, 'patterns' => ['*/Filament/*/Clusters']],
```

Each type can be disabled independently.

## Models, factories, and policies

These features don't scan directories. They register name-guessing callbacks with Laravel so `Model::factory()` and `Gate::getPolicyFor($model)` resolve to the right classes inside modules.

### Model to factory

`Modules\Blog\Models\Post::factory()` resolves to `Modules\Blog\Database\Factories\PostFactory`. Nested namespaces are preserved: `Models\Comments\Comment` maps to `Database\Factories\Comments\CommentFactory`. For non-module models, Laravel's default guessing still works.

### Factory to model

Given a factory class, the reverse resolver strips the factory sub-namespace and the `Factory` suffix to guess the model.

### Model to policy

`Modules\Blog\Models\Post` resolves to `Modules\Blog\Policies\PostPolicy`. Nested namespaces are preserved. Non-module models fall back to Laravel's default resolver.

### Custom sub-namespaces

If your modules use different directory names, update the config:

```php
'models'    => ['active' => true, 'namespace' => 'Models\\'],
'factories' => ['active' => true, 'namespace' => 'Database\\Factories\\'],
'policies'  => ['active' => true, 'namespace' => 'Policies\\'],
```

Change `'namespace' => 'Entities\\'` if models live in `Entities/` instead of `Models/`, and so on.

### IDE support for factories

The guessing callbacks work but some IDEs can't follow them. Set `$model` explicitly on factories to help autocomplete:

```php
class PostFactory extends Factory
{
    protected $model = Post::class;
}
```

This is optional. The guessing works either way.

## Configs

Module config files are discovered from `*/Config/*.php` and merged into Laravel's config repository. The filename becomes the config key: `Modules/Blog/Config/blog.php` is accessible as `config('blog.whatever')`.

### Merging with existing config

When a module's config file shares a name with an application config file (for example, `Modules/Shop/Config/app.php` alongside `config/app.php`), the arrays get merged. The `priority` flag controls which side wins on collisions.

- `priority: true` (default): `array_merge($appConfig, $moduleConfig)`. Module values override application values.
- `priority: false`: `array_merge($moduleConfig, $appConfig)`. Application values override module defaults.

```php
'configs' => [
    'active' => true,
    'patterns' => ['*/Config/*.php'],
    'priority' => true,
],
```

### Config caching caveat

Merging only runs when Laravel's config cache isn't built. After `php artisan config:cache`, the merged result is frozen until the cache is rebuilt. In production, always rebuild on deploy: `config:clear && config:cache`.

## Events and listeners

Laravel's event discovery is extended to include each module's `Listeners/` directory. Listeners are classes with a typed `handle()` method that accepts an event, and they get wired up automatically without any manual event-to-listener mapping.

```php
namespace Modules\Blog\Listeners;

use Modules\Blog\Events\PostPublished;

class NotifyFollowers
{
    public function handle(PostPublished $event): void
    {
        // ...
    }
}
```

A listener in one module can handle events from another module or from the application. The event type hint on `handle()` is what matters.

### Note on subscribers

Event subscribers (classes with a `subscribe()` method that registers multiple event-listener mappings at once) are NOT auto-discovered by Laravel. They need manual registration via `Event::subscribe(MySubscriber::class)` in a service provider. This is different from listener discovery, which this package extends to modules.

## Seeders

Only classes matching `{Module}DatabaseSeeder` are discovered. For a `Blog` module, that's `BlogDatabaseSeeder`. Other seeder classes in the directory are ignored by discovery but can be called from within the main seeder.

Seeders are NOT auto-registered during boot. Call `Modules::seeders()` from the application's `DatabaseSeeder`:

```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mozex\Modules\Facades\Modules;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(Modules::seeders());
    }
}
```

Inside `BlogDatabaseSeeder`, call sub-seeders the normal way:

```php
$this->call([PostSeeder::class, TagSeeder::class]);
```

## Translations

Module `Lang/` directories register both namespaced PHP translations and JSON translations.

### PHP translations

```php
__('blog::messages.welcome');
__('blog::messages.post_created', ['title' => $post->title]);
trans_choice('blog::messages.comments', 5);
```

The namespace is the kebab-cased module name; the file name (without `.php`) is the next segment; then the array key.

### JSON translations

JSON files live directly in `Lang/`, named by locale (`fr.json`, `es.json`). You only need files for locales that aren't your default; English strings already live inline in `__('Original text')` calls. JSON translations don't use namespaces and merge across all modules and the application.

## Helpers

Files matching `*/Helpers/*.php` are loaded via `require_once` in the service provider's `register()` phase. Always guard functions with `function_exists()` to avoid redeclaration errors across modules:

```php
if (! function_exists('format_price')) {
    function format_price(int $cents): string { /* ... */ }
}
```

Helpers load in module order, then alphabetically within each module. If `function_exists()` evaluates to true, the guard prevents the duplicate definition from being loaded.

## Service providers

Any class extending `Illuminate\Support\ServiceProvider` in a module's `Providers/` directory is auto-registered during the application's `register()` phase. Don't list them in `bootstrap/providers.php`.

Use module service providers for: container bindings, morph map entries for the module's models, policies for third-party models (the package's auto-guessing only covers models inside modules), macro definitions, manual event subscriber registration (`Event::subscribe()`), third-party SDK configuration, or custom boot logic. Don't write one just to register routes, views, or commands; those are already handled by the package.

### Real-world example

A `User` module that registers morph map entries for its own models and adds policies for Spatie Permission's `Role` and `Permission` models (which live in a third-party package, so they don't benefit from the package's model-to-policy auto-guessing):

```php
namespace Modules\User\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\User\Models\EmployeeTeam;
use Modules\User\Models\User;
use Modules\User\Policies\PermissionPolicy;
use Modules\User\Policies\RolePolicy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'employee-team' => EmployeeTeam::class,
            'role' => Role::class,
            'permission' => Permission::class,
        ]);

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
    }
}
```

The policy classes live inside the module (`Modules/User/Policies/RolePolicy.php`) next to the module's own policies, even though they map to third-party models.

## Caching workflow

The package scans the modules directory on every request unless cached. Cache files live at `bootstrap/cache/modules-{asset-type}.php`, one per asset type.

```bash
# Production deploy
php artisan modules:clear
php artisan modules:cache
```

Don't cache in local development. Fresh discovery means new files show up immediately. Cache failures usually indicate a PHP error in a newly added class; fix the error first, then retry caching.

### Custom cache drivers

To swap the default file-backed driver (e.g., for a Redis-backed cache shared across workers), call `BaseScout::useCacheDriverFactory()` from a service provider's `register()`:

```php
use Mozex\Modules\Contracts\BaseScout;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;

BaseScout::useCacheDriverFactory(
    fn (BaseScout $scout): DiscoverCacheDriver => new YourDriver($scout->cacheFile())
);
```

Any class implementing Spatie's `DiscoverCacheDriver` works. Pass `null` to restore the default. If the factory is swapped after the package boots, call `BaseScout::clearInstances()` so scout singletons re-resolve.

If the driver needs to separate runtime caching from deploy-time persistence, implement `Mozex\Modules\Features\SupportCaching\Persistable` alongside `DiscoverCacheDriver`. `BaseScout::cache()` calls `persist()` when the driver implements it, `put()` otherwise.

## PHPStan setup

PHPStan needs to know about module directories. A PHP config file (`phpstan.php`) lets you `glob()` them at runtime so new modules are picked up automatically.

```php
<?php

return [
    'includes' => [
        './vendor/larastan/larastan/extension.neon',
    ],
    'parameters' => [
        'level' => 5,
        'paths' => [
            __DIR__ . '/app',
            __DIR__ . '/config',
            ...glob(__DIR__ . '/Modules/*', GLOB_ONLYDIR),
        ],
        'excludePaths' => [
            'analyseAndScan' => [
                ...glob(__DIR__ . '/Modules/*/Tests', GLOB_ONLYDIR),
                ...glob(__DIR__ . '/Modules/*/Database', GLOB_ONLYDIR),
                ...glob(__DIR__ . '/Modules/*/Resources', GLOB_ONLYDIR),
            ],
        ],
        'databaseMigrationsPath' => glob('Modules/*/Database/Migrations', GLOB_ONLYDIR),
        'tmpDir' => 'storage/phpstan',
        'checkOctaneCompatibility' => true,
        'checkModelProperties' => true,
    ],
];
```

Add composer scripts so you don't have to type `-c phpstan.php` every time:

```json
"scripts": {
    "test:types": "phpstan analyse -c phpstan.php --memory-limit=-1 --ansi",
    "baseline": "@test:types --allow-empty-baseline --generate-baseline phpstan-baseline.php"
}
```

Don't add `./phpstan-baseline.php` to the `includes` array until after you've generated it with `composer baseline`. Including a file that doesn't exist yet causes PHPStan to fail at startup, which creates a chicken-and-egg problem.

## PHPUnit setup

Add a Modules test suite to `phpunit.xml`:

```xml
<testsuites>
    <testsuite name="Modules">
        <directory>./Modules/*/Tests</directory>
    </testsuite>
</testsuites>

<source>
    <include>
        <directory>./app</directory>
        <directory>./Modules</directory>
    </include>
</source>
```

Run with `./vendor/bin/phpunit --testsuite Modules`.

## Pest setup

On top of the PHPUnit configuration, update `tests/Pest.php` to apply the TestCase to module tests:

```php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class)->in('Feature', 'Unit');
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/*');
```

The `../Modules/*/Tests/*` pattern matches every direct child of each module's `Tests/` directory (both files and subdirectories). Pest's `in()` method uses `glob()` to expand the pattern, then for each matched directory, any test file whose path starts with that directory gets the `uses()` applied (recursive by prefix match).

For different traits per Tests subdirectory, split the calls:

```php
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/Feature/*');
uses(TestCase::class)->in('../Modules/*/Tests/Unit/*');
```

## Inertia frontend (Vue or React)

The package only handles PHP discovery. Frontend assets inside modules (Vue/React, TypeScript, CSS) are wired up by Vite and your Inertia bootstrap, not by the package. The convention is to put each module's frontend in `Modules/{Name}/Resources/ts/` alongside `Resources/views/`, with Inertia pages under `Resources/ts/Pages/`. The patterns below don't depend on specific Inertia, Vite, Vue, or React versions. Inertia's official Vite plugin auto-resolves pages from `./Pages/`, so module-scoped pages still need the manual `resolve` callback shown below (Inertia's docs explicitly support it as an alternative to the plugin).

### Vite alias (regex, framework-agnostic)

Add a regex alias to `vite.config.ts` so every module resolves without per-module config. The alias itself is identical for Vue and React; only the framework plugin changes:

```ts
// Vue
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [laravel({ input: ['resources/ts/app.ts'] }), vue()],
    resolve: {
        alias: [
            { find: /^@\//, replacement: '/resources/ts/' },
            { find: /^@Modules\/([^\/]+)\/(.*)$/, replacement: '/Modules/$1/Resources/ts/$2' },
        ],
    },
});
```

```ts
// React
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [laravel({ input: ['resources/ts/app.tsx'] }), react()],
    resolve: {
        alias: [
            { find: /^@\//, replacement: '/resources/ts/' },
            { find: /^@Modules\/([^\/]+)\/(.*)$/, replacement: '/Modules/$1/Resources/ts/$2' },
        ],
    },
});
```

Components can then import across modules:

```ts
// Vue
import ForumLayout from '@Modules/Forum/Layouts/ForumLayout.vue';
import Button from '@Modules/Shared/Components/Button.vue';

// React
import ForumLayout from '@Modules/Forum/Layouts/ForumLayout';
import Button from '@Modules/Shared/Components/Button';
```

### Inertia page resolver

Extend Inertia's resolver to handle the `@Modules/` prefix. The logic is identical for both frameworks; only the Inertia adapter package and file extension change.

```ts
// Vue: resources/ts/app.ts
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

type Glob = Record<string, () => Promise<DefineComponent>>;

const appPages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');
const modulePages = import.meta.glob<DefineComponent>('../../Modules/**/Resources/ts/Pages/**/*.vue');

function resolveInertiaPage(name: string): [string, Glob] {
    if (name.startsWith('@Modules/')) {
        const withoutPrefix = name.replace('@Modules/', '');
        const module = withoutPrefix.substring(0, withoutPrefix.indexOf('/'));
        const pagePath = withoutPrefix.slice(module.length + 1);
        return [`../../Modules/${module}/Resources/ts/Pages/${pagePath}.vue`, modulePages];
    }
    return [`./Pages/${name}.vue`, appPages];
}

createInertiaApp({
    resolve: (name) => resolvePageComponent(...resolveInertiaPage(name)),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) }).use(plugin).mount(el);
    },
});
```

```tsx
// React: resources/ts/app.tsx
import type { ResolvedComponent } from '@inertiajs/react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

type Glob = Record<string, () => Promise<ResolvedComponent>>;

const appPages = import.meta.glob<ResolvedComponent>('./Pages/**/*.tsx');
const modulePages = import.meta.glob<ResolvedComponent>('../../Modules/**/Resources/ts/Pages/**/*.tsx');

function resolveInertiaPage(name: string): [string, Glob] {
    if (name.startsWith('@Modules/')) {
        const withoutPrefix = name.replace('@Modules/', '');
        const module = withoutPrefix.substring(0, withoutPrefix.indexOf('/'));
        const pagePath = withoutPrefix.slice(module.length + 1);
        return [`../../Modules/${module}/Resources/ts/Pages/${pagePath}.tsx`, modulePages];
    }
    return [`./Pages/${name}.tsx`, appPages];
}

createInertiaApp({
    resolve: (name) => resolvePageComponent(...resolveInertiaPage(name)),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
```

### Controller usage

Framework-agnostic (PHP side):

```php
return inertia(
    component: '@Modules/Blog/Post/Show',
    props: PostShowResource::from($post),
);
```

`@Modules/Blog/Post/Show` resolves to `Modules/Blog/Resources/ts/Pages/Post/Show.vue` (Vue) or `.tsx` (React). Inline Inertia routes work the same way:

```php
Route::inertia('/coming-soon', '@Modules/Shared/ComingSoon');
```

### TypeScript paths (one entry per module)

Vite's regex alias handles the bundle, but TypeScript's `paths` doesn't support regex. One entry per module in `tsconfig.json`. Include both `.vue` and `.tsx` in the `include` globs if you use both, otherwise keep only what you need:

```json
{
    "compilerOptions": {
        "paths": {
            "@/*": ["./resources/ts/*"],
            "@Modules/Blog/*":   ["./Modules/Blog/Resources/ts/*"],
            "@Modules/Forum/*":  ["./Modules/Forum/Resources/ts/*"],
            "@Modules/Shared/*": ["./Modules/Shared/Resources/ts/*"]
        }
    },
    "include": [
        "resources/ts/**/*.ts",
        "resources/ts/**/*.d.ts",
        "resources/ts/**/*.vue",
        "resources/ts/**/*.tsx",
        "Modules/*/Resources/ts/**/*.ts",
        "Modules/*/Resources/ts/**/*.d.ts",
        "Modules/*/Resources/ts/**/*.vue",
        "Modules/*/Resources/ts/**/*.tsx"
    ]
}
```

`include` uses globs and needs no per-module update. Only `paths` does. Add an entry whenever creating a module with frontend code.

### Auto-generated types from PHP classes

Pair `spatie/laravel-typescript-transformer` with `spatie/laravel-data` to get TypeScript interfaces from PHP Resource/Data classes. PHP-side setup is framework-agnostic:

```php
// config/typescript-transformer.php
'auto_discover_types' => [
    app_path(),
    base_path('Modules/*'),
],
```

Output the types to `resources/ts/types/backend.d.ts`. The frontend side differs between Vue and React.

**Vue**: Vue SFC compiler macros like `defineProps<T>()` run at compile time and don't automatically see external `.d.ts` files. Register the file on the Vue plugin via `script.globalTypeFiles` (valid option since Vue 3.3):

```ts
vue({
    script: {
        globalTypeFiles: ['resources/ts/types/backend.d.ts'],
    },
}),
```

Vue pages then type props directly:

```vue
<script lang="ts" setup>
const props = defineProps<PostShowResource>();
</script>
```

**React**: React uses plain TypeScript with no SFC compiler, so no plugin config is needed. Any `declare interface` in a `.d.ts` file that's part of `tsconfig.json`'s `include` array is globally available automatically. Type props by destructuring the function parameter against the generated interface:

```tsx
export default function Show({ post, replies }: PostShowResource) {
    return <article><h1>{post.title}</h1></article>;
}
```

Or via Inertia's `usePage` hook with a generic when you also need shared props or page metadata:

```tsx
import { usePage } from '@inertiajs/react';

export default function Show() {
    const { props } = usePage<PostShowResource>();
    return <article><h1>{props.post.title}</h1></article>;
}
```

Both are shown in Inertia's official TypeScript guide. Destructured parameters give you page-specific props only; `usePage<T>()` also exposes shared props from middleware, flash messages, errors, and page metadata.

Change a property on the PHP Resource, regenerate types, and TypeScript catches any component that breaks.

### Module CSS imports

To import module stylesheets into `resources/css/app.css` without awkward `/../` paths, alias `Resources/` directly (broader than `Resources/ts/`):

```ts
{ find: /^@Modules\/([^\/]+)\/(.*)$/, replacement: '/Modules/$1/Resources/$2' },
```

Then:

```css
@import '@Modules/Forum/css/forum.css';
```

Tradeoff: TS imports become `@Modules/Forum/ts/Components/X.vue` (Vue) or `@Modules/Forum/ts/Components/X` (React) instead of `@Modules/Forum/Components/X`. Pick one shape and stay consistent.

## Common gotchas

- **Module files not discovered**: run `php artisan modules:list` to confirm the module is enabled and showing asset counts. A zero count or missing asset type row means the scout found nothing matching the configured patterns.
- **Cached discovery stale**: run `php artisan modules:clear` after adding, renaming, or moving files. Skip caching entirely in local development.
- **Seeder not running**: confirm the class is named exactly `{ModuleName}DatabaseSeeder` and that `Modules::seeders()` is called from the app's `DatabaseSeeder`.
- **Filament assets missing**: verify the directory structure includes a panel segment (`Filament/Admin/Resources/`, not `Filament/Resources/`) and that the directory name matches a panel ID in the app.
- **Namespace mismatch errors**: the `Modules\\` PSR-4 mapping must be in the project's `composer.json` (not the package's), followed by `composer dump-autoload`.
- **Config changes not applied**: if `config:cache` is active, rebuild it. Module config merging only runs on uncached config loads.
