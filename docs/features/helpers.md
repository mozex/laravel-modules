# Helpers

## Overview

The package auto-discovers PHP helper files within your modules and requires them once at registration time. This makes your helper functions globally available across the application.

## What gets discovered

- Files matching the configured patterns (default: `*/Helpers/*.php` under each module)
- Each discovered file is `require`d once during the container registration phase

## Default configuration

In `config/modules.php`:

```php
'helpers' => [
    'active' => true,
    'patterns' => [
        '*/Helpers/*.php',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Helpers/
    ├── formatting.php        // defines blog-specific helpers
    └── strings.php           // defines extra string helpers

Modules/Shop/
└── Helpers/
    └── pricing.php           // defines shop-specific helpers
```

## Usage

- Define helpers in plain PHP files and guard them to avoid redeclaration:
  ```php
  if (! function_exists('format_price')) {
      function format_price(int $cents): string { /* ... */ }
  }
  ```
- After discovery, call them anywhere:
  ```php
  $label = format_price(1999);
  ```

## Configuration options

- Toggle discovery
  - Set `'helpers.active' => false` to disable requiring helper files.
- Change discovery patterns
  - Edit `'helpers.patterns'` to add/remove directories, relative to each module root.

## Performance and caching

- Helpers are required once at registration time; modules cache (`php artisan modules:cache`) speeds discovery only.

## Testing hints

- Assert helper functions are available in tests by calling them directly.
- To avoid state bleed, prefer pure helper functions; guard with `function_exists`.

## Troubleshooting

- Function not found:
  - Ensure the file path matches a configured pattern and the feature is active.
  - Confirm the function is not conditionally defined behind a failing guard.

## See also

- [Commands](./commands.md)
- [Configs](./configs.md)
- [Routes](./routes.md)

