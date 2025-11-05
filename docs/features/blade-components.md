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

## Performance and caching
- Build cache: `php artisan modules:cache`
- Clear cache: `php artisan modules:clear`

## Testing hints

- You can assert registration via `Blade::getClassComponentAliases()` or render the components in a small view.
- If you rename or move components, rebuild the cache.

## Troubleshooting

- Component not found:
  - Ensure it extends `Illuminate\View\Component` and is not abstract.
  - Ensure the file is under one of the configured `patterns`.
  - Check per-module activation in `config/modules.php` under `'modules' => ['YourModule' => ['active' => true]]`.
  - Rebuild cache with `php artisan modules:cache`.
- Alias differs from expectation:
  - Verify the calculated alias from the folder structure and kebab-case rules above.
  - You can always reference the component class directly if needed: `Blade::component(YourClass::class, 'your-alias')` in a service provider.

## See also

- [Views & Anonymous Components](./views.md)
