# Routes

## Overview

The package auto-discovers route files in your modules and loads them with sensible defaults. Routes are grouped by the filename of each route file (e.g., `web.php`, `api.php`). Special filenames are supported for broadcasting channels (`channels.php`) and console routes (`console.php`). You can customize route groups and how files are registered via the `Modules` facade.

## What gets discovered

- Files matching the configured patterns (default: `*/Routes/*.php` under each module)
- Files are classified by base filename:
  - `console.php`: console command routes (added to console kernel when supported)
  - `channels.php`: broadcasting channel definitions; `Broadcast::routes()` is called once, then channel files are required after the app boots
  - Any other filename (e.g., `web.php`, `api.php`, `admin.php`): loaded as HTTP routes under a route group determined by that filename

## Default configuration

In `config/modules.php`:

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

## Directory layout examples

```
Modules/Blog/
└── Routes/
    ├── web.php         // grouped using the `web` group
    ├── api.php         // grouped using the `api` group
    ├── admin.php       // grouped using a custom `admin` group (if configured)
    ├── channels.php    // Broadcast::routes() + channel definitions
    └── console.php     // console routes (commands)

Modules/Shop/
└── Routes/
    ├── web.php
    └── api.php
```

## Groups and registration

- By default, two groups are pre-defined:
  - `web`: `middleware` → `['web']`
  - `api`: `prefix` → `'api'`, `middleware` → `['api']`
- The group used for a file is the base filename (e.g., `web.php` → `web`).
- You can define or override groups and their attributes using the `Modules` facade (attributes accept static values or closures evaluated at registration time):

```php
use Mozex\Modules\Facades\Modules;

// Define or override a group named 'admin'
Modules::routeGroup('admin',
    prefix: 'admin',
    middleware: ['web', 'auth'],
    as: 'admin::' // name prefix is passed through, use '.' or '::' as you prefer
);

// Override 'api' group dynamically
Modules::routeGroup('api',
    prefix: fn () => config('app.api_prefix', 'api'),
    middleware: ['api', 'throttle:api']
);
```

- You can also override how a group registers its routes by providing a custom registrar:

```php
use Illuminate\Support\Facades\Route;

Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(function () use ($attributes, $routes): void {
        Route::group(
            attributes: $attributes,
            routes: $routes
        );
    });
});
```

Why this works

- The registrar key ('localized') matches the route file name `localized.php`. That file will be loaded using this registrar.

```php
Modules::routeGroup(
    name: 'localized',
    middleware: ['web'],
    as: 'localized::',
);
```

- `Route::localized(...)` is a placeholder for your localization wrapper (macro or package). Replace it with whatever your app uses to set the locale-aware scope.

Minimal usage example

- `Modules/Shop/Routes/localized.php`:

```php
use Illuminate\Support\Facades\Route;

Route::get('/products', fn () => 'Products')->name('products');
```

- With the group above and locale `en`, the route will resolve as:
  - URL: `/en/products`
  - Name: `localized::products`

- If a file's name doesn't match any defined group, it's registered with the default registrar and empty attributes (no added middleware/prefix).

> Where to register
>
> Call `Modules::routeGroup(...)` and `Modules::registerRoutesUsing(...)` from a service provider’s `register()` method (or a provider that runs early) so your groups/registrars are defined before route discovery and loading.

## Broadcasting channels (`channels.php`)

- If any `channels.php` files are discovered, `Broadcast::routes()` is called once after the application boots and all channel files are required.
- Place your channel definitions in `Modules/*/Routes/channels.php` as usual:

```php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

## Console routes (`console.php`)

- `console.php` files are added to the console kernel if your Laravel version supports it (compatibility hook for Laravel 10+).

> Note on schedules (Laravel < 10): if you’re using the traditional Console Kernel scheduling style, you can still keep
schedules inside modules. This package discovers module Console Kernels (extending
`Mozex\Modules\Contracts\ConsoleKernel`) and calls their `schedule(Schedule $schedule)` method via the Schedules
feature. See the [Schedules](./schedules.md) docs.

## Usage examples

- `Modules/Blog/Routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::get('/blog', fn () => 'Blog Home');
```

- `Modules/Shop/Routes/admin.php`:

```php
use Illuminate\Support\Facades\Route;

Route::middleware('can:manage-shop')->group(function () {
    Route::get('/dashboard', fn () => 'Admin Dashboard')->name('dashboard');
});
```

## Configuration options

- Toggle discovery
  - Set `'routes.active' => false` to disable module route loading.
- Change discovery patterns or special filenames
  - Edit `'routes.patterns'`, `'routes.commands_filenames'`, and `'routes.channels_filenames'` to match your conventions.
- Define/override groups and registrars
  - Use `Modules::routeGroup($name, ...)` and `Modules::registerRoutesUsing($name, $closure)` (values can be closures that return computed attributes).

## Testing hints

- Quick smoke tests:
  - Hit a known path registered in a module route file and assert the response.
  - For channels, assert private channel authorization logic runs as expected.
- Validate effective attributes: create a distinct middleware or prefix in a custom group and verify requests are affected.

## Troubleshooting

- File not using expected group: ensure the filename matches your group name (e.g., `admin.php` for group `admin`) or define the group with `Modules::routeGroup('admin', ...)`.
- Middleware/prefix not applied: check your group attributes and confirm closures return expected values at registration time.
- Routes not changing: clear Laravel route cache (`php artisan route:clear`), then warm it again if needed (`php artisan route:cache`).

## See also

- [Views](./views.md)
- [Blade Components](./blade-components.md)
