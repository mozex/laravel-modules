# Livewire Components

## Overview

Auto-discovers Livewire components within modules and registers them with namespaced aliases using `<livewire:module::component/>`. All three Livewire v4 component types are supported: class-based, single-file (SFC), and multi-file (MFC).

## What gets discovered

- Class-based components extending `Livewire\Component` in directories matching configured patterns (default: `*/Livewire`)
- Single-file components (`.blade.php`) in the configured `view_path` directory
- Multi-file components (named directories with matching `.php` and `.blade.php` files) in the configured `view_path` directory

## Default configuration

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
    'view_path' => 'Resources/views/livewire',
],
```

## Component types

### Class-based components

Traditional Livewire components with a PHP class in the `Livewire/` directory and a separate Blade view:

```php
// Modules/Blog/Livewire/Posts.php
namespace Modules\Blog\Livewire;

use Livewire\Component;

class Posts extends Component
{
    public function render()
    {
        return view('blog::livewire.posts');
    }
}
```

### Single-File Components (SFCs)

Combine PHP and Blade in one `.blade.php` file. Place them in the configured `view_path` directory:

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

SFCs are automatically discovered and accessible as `<livewire:blog::counter />`.

### Multi-File Components (MFCs)

Separate the PHP class and Blade view into their own files inside a named directory. Both files must share the directory name. Place the directory in the configured `view_path`:

```php
// Modules/Blog/Resources/views/livewire/toggle/toggle.php
<?php

use Livewire\Component;

new class extends Component {
    public bool $on = false;

    public function toggle(): void
    {
        $this->on = ! $this->on;
    }
};
```

```blade
{{-- Modules/Blog/Resources/views/livewire/toggle/toggle.blade.php --}}
<div>
    <span>{{ $on ? 'On' : 'Off' }}</span>
    <button wire:click="toggle">Toggle</button>
</div>
```

MFCs are automatically discovered and accessible as `<livewire:blog::toggle />`. You can optionally add `.js`, `.css`, and `.test.php` files to the same directory.

## Naming

- Module name → kebab-case prefix, path segments → kebab-cased and dot-joined:
  - `Modules/Blog/Livewire/Posts.php` → `<livewire:blog::posts/>`
  - `Modules/Blog/Livewire/Nested/ManageComments.php` → `<livewire:blog::nested.manage-comments/>`
  - `Modules/PWA/Livewire/Icons.php` → `<livewire:pwa::icons/>`
  - `Modules/Blog/Resources/views/livewire/counter.blade.php` → `<livewire:blog::counter/>` (SFC)
  - `Modules/Blog/Resources/views/livewire/toggle/toggle.blade.php` → `<livewire:blog::toggle/>` (MFC)

## Directory layout

```
Modules/Blog/
├── Livewire/
│   ├── Posts.php                              // Class: <livewire:blog::posts />
│   └── Nested/
│       └── NestedUsers.php                    // Class: <livewire:blog::nested.nested-users />
└── Resources/
    └── views/
        └── livewire/
            ├── posts.blade.php                // View for Posts class component
            ├── counter.blade.php              // SFC: <livewire:blog::counter />
            ├── toggle/                        // MFC: <livewire:blog::toggle />
            │   ├── toggle.php
            │   └── toggle.blade.php
            └── nested/
                └── nested-users.blade.php     // View for NestedUsers class component
```

## Usage

```blade
<livewire:blog::posts />
@livewire('shop::list-products')
<livewire:pwa::icons />
<livewire:blog::counter />  {{-- SFC --}}
<livewire:blog::toggle />   {{-- MFC --}}
```

## Configuration

- Set `'livewire-components.active' => false` to disable auto-registration.
- Edit `'livewire-components.patterns'` to change class-based component discovery directories.
- Edit `'livewire-components.view_path'` to change the SFC/MFC view directory (relative to module root). Default: `Resources/views/livewire`.

## Troubleshooting

- **Component not found**: ensure class-based components extend `Livewire\Component` and are under a discovered `Livewire` directory, or ensure SFC/MFC files are in the configured `view_path`.
- **Alias mismatch**: use kebab-case module name and dots for nested directories.
- **SFC not discovered**: verify the file uses `.blade.php` extension and is in the `view_path` directory.
- **MFC not discovered**: verify the directory contains both `{name}.php` and `{name}.blade.php` files with matching names.

## Version compatibility

This version of the package requires Livewire v4. If your application still uses Livewire v3, use the [2.x branch](https://github.com/mozex/laravel-modules/tree/2.x) instead.

## See also

- [Views](./views.md)
- [Blade Components](./blade-components.md)
