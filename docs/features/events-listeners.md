# Events & Listeners

## Overview

Enables Laravel's event discovery for modules with a module-aware class resolver. Listeners are automatically found and attached to events — no manual mappings required.

## How it works

- Event discovery is enabled whenever the Listeners feature is active.
- Directories searched come from the Listeners config (default: `*/Listeners`).
- For files under the modules root, the class name is computed from the file path relative to the project base (e.g., `Modules/Blog/Listeners/NotifyFollowers.php` → `Modules\Blog\Listeners\NotifyFollowers`).
- For non-module files, Laravel's default `DiscoverEvents` resolver is used.

## Default configuration

```php
'listeners' => [
    'active' => true,
    'patterns' => [
        '*/Listeners',
    ],
],
```

## Directory layout

```
Modules/Blog/
├── Events/
│   └── PostPublished.php
└── Listeners/
    └── NotifyFollowers.php

Modules/Shop/
├── Events/
│   └── OrderPlaced.php
└── Listeners/
    ├── SendReceipt.php
    └── Analytics/
        └── TrackPurchase.php
```

## Usage

Place listener classes under each module's `Listeners` directory. Laravel discovers and attaches them at boot. Listeners can handle events from any module or the core app.

```php
Event::assertListening(
    Modules\Blog\Events\PostPublished::class,
    Modules\Blog\Listeners\NotifyFollowers::class
);
```

## Configuration

- Set `'listeners.active' => false` to disable event/listener discovery entirely.
- Edit `'listeners.patterns'` to change discovery directories.

## Troubleshooting

- **Listener not attached**: ensure the file is under a configured `Listeners` path and the feature is active.
- **Namespace mismatch**: the resolved namespace must match the file path and class name.
