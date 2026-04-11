---
title: Commands
weight: 7
---

Artisan command classes inside modules are discovered and registered automatically. Any non-abstract class extending `Illuminate\Console\Command` that lives under a configured path becomes available in Artisan.

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
        ├── PublishPosts.php          // discovered (extends Command)
        ├── PruneDrafts.php           // discovered (extends Command)
        └── BaseCommand.php           // ignored (abstract class)
```

## Writing a module command

Module commands work exactly like application commands. Define a `$signature`, a `$description`, and a `handle()` method:

```php
namespace Modules\Blog\Console\Commands;

use Illuminate\Console\Command;

class PublishPosts extends Command
{
    protected $signature = 'blog:publish-posts
                            {--dry-run : Show what would be published without making changes}';

    protected $description = 'Publish all scheduled posts that are past their publish date';

    public function handle(): int
    {
        $query = Post::where('publish_at', '<=', now())
            ->where('status', 'draft');

        if ($this->option('dry-run')) {
            $this->info("Would publish {$query->count()} posts.");
            return self::SUCCESS;
        }

        $count = $query->update(['status' => 'published']);
        $this->info("Published {$count} posts.");

        return self::SUCCESS;
    }
}
```

Run it:

```bash
php artisan blog:publish-posts
php artisan blog:publish-posts --dry-run
```

## What gets discovered

The scanner finds all non-abstract classes that extend `Illuminate\Console\Command` (directly or through intermediate classes). Abstract base commands are skipped, so you can create shared base classes without them showing up in `php artisan list`.

## Signature collisions

Each command's `$signature` must be unique across your entire application, including all modules. If two modules register a command with the same signature, the second one overwrites the first. Use a module-specific prefix (like `blog:`, `shop:`) to avoid collisions.

## Console routes vs. command classes

You can also define closure-based Artisan commands in `Routes/console.php` files (see the [Routes](./routes) docs). Use command classes when you want a dedicated class with its own tests and dependency injection. Use console routes for quick, one-off commands that don't need much structure.

## Disabling

Set `'commands.active' => false` to stop auto-registering module commands. Adjust `'commands.patterns'` if your modules use a different directory for commands.
