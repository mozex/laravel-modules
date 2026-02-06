# Livewire Components

## Overview

Discovers Livewire components inside modules and registers them with namespaced aliases: `<livewire:module::path.to.component/>`.

## What gets discovered

- Non-abstract classes extending `Livewire\Component`
- Located in directories matching configured patterns (default: `*/Livewire`)

## Default configuration

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
],
```

## Naming

- Module name → kebab-case prefix, path segments → kebab-cased and dot-joined:
  - `Modules/Blog/Livewire/Posts.php` → `<livewire:blog::posts/>`
  - `Modules/Blog/Livewire/Nested/ManageComments.php` → `<livewire:blog::nested.manage-comments/>`
  - `Modules/PWA/Livewire/Icons.php` → `<livewire:pwa::icons/>`

## Directory layout

```
Modules/Blog/
└── Livewire/
    ├── Posts.php                         // <livewire:blog::posts />
    └── Nested/
        └── NestedUsers.php              // <livewire:blog::nested.nested-users />

Modules/PWA/
└── Livewire/
    └── Icons.php                         // <livewire:pwa::icons />
```

## Usage

```blade
<livewire:blog::posts />
@livewire('shop::list-products')
<livewire:pwa::icons />
```

## Configuration

- Set `'livewire-components.active' => false` to disable auto-registration.
- Edit `'livewire-components.patterns'` to change discovery directories.

## Troubleshooting

- **Component not found**: ensure it extends `Livewire\Component` and is under a discovered `Livewire` directory.
- **Alias mismatch**: use kebab-case module name and dots for nested directories.

## See also

- [Views](./views.md)
- [Blade Components](./blade-components.md)
