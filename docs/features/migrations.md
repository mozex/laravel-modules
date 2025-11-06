# Migrations

## Overview

The package auto-discovers module migration directories and registers them with Laravel’s migrator. All migrations under those directories (including nested subfolders) are picked up by `php artisan migrate`.

## What gets discovered

- Directories matching the configured patterns (default: `*/Database/Migrations` under each module)

## Default configuration

In `config/modules.php`:

```php
'migrations' => [
    'active' => true,
    'patterns' => [
        '*/Database/Migrations',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Database/
    └── Migrations/
        ├── 2024_01_10_100000_create_posts_table.php
        └── 2024_01_11_110000_add_published_to_posts_table.php

Modules/Shop/
└── Database/
    └── Migrations/
        ├── 2024_02_01_090000_create_orders_table.php
        └── sub/
            └── 2024_02_05_120000_create_order_items_table.php
```

## Usage

- Run migrations as usual; module paths are already registered:
  ```bash
  php artisan migrate
  ```
- You can target specific paths (`--path`) if needed; the discovered directories live under your modules directory.

## Configuration options

- Toggle discovery
  - Set `'migrations.active' => false` to stop registering module migration paths.
- Change discovery patterns
  - Edit `'migrations.patterns'` to add/remove directories, relative to each module root.

## Testing hints

- Assert migrator paths include module directories:
  ```php
  $paths = app('migrator')->paths();
  expect($paths)->toContain(base_path('Modules/Blog/Database/Migrations'));
  ```
- Place a dummy migration in a subfolder and run `migrate --pretend` to confirm it’s picked up.

## Troubleshooting

- Migration not found: verify the directory matches the configured patterns and the feature is active; check subfolder placement and filename timestamps.
- Order/duplicate issues: ensure unique timestamp prefixes across modules to avoid collisions.

## See also

- [Configs](./configs.md)
- [Routes](./routes.md)
- [Views](./views.md)
- [Blade Components](./blade-components.md)
