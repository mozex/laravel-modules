# Nova Resources

## Overview

Discovers Nova resources inside modules and registers them during Nova's `serving` event. Classes extending `Laravel\Nova\Actions\ActionResource` are excluded.

## Default configuration

```php
'nova-resources' => [
    'active' => true,
    'patterns' => [
        '*/Nova',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Nova/
    └── Post.php               // extends Laravel\Nova\Resource (discovered)

Modules/Shop/
└── Nova/
    └── Product.php            // discovered
```

## Usage

Define Nova resource classes under `Modules/{Module}/Nova`. They are registered automatically.

## Configuration

- Set `'nova-resources.active' => false` to disable discovery.
- Edit `'nova-resources.patterns'` to change discovery directories.

## See also

- [Models & Factories](./models-factories.md)
- [Policies](./policies.md)
