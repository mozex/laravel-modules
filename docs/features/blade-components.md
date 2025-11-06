# Blade Components

## Overview

The package auto-discovers class-based Blade components within your modules and registers them with a clean, predictable alias. You can then use them via the `<x-module::path.to.component/>` syntax anywhere in your app.

## What gets discovered

- Classes that extend `Illuminate\View\Component`
- Located in directories matching the configured patterns (default: `*/View/Components` under each module)
- Only non-abstract classes are registered

## Default configuration

In `config/modules.php`:

```php
'blade-components' => [
    'active' => true,
    'patterns' => [
        '*/View/Components',
    ],
],
```

## Directory layout examples

Given this module structure:

```
Modules/Blog/
├── View/
│   └── Components/
│       ├── Post/
│       │   └── Card.php                // class Blog\View\Components\Post\Card extends Component
│       └── Filter.php                  // class Blog\View\Components\Filter extends Component
└── Resources/
    └── views/
        └── components/
            └── button/
                └── primary.blade.php   // anonymous component (covered in Views docs)
```

## Aliases that will be registered

- Blog\View\Components\Filter => `<x-blog::filter />`
- Blog\View\Components\Post\Card => `<x-blog::post.card />`

## Naming rules

- The module name becomes the first segment in lower-kebab-case: `Blog` => `blog`, `UserAdmin` => `user-admin`.
- Alias segments are derived from the file path under the first matching pattern, with each segment kebab-cased: `Post/Card.php` => `post.card`, `WithoutView.php` => `without-view`.
- If no path segments can be determined from any configured pattern (edge case), the alias falls back to the lowercase class basename.

## Usage examples

- Inline usage (class-with-view or class returning a view):
  ```blade
  <x-blog::filter :name="$name" />
  <x-blog::post.card :post="$post" />
  ```

- Works with any view resolution inside the component's `render()` method:
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

## Configuration options

- Toggle discovery
  - Set `'blade-components.active' => false` to disable auto-registration.
- Change discovery patterns
  - Edit `'blade-components.patterns'` to add/remove directories, relative to each module root. Wildcards are supported and resolved under `<base>/Modules` by default.

Example: custom modules directory and patterns

```php
return [
    'modules_directory' => 'Modules',        // default: project-root/Modules
    'modules_namespace' => 'Modules\\',

    'blade-components' => [
        'active' => true,
        'patterns' => [
            '*/View/Components',
            '*/Components',               // alternate layout (e.g., Modules/*/Components)
        ],
    ],
];
```

## Troubleshooting

- Component renders as unknown tag: clear compiled views (`php artisan view:clear`) and confirm the alias matches the kebab-cased path (e.g., `Post/Card.php` → `<x-blog::post.card />`).
- View not found from `render()`: ensure the view exists under the module namespace (e.g., `blog::components.post.card`) and the file is named with `.blade.php`.
- Alias collision across modules: if two classes would map to the same alias, rename one or adjust structure to keep aliases unique.
- Class not loaded: make sure the file and namespace match PSR-4 (run `composer dump-autoload` if needed).

## See also

- [Views & Anonymous Components](./views.md)

