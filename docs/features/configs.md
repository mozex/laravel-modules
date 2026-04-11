---
title: Configs
weight: 4
---

Module config files are discovered and merged into Laravel's configuration repository at boot time. The filename becomes the config key, so `Modules/Blog/Config/blog.php` is accessible as `config('blog.whatever')`.

This is one of the first features to run during boot, which means other features (routes, views, service providers) can read module config values during their own registration.

## Default configuration

```php
'configs' => [
    'active' => true,
    'patterns' => [
        '*/Config/*.php',
    ],
    'priority' => true,
],
```

## Directory layout

```
Modules/Blog/
└── Config/
    └── blog.php

Modules/Shop/
└── Config/
    ├── shop.php
    └── app.php      // merges with Laravel's config/app.php
```

Config files are standard Laravel config files. They return an array:

```php
// Modules/Blog/Config/blog.php
return [
    'posts_per_page' => 15,
    'allow_comments' => true,
    'cache_ttl' => 3600,
];
```

After discovery, these values are available through `config()`:

```php
config('blog.posts_per_page');  // 15
config('blog.allow_comments');  // true
```

## Merging with existing config

A module config file can share a name with an existing application config file. When `Modules/Shop/Config/app.php` exists alongside Laravel's `config/app.php`, the two arrays are merged. The `priority` setting controls which side wins when keys collide.

### `priority: true` (default)

Module values override application values. The merge is `array_merge($appConfig, $moduleConfig)`, so the module has the final say on any shared keys.

```php
// config/app.php
return ['feature' => ['enabled' => false, 'limit' => 10]];

// Modules/Shop/Config/app.php
return ['feature' => ['enabled' => true]];

// Result:
config('app.feature.enabled'); // true  (module wins)
config('app.feature.limit');   // 10    (app value preserved, module didn't set it)
```

This is useful when a module needs to override application defaults, like turning on a feature flag or changing a driver setting.

### `priority: false`

Application values override module values. The merge flips: `array_merge($moduleConfig, $appConfig)`. The module provides defaults that the application can selectively override.

```php
// With priority: false, same files as above:
config('app.feature.enabled'); // false (app wins)
config('app.feature.limit');   // 10   (app wins)
```

This mode is useful when modules ship sensible defaults but the application should always have the last word.

## Config caching

Merging only runs when Laravel's configuration isn't cached. When you run `php artisan config:cache`, the merged result gets baked into the cache file. After that, config changes in module files won't take effect until you rebuild the cache.

In production, always rebuild the config cache after deploying module config changes:

```bash
php artisan config:clear
php artisan config:cache
```

During development, don't cache configs. Laravel loads everything fresh on each request, so module config changes show up immediately.

## Multiple modules, same config key

If two modules both have a `Config/shared.php` file, both get merged into `config('shared')`. The module load order determines which values end up on top. A module with `order: 1` loads first, then a module with `order: 2` merges over it. With `priority: true`, the last module to load wins.

If you need predictable precedence, set explicit `order` values in the `modules` config section.

## Disabling

Set `'configs.active' => false` to skip config merging entirely. Module config files will still exist on disk but won't be loaded or merged.

You can also adjust `'configs.patterns'` if your modules use a different directory name for config files.
