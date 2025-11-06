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

## Troubleshooting

- Function not found: verify the file path is under a discovered `Helpers` directory and the feature is active.
- Redeclaration error: wrap helpers with `function_exists` guards to avoid duplicate definitions across modules.

## See also

- [Commands](./commands.md)
- [Configs](./configs.md)
- [Views](./views.md)
