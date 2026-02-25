# Upgrade Guide

## Upgrading to 3.x from 2.x

### Requirements

| Dependency | Required Version |
|---|---|
| PHP | `^8.3` |
| Laravel | `^11.29 \| ^12.0 \| ^13.0` |
| Livewire | `^4.0` |
| Filament | `^5.0` |

### Blade Templates — No Changes Needed

The `<livewire:module::component />` tag syntax is identical between v2 and v3. Your existing Blade templates require zero changes.

### New `view_path` Config Key

If you have published the `modules.php` config file, add the `view_path` key to the `livewire-components` section:

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
    'view_path' => 'Resources/views/livewire', // Add this line
],
```

This key controls where Livewire looks for Single-File Components (SFC) and Multi-File Components (MFC) relative to each module's root directory. The default value is `Resources/views/livewire`.

### Livewire Scout Output Format Change

If you programmatically consume the `LivewireComponentsScout` discovery results, note that the output format has changed:

**Before (v2):** Individual class entries
```php
['module' => 'First', 'path' => '.../Teams.php', 'namespace' => 'Modules\\First\\Livewire\\Teams']
```

**After (v3):** Directory entries
```php
['module' => 'First', 'path' => '.../Livewire', 'namespace' => 'Modules\\First\\Livewire']
```

### Single-File and Multi-File Component Support (New)

v3 adds support for all three Livewire v4 component types. Place them in the configured `view_path` directory:

**Single-File Component (SFC)** — PHP and Blade in one file:

```blade
{{-- Modules/Blog/Resources/views/livewire/counter.blade.php --}}
<?php

use Livewire\Component;

new class extends Component {
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }
};
?>

<div>
    <span>Count: {{ $count }}</span>
    <button wire:click="increment">+</button>
</div>
```

**Multi-File Component (MFC)** — separate files in a named directory:

```
Modules/Blog/Resources/views/livewire/toggle/
├── toggle.php          // PHP class (required)
└── toggle.blade.php    // Blade view (required)
```

Both types are opt-in — components are only discovered if files exist in the configured view path.

### Removed: Console Kernel Scheduling

The `SupportSchedules` feature and `Mozex\Modules\Contracts\ConsoleKernel` contract have been removed. Module scheduling via `Console\Kernel.php` classes is no longer supported.

**Migration:** Move schedule definitions to `Routes/console.php` using the `Schedule` facade:

```php
// Modules/Blog/Routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('blog:sync')->dailyAt('02:00');
```

This is the standard Laravel approach and has been supported by the package's Routes feature since v2.

### Incompatible Package Versions

Composer will now prevent installation if incompatible versions of Livewire or Filament are detected. If you have Livewire v3 or Filament v3/v4 installed, you must upgrade them before installing this version.

### Dropped Support

- PHP 8.2
- Laravel 10
- Livewire v3
- Filament v3 and v4
- Console Kernel scheduling (`Mozex\Modules\Contracts\ConsoleKernel`)
