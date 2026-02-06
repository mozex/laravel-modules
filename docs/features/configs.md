# Configs

## Overview

Auto-discovers module config files and merges them into Laravel's configuration at boot time (when config is not cached). The merging order is configurable via the `priority` option.

## What gets discovered

- PHP files matching configured patterns (default: `*/Config/*.php`)
- Each file's base filename becomes the config key: `Modules/Blog/Config/blog.php` → `config('blog.*')`

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

## Merging strategy

**Important**: Merging only runs when Laravel configuration is **not** cached. In production with `php artisan config:cache`, module configs are merged at cache-build time. Always rebuild the cache after changing module configs.

- `priority: true` (default): `array_merge(app_config, module_config)` — module values win
- `priority: false`: `array_merge(module_config, app_config)` — app values win, modules provide defaults

### Example

Given `config/app.php` with `'feature' => ['enabled' => false, 'limit' => 10]` and `Modules/Shop/Config/app.php` returning `['feature' => ['enabled' => true]]`:

- With `priority: true`: `config('app.feature.enabled')` → `true`, `config('app.feature.limit')` → `10`

## Directory layout

```
Modules/Blog/
└── Config/
    └── blog.php          // config('blog.*')

Modules/Shop/
└── Config/
    └── app.php           // merged with Laravel's config/app.php
```

## Configuration

- Set `'configs.active' => false` to disable merging.
- Edit `'configs.patterns'` to change discovery directories.
- Set `'configs.priority'` to control merge direction.

## Troubleshooting

- **Values not updating**: clear config cache (`php artisan config:clear`).
- **Wrong key**: the filename is the key — `blog.php` → `config('blog.*')`.
