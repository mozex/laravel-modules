# Configs

## Overview

The package auto-discovers module config files and merges them into Laravel’s configuration at boot (when the app config is not cached). Merging order is configurable, so you can choose whether module values override the app config or act as defaults.

## What gets discovered

- Files matching the configured patterns (default: `*/Config/*.php` under each module)
- Each file’s base filename becomes the config key (e.g., `Modules/Blog/Config/blog.php` → `config('blog.*')`)

## Default configuration

In `config/modules.php`:

```php
'configs' => [
    'active' => true,
    'patterns' => [
        '*/Config/*.php',
    ],
    'priority' => true, // when true, module values override app values
],
```

## Merging strategy

Merging only runs when Laravel configuration is not cached.

- If `priority` is true (default):
  - final = array_merge(app[$key] ?? [], module[$key])
  - module values win for duplicate keys.
- If `priority` is false:
  - final = array_merge(module[$key], app[$key] ?? [])
  - app values win; module files provide defaults.

Example

- `config/app.php` contains:
  ```php
  'feature' => [
      'enabled' => false,
      'limit' => 10,
  ]
  ```
- `Modules/Shop/Config/app.php` contains:
  ```php
  return [
      'feature' => [
          'enabled' => true,
      ],
  ];
  ```
- With `priority: true`, `config('app.feature.enabled')` becomes `true` and `config('app.feature.limit')` remains `10`.

## Directory layout examples

```
Modules/Blog/
└── Config/
    └── blog.php          // config('blog.*')

Modules/Shop/
└── Config/
    └── app.php           // config('app.*') merged with Laravel app config
```

## Usage examples

- Read merged values as usual:
  ```php
  if (config('blog.comments.enabled')) {
      // ...
  }
  $limit = config('app.feature.limit', 10);
  ```

## Configuration options

- Toggle discovery
  - Set `'configs.active' => false` to disable merging.
- Change discovery patterns or priority
  - Edit `'configs.patterns'` to add/remove directories.
  - Set `'configs.priority'` to tune who wins on conflicts.

## Performance and caching

- Merging is skipped when the app configuration is cached (`php artisan config:cache`).
- Modules asset caching (`php artisan modules:cache`) only optimizes discovery; it does not affect merge results.

## Testing hints

- Assert expected values:
  ```php
  expect(config('blog.x'))->toBe('...');
  ```
- Flip `'configs.priority'` and verify override direction in tests.

## Troubleshooting

- Value not merging:
  - Ensure the file path matches a configured pattern and the feature is active.
  - Clear Laravel config cache if enabled.
- Unexpected precedence:
  - Check the `'configs.priority'` flag.

## See also

- [Routes](./routes.md)
- [Views](./views.md)
- [Blade Components](./blade-components.md)

