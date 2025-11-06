# Migrations

## Overview

The package auto-discovers module migration directories and registers them with Laravel’s migrator. All migrations under those directories (including nested subdirectories) are picked up by `php artisan migrate`.

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

## Troubleshooting

- Migration not found: make sure the file lives under a discovered directory and its timestamped filename is correct.
- Order/collisions: ensure unique timestamps across modules to avoid conflicts.
- Targeted runs: use `--path=Modules/ModuleName/Database/Migrations` when you want to run a module’s migrations only.

## See also

- [Seeders](./seeders.md)
- [Models & Factories](./models-factories.md)
