# Livewire Components

## Overview

The package discovers Livewire components inside your modules and registers them with namespaced aliases, so you can render them with `<livewire:module::path.to.component/>` or `@livewire('module::path.to.component')`.

## What gets discovered

- Classes that extend `Livewire\Component`
- Located in directories matching the configured patterns (default: `*/Livewire` under each module)
- Abstract or non‑component classes are ignored

## Default configuration

In `config/modules.php`:

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
],
```

## Aliases and naming

- Component aliases are derived from the module name and the relative path under the Livewire folder:
  - `Modules/Blog/Livewire/Posts.php` → `<livewire:blog::posts/>`
  - `Modules/Blog/Livewire/Nested/Manage/Comments.php` → `<livewire:blog::nested.manage.comments/>`
- The module name becomes the first segment in kebab‑case; subsequent path segments are kebab‑cased and dot‑joined.

## Directory layout examples

```
Modules/Blog/
└── Livewire/
    ├── Posts.php                         // <livewire:blog::posts />
    └── Nested/
        └── Manage/
            └── Comments.php              // <livewire:blog::nested.manage.comments />

Modules/Shop/
└── Livewire/
    └── ListProducts.php                  // <livewire:shop::list-products />

Modules/PWA/
└── Livewire/
    └── Icons.php                         // <livewire:pwa::icons />
```

## Usage

- In Blade:
  ```blade
  <livewire:blog::posts />
  @livewire('shop::list-products')
  <livewire:blog::nested.manage.comments />
  <livewire:pwa::icons />
  ```

## Configuration options

- Toggle discovery
  - Set `'livewire-components.active' => false` to disable auto‑registration.
- Change discovery patterns
  - Edit `'livewire-components.patterns'` to add/remove directories, relative to each module root.

## Performance and caching

- Livewire components are registered at boot. Modules cache (`php artisan modules:cache`) speeds discovery only.

## Testing hints

- Use `app(\Livewire\Mechanisms\ComponentRegistry::class)->getName(ClassName::class)` to assert registration.
- Render components in tests with `Blade::render('<livewire:module::alias/>')` and assert output.

## Troubleshooting

- Component not found:
  - Ensure it extends `Livewire\Component` and is under a configured `Livewire` directory.
  - Confirm module activation and rebuild modules cache if needed.

## See also

- [Views](./views.md)
- [Blade Components](./blade-components.md)
- [Routes](./routes.md)

