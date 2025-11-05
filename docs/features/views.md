# Views

## Overview

The package auto-discovers view directories within your modules and registers each module as a view namespace. You can render views using the `module::path.to.view` syntax anywhere in your app. It also supports anonymous Blade components placed under `Resources/views/components`, available via the `<x-module::...>` syntax.

## What gets discovered

- Directories matching the configured patterns (default: `*/Resources/views` under each module)
- The view namespace is the kebab-case of the module name (e.g., `Blog` → `blog`, `Shop` → `shop`, `PWA` → `pwa`)

## Default configuration

In `config/modules.php`:

```php
'views' => [
    'active' => true,
    'patterns' => [
        '*/Resources/views',
    ],
],
```

## Namespace and naming

- Module name → namespace (kebab-case):
  - `Blog` → `blog`
  - `Shop` → `shop`
  - `PWA` (all caps) → `pwa`
- A view file at `Modules/Blog/Resources/views/home.blade.php` is used via `view('blog::home')`.
- Nested paths map to dot notation: `Modules/Shop/Resources/views/pages/product/show.blade.php` → `view('shop::pages.product.show')`.

## Directory layout examples

```
Modules/Blog/
└── Resources/
    └── views/
        ├── home.blade.php              // view('blog::home')
        └── components/
            ├── filter.blade.php        // <x-blog::filter />
            └── form/
                └── input.blade.php     // <x-blog::form.input />

Modules/Shop/
└── Resources/
    └── views/
        ├── catalog/
        │   └── index.blade.php         // view('shop::catalog.index')
        ├── pages/
        │   └── product/
        │       └── show.blade.php      // view('shop::pages.product.show')
        └── components/
            ├── checkbox.blade.php      // <x-shop::checkbox />
            └── button/
                └── checkout.blade.php  // <x-shop::button.checkout />

Modules/PWA/
└── Resources/
    └── views/
        ├── head.blade.php              // view('pwa::head')
        └── components/
            └── manifest.blade.php      // <x-pwa::manifest />
```

## Anonymous components

Anonymous Blade components placed under `Resources/views/components` are available via `<x-module::...>`:

- `Modules/Blog/Resources/views/components/form/input.blade.php` → `<x-blog::form.input />`
- `Modules/Shop/Resources/views/components/checkbox.blade.php` → `<x-shop::checkbox />`
- `Modules/Shop/Resources/views/components/button/checkout.blade.php` → `<x-shop::button.checkout />`
- `Modules/PWA/Resources/views/components/manifest.blade.php` → `<x-pwa::manifest />`

## Usage examples

- Views:
  ```php
  echo view('blog::home');
  echo view('shop::catalog.index');
  echo view('shop::pages.product.show');
  echo view('pwa::head');
  ```

- Anonymous components:
  ```blade
  <x-blog::form.input />
  <x-shop::checkbox />
  <x-shop::button.checkout />
  <x-pwa::manifest />
  ```

## Configuration options

- Toggle discovery
  - Set `'views.active' => false` to disable view namespace registration.
- Change discovery patterns
  - Edit `'views.patterns'` to add/remove directories, relative to each module root. Wildcards are supported and resolved under `<base>/Modules` by default.

## Performance and caching

- Discovered view directories participate in the modules cache commands:
  - Build cache: `php artisan modules:cache`
  - Clear cache: `php artisan modules:clear`

## See also

- [Blade Components](./blade-components.md)
