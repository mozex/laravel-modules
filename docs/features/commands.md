# Commands

## Overview

Discovers module Artisan command classes and registers them automatically. Any non-abstract class extending `Illuminate\Console\Command` that matches configured patterns is added to Artisan.

## Default configuration

```php
'commands' => [
    'active' => true,
    'patterns' => [
        '*/Console/Commands',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Console/
    └── Commands/
        ├── PublishPosts.php          // extends Command (discovered)
        └── BaseCommand.php           // abstract (ignored)
```

## Usage

Commands are available via Artisan as soon as they are discovered:

```bash
php artisan blog:publish-posts
```

## Console routes

If you define module `Routes/console.php` files and your Laravel version supports command route paths (Laravel 10+), those commands are also registered. See [Routes](./routes.md).

## Configuration

- Set `'commands.active' => false` to disable auto-registration.
- Edit `'commands.patterns'` to change discovery directories.

## Troubleshooting

- **Command not found**: ensure it extends `Illuminate\Console\Command`, has a `$signature`, and is under a discovered path.
- **Duplicate signature**: ensure unique `$signature` values across modules.
