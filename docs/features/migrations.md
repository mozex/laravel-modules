# Migrations

## Overview

Auto-discovers module migration directories and registers them with Laravel's migrator. All migrations under those directories are picked up by `php artisan migrate`.

## Default configuration

```php
'migrations' => [
    'active' => true,
    'patterns' => [
        '*/Database/Migrations',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Database/
    └── Migrations/
        ├── 2024_01_10_100000_create_posts_table.php
        └── 2024_01_11_110000_add_published_to_posts_table.php
```

## Usage

Run migrations as usual — module paths are already registered:

```bash
php artisan migrate
```

Target a specific module:

```bash
php artisan migrate --path=Modules/Blog/Database/Migrations
```

## Configuration

- Set `'migrations.active' => false` to stop registering module migration paths.
- Edit `'migrations.patterns'` to change discovery directories.

## Troubleshooting

- **Migration not found**: ensure the file is under a discovered directory with a valid timestamped filename.
- **Timestamp collisions**: use unique timestamps across modules.

## See also

- [Seeders](./seeders.md)
- [Models & Factories](./models-factories.md)
