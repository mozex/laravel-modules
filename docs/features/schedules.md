---
title: Schedules
weight: 16
---

The recommended way to register scheduled tasks in modules is through `Routes/console.php` using the `Schedule` facade. See the [Routes](./routes.md) feature docs for details on how console route files work.

The package also gives each module an optional Console Kernel that Laravel's scheduler calls automatically. Create a `Console/Kernel.php` class inside a module, extend `Mozex\Modules\Contracts\ConsoleKernel`, and implement a `schedule()` method. That's it. The package discovers the kernel, instantiates it, and hands Laravel's scheduler to your method.

## Default configuration

```php
'schedules' => [
    'active' => true,
    'patterns' => [
        '*/Console',
    ],
],
```

The scout scans each `Console` directory for classes named exactly `Kernel` that extend `Mozex\Modules\Contracts\ConsoleKernel`. Other classes in the directory are ignored (including your Artisan commands under `Console/Commands/`, which are discovered by a separate feature).

## Directory layout

```
Modules/Blog/
└── Console/
    ├── Kernel.php               // <- discovered schedule kernel
    └── Commands/                // <- Artisan commands (separate feature)
        └── PublishPosts.php
```

## Writing a module Console Kernel

Extend `Mozex\Modules\Contracts\ConsoleKernel` and implement `schedule()`:

```php
namespace Modules\Blog\Console;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;

class Kernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule): void
    {
        $schedule->command('blog:publish-posts')->everyFiveMinutes();

        $schedule->command('blog:cleanup-drafts')->daily();

        $schedule->call(function () {
            // inline task
        })->hourly();
    }
}
```

The package calls `new Kernel()->schedule($schedule)` once the application boots, passing Laravel's active `Schedule` instance. You get the full scheduler API: frequency constraints (`->everyFiveMinutes()`, `->daily()`, `->cron(...)`), environment guards (`->environments('production')`), overlap prevention (`->withoutOverlapping()`), output capture (`->appendOutputTo(...)`), and anything else the Laravel scheduler supports.

## Naming and location

The scout has three strict rules:

1. The file must be named `Kernel.php`.
2. The class must live at namespace `Modules\{ModuleName}\Console\Kernel`.
3. The class must extend `Mozex\Modules\Contracts\ConsoleKernel` (not Laravel's `Illuminate\Foundation\Console\Kernel`).

Any other class in the `Console/` directory is ignored. If you create `Modules/Blog/Console/MyKernel.php` or put the kernel at a different namespace, the scout won't pick it up.

## Disabling

Set `'schedules.active' => false` to disable Console Kernel discovery. Your `Routes/console.php` files will still work; they're handled by the Routes feature, not this one.

Adjust `'schedules.patterns'` if you keep module Console Kernels somewhere other than `Console/`.
