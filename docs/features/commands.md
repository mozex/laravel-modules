# Commands

## Overview

The package discovers module Artisan command classes and registers them automatically. Any class that extends `Illuminate\Console\Command` and matches the configured patterns is picked up and added to Artisan.

## What gets discovered

- Classes that extend `Illuminate\Console\Command`
- Located in directories matching the configured patterns (default: `*/Console/Commands` under each module)
- Abstract base commands (or classes not extending `Command`) are ignored

## Default configuration

In `config/modules.php`:

```php
'commands' => [
    'active' => true,
    'patterns' => [
        '*/Console/Commands',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Console/
    └── Commands/
        ├── PublishPosts.php          // extends Illuminate\Console\Command (discovered)
        └── BaseCommand.php           // abstract base (ignored)

Modules/Shop/
└── Console/
    └── Commands/
        ├── SyncInventory.php         // discovered
        └── ChainSync.php             // discovered
```

## Usage

- Commands are available via Artisan as soon as they are discovered:
  ```bash
  php artisan list | find "blog:publish-posts"
  php artisan blog:publish-posts
  ```

## Configuration options

- Toggle discovery
  - Set `'commands.active' => false` to disable auto-registration.
- Change discovery patterns
  - Edit `'commands.patterns'` to add/remove directories, relative to each module root.

## Console routes integration

- If you define module `console.php` route files (see the [Routes](./routes.md) docs), and your Laravel version supports command route paths (Laravel 10+), those command routes are also registered and visible in `php artisan list`.

## Performance and caching

- Command discovery happens at boot; modules cache (`php artisan modules:cache`) optimizes discovery only.
- Laravel’s `config:cache`/`route:cache` do not affect command registration.

## Testing hints

- Assert command registration via `Artisan::all()` keys or by running commands in tests.
- Ensure abstract base commands aren’t picked up; keep real commands extending `Illuminate\Console\Command`.

## Troubleshooting

- Command not found: verify it extends `Illuminate\Console\Command`, matches a configured pattern, and the feature is active.
- Name collision: ensure your command signatures are unique across modules.

## See also

- [Routes](./routes.md)
- [Helpers](./helpers.md)
- [Configs](./configs.md)
