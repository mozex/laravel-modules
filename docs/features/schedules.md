# Schedules

## Overview

Two complementary ways to register scheduled tasks in modules:

1. **Laravel 10+** (recommended): define schedules in `Routes/console.php` using the `Schedule` facade.
2. **All versions**: define a per-module Console Kernel extending `Mozex\Modules\Contracts\ConsoleKernel` with a `schedule()` method.

## Default configuration

```php
'schedules' => [
    'active' => true,
    'patterns' => [
        '*/Console',
    ],
],
```

Console route files are handled by the [Routes](./routes.md) feature (`routes.commands_filenames: ['console']`).

## Directory layout

```
Modules/Blog/
├── Routes/
│   └── console.php             // Laravel 10+
└── Console/
    └── Kernel.php              // All versions
```

## Laravel 10+: Routes/console.php

```php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('blog:reindex', function () {
    // ...
})->purpose('Reindex blog')->hourly();

Schedule::command('blog:sync')->dailyAt('02:00');
```

## All versions: Console Kernel

Create `Modules/{Module}/Console/Kernel.php` extending `Mozex\Modules\Contracts\ConsoleKernel`:

```php
namespace Modules\Blog\Console;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;

class Kernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule): void
    {
        $schedule->command('blog:sync')->dailyAt('02:00');
    }
}
```

The package discovers these Kernels and calls `schedule()` for each active module.

## Configuration

- `'schedules.active' => false` disables Console Kernel scheduling.
- Remove `'console'` from `'routes.commands_filenames'` to stop loading `Routes/console.php`.

## Troubleshooting

- **Console route schedules not running**: ensure Laravel 10+ and files are named `Routes/console.php`.
- **Kernel not invoked**: class must be named `Kernel` in `{Module}\Console` and extend `Mozex\Modules\Contracts\ConsoleKernel`.

## See also

- [Routes](./routes.md)
- [Commands](./commands.md)
