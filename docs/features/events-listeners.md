# Events & Listeners

## Overview

The package enables Laravel’s event discovery for modules and adds a module‑aware class resolver so your listeners are automatically found and attached to events. Define events and listeners inside each module and let the framework wire them—no manual mappings required.

## What gets discovered

- Event discovery is enabled whenever the Listeners feature is active.
- The directories searched for event discovery come from the Listeners discovery (default: `*/Listeners`).
- Listener class names are derived from file paths using a module‑aware resolver; non‑module files fall back to Laravel’s default resolver.

## Default configuration

In `config/modules.php`:

```php
'listeners' => [
    'active' => true,
    'patterns' => [
        '*/Listeners',
    ],
],
```

## How discovery works

- Listener class resolution:
  - For files under your modules root, the class name is computed from the file path relative to the project base (separators → namespace separators); e.g., `Modules/Blog/Listeners/NotifyFollowers.php` → `Modules\Blog\Listeners\NotifyFollowers`.
  - For non‑module files, the default `DiscoverEvents` logic is used.

## Directory layout examples

```
Modules/Blog/
├── Events/
│   └── PostPublished.php
└── Listeners/
    └── NotifyFollowers.php           // Modules\Blog\Listeners\NotifyFollowers

Modules/Shop/
├── Events/
│   └── OrderPlaced.php
└── Listeners/
    ├── SendReceipt.php               // Modules\Shop\Listeners\SendReceipt
    └── Analytics/
        └── TrackPurchase.php         // Modules\Shop\Listeners\Analytics\TrackPurchase
```

## Usage

- Define events per module (e.g., `Modules\\Blog\\Events\\PostPublished`).
- Place listener classes under each module’s `Listeners` directory.
- Laravel will discover and attach them at boot; you can assert this with:

> Note: Module listeners can listen to events defined anywhere in your application (core app or other modules); the event does not have to live in the same module.

```php
Event::assertListening(Modules\\Blog\\Events\\PostPublished::class, Modules\\Blog\\Listeners\\NotifyFollowers::class);
```

## Configuration options

- Toggle discovery
  - Set `'listeners.active' => false` to disable event listener discovery (and event discovery).
- Change discovery patterns
  - Edit `'listeners.patterns'` to add/remove directories per module.

## Performance and caching

- Discovery runs during boot. Modules cache (`php artisan modules:cache`) accelerates discovery only.

## Testing hints

- Use `Event::fake()` and `Event::assertListening(...)` to verify attachments.
- Fire module events in feature tests and assert that listeners handled them.

## Troubleshooting

- Listener not attached:
  - Ensure the file lives under a configured `Listeners` path and the feature is active.
  - Confirm the resolved namespace matches the file path and class name.

## See also

- [Policies](./policies.md)
- [Models & Factories](./models-factories.md)
- [Routes](./routes.md)
