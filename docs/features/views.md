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

## Namespace and naming

- Module name → namespace (kebab-case):
  - `Blog` → `blog`
  - `Shop` → `shop`
  - `PWA` (all caps) → `pwa`
- A view file at `Modules/Blog/Resources/views/home.blade.php` is used via `view('blog::home')`.
- Nested paths map to dot notation: `Modules/Shop/Resources/views/pages/product/show.blade.php` → `view('shop::pages.product.show')`.

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

- Blade includes:
  ```blade
  @include('invoice::inc.view')
  {{-- loads Modules/Invoice/Resources/views/inc/view.blade.php --}}
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

## Troubleshooting

- View not found: verify the namespaced key matches the file path (kebab-case module name + dot path). Example: `Modules/Shop/Resources/views/catalog/index.blade.php` → `shop::catalog.index`.
- Anonymous component not found: ensure it lives under `Resources/views/components` and you’re using the `<x-module::...>` path (kebab-cased and dot-joined).
- Wrong module namespace: the module alias is the kebab-case of the module directory name (e.g., `Blog` → `blog`).

## See also

- [Blade Components](./blade-components.md)
