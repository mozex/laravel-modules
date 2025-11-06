# Translations

## Overview

Provide module-specific language lines under each module and use them via namespaced keys or JSON translations. The package discovers translation directories and registers both PHP array and JSON paths for Laravel’s translator.

## What gets discovered

- Directories matching the configured patterns (default: `*/Lang` under each module)
- PHP array files (e.g., `en/messages.php`) are loaded under a module namespace
- JSON files (e.g., `en.json`) are added as JSON translation paths

## Default configuration

In `config/modules.php`:

```php
'translations' => [
    'active' => true,
    'patterns' => [
        '*/Lang',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Lang/
    ├── en/
    │   ├── auth.php                  // return [...]
    │   └── messages.php              // return [...]
    ├── tr/
    │   └── messages.php              // return [...]
    └── en.json                       // { "Welcome": "Welcome" }

Modules/Shop/
└── Lang/
    └── en/
        └── cart.php                  // return [...]
```

## Usage

- PHP array translations: use the module namespace (kebab‑case of the module name) + file + key
  ```php
  __('blog::messages.welcome')
  trans('shop::cart.added')
  ```

- JSON translations: available via `__('Text')` from any module if a matching JSON entry exists in module `Lang/*.json`.

## Configuration options

- Toggle discovery
  - Set `'translations.active' => false` to disable translation loading.
- Change discovery patterns
  - Edit `'translations.patterns'` to add/remove directories, relative to each module root.

## Troubleshooting

- Key not found: verify the namespace and path mapping. Example: `Modules/Blog/Lang/en/messages.php` → `__('blog::messages.key')`.
- Wrong locale: ensure your app locale matches the folder/file (e.g., `en/`, `tr/`) or provide fallbacks.
- JSON not loading: place JSON files directly under the module `Lang` directory (e.g., `Modules/Blog/Lang/en.json`).

## See also

- [Views](./views.md)
- [Service Providers](./service-providers.md)

