# Routes

## Overview

Auto-discovers route files in modules and loads them with sensible defaults. Routes are grouped by filename (`web.php` → `web` group, `api.php` → `api` group). Special filenames handle broadcasting channels (`channels.php`) and console routes (`console.php`). Groups and registrars are customizable via the `Modules` facade.

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
    ├── web.php         // grouped with 'web' middleware
    ├── api.php         // grouped with 'api' prefix + middleware
    ├── admin.php       // custom group (if configured)
    ├── channels.php    // broadcast channel definitions
    └── console.php     // console routes (Laravel 10+)
```

## Groups and registration

Two groups are pre-defined:

```php
Modules::routeGroup('api', prefix: 'api', middleware: ['api']);
Modules::routeGroup('web', middleware: ['web']);
```

- The filename is the group key: `web.php` → `web`, `api.php` → `api`.
- Files with unrecognized names get no middleware or prefix.

### Custom groups

```php
// In a service provider's register() method
Modules::routeGroup('admin',
    prefix: 'admin',
    middleware: ['web', 'auth'],
    as: 'admin::'
);
```

Attribute values can be closures (evaluated at registration time):

```php
Modules::routeGroup('api',
    prefix: fn () => config('app.api_prefix', 'api'),
    middleware: ['api', 'throttle:api']
);
```

### Custom registrars

Override how a group registers its routes:

```php
Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(function () use ($attributes, $routes): void {
        Route::group($attributes, $routes);
    });
});

Modules::routeGroup('localized', middleware: ['web'], as: 'localized::');
```

The registrar key matches the route filename (`localized.php`).

> Call `Modules::routeGroup()` and `Modules::registerRoutesUsing()` from a service provider's `register()` method so groups are defined before route discovery.

## Broadcasting channels

If any `channels.php` files are discovered, `Broadcast::routes()` is called once after the app boots and all channel files are required:

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

## Console routes

`console.php` files are added to the console kernel on Laravel 10+. For schedule registration on older versions, see [Schedules](./schedules.md).

## Configuration

- Set `'routes.active' => false` to disable module route loading.
- Edit `'routes.patterns'`, `'routes.commands_filenames'`, and `'routes.channels_filenames'` to match your conventions.

## Troubleshooting

- **Wrong group**: the filename is the group key. Define attributes via `Modules::routeGroup('admin', ...)`.
- **Changes not visible**: clear route cache (`php artisan route:clear`).
- **Channels not loading**: place definitions in `Modules/*/Routes/channels.php`.
