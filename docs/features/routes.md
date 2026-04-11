---
title: Routes
weight: 3
---

Route files are discovered from each module's `Routes/` directory and loaded with middleware groups based on the filename. A file named `web.php` gets the `web` middleware group. A file named `api.php` gets the `api` prefix and middleware. This convention-based approach means most modules need zero route configuration.

## Default configuration

```php
'routes' => [
    'active' => true,
    'patterns' => [
        '*/Routes/*.php',
    ],
    'commands_filenames' => [
        'console',
    ],
    'channels_filenames' => [
        'channels',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Routes/
    ├── web.php         // web middleware group
    ├── api.php         // api prefix + middleware group
    ├── admin.php       // custom group (see below)
    ├── channels.php    // broadcast channel definitions
    └── console.php     // Artisan console commands and scheduling
```

Route files are standard Laravel route files. Write them exactly as you would in `routes/web.php` or `routes/api.php`:

```php
// Modules/Blog/Routes/web.php
use Illuminate\Support\Facades\Route;

Route::get('/blog', [PostController::class, 'index']);
Route::get('/blog/{post}', [PostController::class, 'show']);
```

```php
// Modules/Blog/Routes/api.php
use Illuminate\Support\Facades\Route;

Route::get('/posts', [PostApiController::class, 'index']);
Route::post('/posts', [PostApiController::class, 'store']);
```

The `api.php` routes get the `/api` prefix automatically, so the endpoints above become `/api/posts` and `/api/posts`.

## Built-in route groups

Two groups come pre-defined:

| Filename | Group attributes |
|---|---|
| `web.php` | `middleware: ['web']` |
| `api.php` | `prefix: 'api', middleware: ['api']` |

These are the route file types that get discovered with sensible defaults out of the box.

## Every route file is discovered

Any `.php` file you drop into a module's `Routes/` directory gets discovered and loaded, even if the filename doesn't match a defined group. You don't have to register it anywhere. Create `Routes/admin.php`, `Routes/webhooks.php`, or `Routes/anything.php` and the routes inside will be loaded automatically.

The catch: a file with a name that doesn't match a defined group gets loaded without any middleware, prefix, or naming attributes. The routes work, but nothing wraps them. If you want middleware, a prefix, a name prefix, or any other group-level configuration applied to that file, define a matching route group (see the next section).

## Adding new route file types

If you want the package to discover a new type of route file across modules, like `admin.php` or `settings.php`, define a route group with that filename as the key. Every module's matching file will then be loaded with the attributes you configure.

Call this from a service provider's `register()` method so the group is defined before route discovery runs:

```php
use Mozex\Modules\Facades\Modules;

// In a service provider's register() method
Modules::routeGroup('admin',
    prefix: 'admin',
    middleware: ['web', 'auth', 'is-admin'],
    as: 'admin.',
);
```

Now any module that creates a `Routes/admin.php` file gets those attributes applied. The group name must match the filename exactly.

Group attributes accept closures, which are evaluated at registration time:

```php
Modules::routeGroup('api',
    prefix: fn () => config('app.api_prefix', 'api'),
    middleware: ['api', 'throttle:api'],
);
```

This lets you pull values from config or other sources that aren't available at service provider registration time.

## Custom registrars

A `routeGroup()` call wraps your routes in a standard `Route::group($attributes, $routes)` call. That works for most cases, but some route types need more than that. For example, a `localized.php` group might need to be wrapped in a `Route::localized()` call so every route inside gets locale-aware URLs. Custom registrars let you take over the registration entirely and wrap the routes in whatever logic you need.

```php
use Mozex\Modules\Facades\Modules;
use Illuminate\Support\Facades\Route;

Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(function () use ($attributes, $routes): void {
        Route::group($attributes, $routes);
    });
});

Modules::routeGroup('localized', middleware: ['web'], as: 'localized.');
```

With this setup, every module's `Routes/localized.php` file is loaded inside a `Route::localized()` call. The registrar key matches the route filename.

Call both `routeGroup()` and `registerRoutesUsing()` from your service provider's `register()` method.

## Console routes

Files listed in the `commands_filenames` config (default: `['console']`) are handled specially. Instead of loading into a route group, they're registered with Laravel's console kernel via `addCommandRoutePaths()`.

Use `console.php` to define Artisan commands with closures and schedule tasks:

```php
// Modules/Blog/Routes/console.php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('blog:cleanup', function () {
    // Clean up old draft posts
});

Schedule::command('blog:cleanup')->daily();
```

This keeps module-specific scheduling and console commands self-contained within the module.

## Broadcasting channels

Files listed in the `channels_filenames` config (default: `['channels']`) define broadcast channel authorization callbacks. When any `channels.php` files are discovered, `Broadcast::routes()` is called once after the application boots, then all channel files are loaded:

```php
// Modules/Blog/Routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('blog.post.{postId}', function ($user, $postId) {
    return $user->can('view', Post::find($postId));
});
```

## Route caching

When Laravel's route cache is active (`php artisan route:cache`), module routes are included in the cached file. After adding or changing module route files, rebuild the cache:

```bash
php artisan route:clear
php artisan route:cache
```

The package skips route loading entirely when a route cache exists, just like Laravel does with its own route files.

## Load order

Routes load in module order. If `Shared` has `order: 1` and `Blog` has `order: 2`, Shared's routes register first. This matters when routes have overlapping patterns, since the first matching route wins.

## Disabling

Set `'routes.active' => false` to disable all module route loading. You can also adjust the `patterns`, `commands_filenames`, and `channels_filenames` arrays to control exactly which files get picked up.
