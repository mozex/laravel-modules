# Blade Components

## Overview

Auto-discovers class-based Blade components within modules and registers them with namespaced aliases using the `<x-module::path.to.component/>` syntax.

## What gets discovered

- Non-abstract classes extending `Illuminate\View\Component`
- Located in directories matching configured patterns (default: `*/View/Components`)

## Default configuration

```php
'blade-components' => [
    'active' => true,
    'patterns' => [
        '*/View/Components',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── View/
    └── Components/
        ├── Post/
        │   └── Card.php          // <x-blog::post.card />
        ├── Filter.php            // <x-blog::filter />
        └── WithoutView.php       // <x-blog::without-view />
```

## Naming rules

- Module name → kebab-case prefix: `Blog` → `blog`, `UserAdmin` → `user-admin`
- Path segments under the matched pattern → kebab-cased and dot-joined: `Post/Card.php` → `post.card`
- Fallback: if no path segments can be derived, the alias uses the lowercase class basename

## Usage

```blade
<x-blog::filter :name="$name" />
<x-blog::post.card :post="$post" />
```

Components can return any view from their `render()` method:

```php
class Card extends \Illuminate\View\Component
{
    public function __construct(public $post) {}

    public function render()
    {
        return view('blog::components.post.card');
    }
}
```

## Configuration

- Set `'blade-components.active' => false` to disable auto-registration.
- Edit `'blade-components.patterns'` to change discovery directories.

## Troubleshooting

- **Unknown tag**: clear compiled views (`php artisan view:clear`) and confirm the alias matches the kebab-cased path.
- **Alias collision**: if two classes across modules map to the same alias, rename one or adjust directory structure.

## See also

- [Views & Anonymous Components](./views.md)
