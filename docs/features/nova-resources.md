# Nova Resources

## Overview

The package discovers Nova resources inside your modules and registers them when Nova serves. Action resources are excluded. This lets you organize Nova resources per module without manual registration.

## What gets discovered

- Classes that extend `Laravel\Nova\Resource`
- Action resources (extending `Laravel\Nova\Actions\ActionResource`) are excluded
- Located in directories matching the configured patterns (default: `*/Nova` under each module)

## Default configuration

In `config/modules.php`:

```php
'nova-resources' => [
    'active' => true,
    'patterns' => [
        '*/Nova',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Nova/
    └── Post.php               // Modules\Blog\Nova\Post

Modules/Shop/
└── Nova/
    ├── Product.php            // Modules\Shop\Nova\Product
    └── WrongResource.php      // not extending Nova Resource (ignored)
```

## Usage

- Define Nova resource classes under `Modules/{Module}/Nova`.
- The package will register them automatically during Nova’s `serving` event.

## Configuration options

- Toggle discovery
  - Set `'nova-resources.active' => false` to disable discovery.
- Change discovery patterns
  - Edit `'nova-resources.patterns'` to add/remove directories, relative to each module root.

## Performance and caching

- Registration happens when Nova serves a request. Modules cache (`php artisan modules:cache`) speeds discovery only.

## Testing hints

- You can assert resources are present in Nova by inspecting Nova’s registered resources list in integration tests, or rely on the unit tests included in this package for discovery behavior.

## Troubleshooting

- Resource not registered:
  - Ensure it extends `Laravel\Nova\Resource` (and not `ActionResource`).
  - Confirm the file is under a configured `Nova` directory and the feature is active.

## See also

- [Views](./views.md)
- [Routes](./routes.md)
- [Models & Factories](./models-factories.md)

