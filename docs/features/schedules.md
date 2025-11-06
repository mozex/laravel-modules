# Schedules

## Overview

This feature covers two complementary ways to register scheduled tasks in your modules:

1) Laravel 10+ (recommended): define schedules directly in each module’s `Routes/console.php` using the `Schedule` facade (and optionally inline Artisan commands).
2) Laravel < 10 (or when console route files aren’t available): define a per‑module Console Kernel that declares schedules in a `schedule(Schedule $schedule)` method. The package discovers and runs these module kernels for you.

Use either approach—or both—depending on your Laravel version and needs.

## What gets discovered

- Console route files (Laravel 10+):
  - Files named `console.php` under `Modules/*/Routes/` are added to the Console Kernel when your Laravel supports command route paths. Anything you schedule inside those files via `Schedule::...` will be registered.
  - This is part of the [Routes](./routes.md) feature. Ensure `routes.commands_filenames` contains `console` (default) and your framework supports it.

- Module Console Kernels (version‑independent; especially useful for Laravel < 10):
  - Classes named `Kernel` under `Modules/*/Console/` that extend `Mozex\Modules\Contracts\ConsoleKernel`.
  - The package discovers these classes and calls their `schedule(Schedule $schedule)` method at boot.

## Default configuration

In `config/modules.php`:

```php
'schedules' => [
    'active' => true,
    'patterns' => [
        '*/Console',
    ],
],

'routes' => [
    'active' => true,
    'patterns' => [
        '*/Routes/*.php',
    ],
    'commands_filenames' => [
        'console',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
├── Routes/
│   └── console.php                 // Laravel 10+: schedule via Schedule facade here
└── Console/
    └── Kernel.php                  // Laravel < 10: module Console Kernel with schedule()

Modules/Shop/
├── Routes/
│   └── console.php
└── Console/
    └── Kernel.php
```

## Usage

### Laravel 10+: schedule in `Routes/console.php`

```php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('blog:reindex', function () {
    // ...
})->purpose('Reindex blog')->hourly();

Schedule::exec('blog:sync-external')
    ->dailyAt('02:00');
```

- These files are loaded into the Console Kernel when supported, so both inline commands and `Schedule::...` registrations take effect.
- See [Routes](./routes.md) for more about console route files.

### Laravel < 10: per‑module Console Kernel

Create a `Kernel` under `Modules/{Module}/Console` that extends `Mozex\Modules\Contracts\ConsoleKernel` and implement `schedule()`:

```php
namespace Modules\Blog\Console;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;

class Kernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule): void
    {
        $schedule->exec('blog:sync-external')->dailyAt('02:00');
        // $schedule->command('blog:reindex')->hourly();
    }
}
```

The package discovers these Kernels and calls `schedule()` for each active module.

## Configuration options

- Toggle features
  - `'schedules.active' => false` to disable module Console Kernel scheduling.
  - `'routes.active' => false` or removing `console` from `'routes.commands_filenames'` to stop loading `Routes/console.php`.
- Patterns
  - Adjust `'schedules.patterns'` for Console Kernels and `'routes.patterns'` for console route files if your layout differs.

## Testing hints

- Inspect scheduled events:
  ```php
  $commands = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
      ->pluck('command')
      ->flatten()
      ->map(fn (string $command) => str_contains($command, 'artisan')
          ? str($command)->explode(' ')->last()
          : $command
      );
  expect($commands)->toContain('blog:reindex');
  ```
- In Laravel 10+, verify schedules in `Routes/console.php` are visible alongside module Console Kernel schedules.

## Troubleshooting

- Not seeing schedules from `Routes/console.php`:
  - Ensure your Laravel version supports command route paths (10+), and `routes.commands_filenames` includes `console`.
  - Confirm the file path is `Modules/*/Routes/console.php` and the [Routes](./routes.md) feature is active.
- Module Console Kernel not running:
  - Check the class is `Modules/{Module}/Console/Kernel` and extends `Mozex\Modules\Contracts\ConsoleKernel`.
  - Ensure `'schedules.active' => true` and the module is active.

## See also

- [Routes](./routes.md)
- [Commands](./commands.md)
- [Configs](./configs.md)
