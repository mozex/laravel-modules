---
title: Blade Components
weight: 1
---

Class-based Blade components in modules are discovered and registered with namespaced aliases. A component class at `Modules/Blog/View/Components/Card.php` becomes `<x-blog::card />`. Nested directories become dot-separated paths: `Post/Card.php` becomes `<x-blog::post.card />`.

The package scans for non-abstract classes that extend `Illuminate\View\Component`. Abstract base classes and interfaces are ignored.

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
        ├── Card.php              // <x-blog::card />
        ├── Filter.php            // <x-blog::filter />
        └── Post/
            ├── Card.php          // <x-blog::post.card />
            └── Summary.php       // <x-blog::post.summary />
```

## Writing a component

Module Blade components work exactly like regular Laravel Blade components. Extend `Illuminate\View\Component`, accept data through the constructor, and return a view from `render()`:

```php
namespace Modules\Blog\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public function __construct(
        public string $title,
        public string $excerpt,
        public ?string $image = null,
    ) {}

    public function render()
    {
        return view('blog::components.card');
    }
}
```

The view file lives in the module's view directory:

```blade
{{-- Modules/Blog/Resources/views/components/card.blade.php --}}
<div class="card">
    @if($image)
        <img src="{{ $image }}" alt="{{ $title }}">
    @endif
    <h3>{{ $title }}</h3>
    <p>{{ $excerpt }}</p>
</div>
```

Then use it in any Blade template:

```blade
<x-blog::card title="My Post" excerpt="A short summary" />
```

## Alias naming

The alias follows two rules:

1. The module directory name is converted to kebab-case and used as the namespace prefix: `Blog` becomes `blog`, `UserAdmin` becomes `user-admin`.
2. The path segments under `View/Components/` are kebab-cased and joined with dots: `Post/Card.php` becomes `post.card`.

Some examples:

| Class location | Alias |
|---|---|
| `Blog/View/Components/Filter.php` | `<x-blog::filter />` |
| `Blog/View/Components/Post/Card.php` | `<x-blog::post.card />` |
| `Blog/View/Components/UI/Button/Primary.php` | `<x-blog::ui.button.primary />` |
| `PWA/View/Components/Icons.php` | `<x-pwa::icons />` |

## Inline rendering

Components don't need a separate view file. You can return inline HTML from `render()`:

```php
public function render()
{
    return <<<'blade'
        <span class="badge badge-{{ $type }}">{{ $label }}</span>
    blade;
}
```

## Blade Components vs. Anonymous Components

This feature handles class-based components in `View/Components/`. If you want simple components without a PHP class, use anonymous components instead. Those are `.blade.php` files placed in `Resources/views/components/` and are covered by the [Views](./views.md) feature.

Both can coexist in the same module. Use class-based components when you need constructor logic, computed properties, or methods. Use anonymous components for simpler presentational markup.

## Troubleshooting

**"Unable to locate component" error**: Run `php artisan view:clear` to clear compiled views. Check that the class extends `Component` (not just any class) and lives under a directory matching the configured patterns.

## Disabling

Set `'blade-components.active' => false` to stop auto-registration. Adjust `'blade-components.patterns'` if your components live in a different directory.
