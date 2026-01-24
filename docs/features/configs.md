# Configs

## Overview

The package auto-discovers module config files and merges them into Laravel’s configuration at boot (when the app config is not cached). Merging order is configurable, so you can choose whether module values override the app config or act as defaults.

## What gets discovered

- Files matching the configured patterns (default: `*/Config/*.php` under each module)
- Each file’s base filename becomes the config key (e.g., `Modules/Blog/Config/blog.php` → `config('blog.*')`)

## Default configuration

In `config/modules.php`:

```php
'configs' => [
    'active' => true,
    'patterns' => [
        '*/Config/*.php',
    ],
    'priority' => true, // when true, module values override app values
],
```

## Merging strategy

**Important**: Merging only runs when Laravel configuration is **not** cached. In production with cached config (`php artisan config:cache`), module configs are merged at cache-build time, not at runtime. After changing module configs, always rebuild the cache.

- If `priority` is true (default):
  - final = array_merge(app[$key] ?? [], module[$key])
  - module values win for duplicate keys.
- If `priority` is false:
  - final = array_merge(module[$key], app[$key] ?? [])
  - app values win; module files provide defaults.

Example

- `config/app.php` contains:
  ```php
  'feature' => [
      'enabled' => false,
      'limit' => 10,
  ]
  ```
- `Modules/Shop/Config/app.php` contains:
  ```php
  return [
      'feature' => [
          'enabled' => true,
      ],
  ];
  ```
- With `priority: true`, `config('app.feature.enabled')` becomes `true` and `config('app.feature.limit')` remains `10`.

## Directory layout examples

```
Modules/Blog/
└── Config/
    └── blog.php          // config('blog.*')

Modules/Shop/
└── Config/
    └── app.php           // config('app.*') merged with Laravel app config
```

## Usage examples

- Read merged values as usual:
  ```php
  if (config('blog.comments.enabled')) {
      // ...
  }
  $limit = config('app.feature.limit', 10);
  ```

## Configuration options

- Toggle discovery
  - Set `'configs.active' => false` to disable merging.
- Change discovery patterns or priority
  - Edit `'configs.patterns'` to add/remove directories.
  - Set `'configs.priority'` to tune who wins on conflicts.

## Troubleshooting

- Values not updating: clear Laravel’s config cache, then reload (`php artisan config:clear && php artisan config:cache`).
- Wrong key: the file name becomes the key. `Modules/Blog/Config/blog.php` → `config('blog.*')`.
- Merge direction: set `'configs.priority'` to control whether module or app values win on conflicts.

## See also

- [Routes](./routes.md)
