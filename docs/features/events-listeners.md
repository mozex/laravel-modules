---
title: "Events & Listeners"
weight: 11
---

Laravel can auto-discover event listeners by scanning directories for classes that type-hint events in their `handle()` method. This feature extends that scanning to include module `Listeners/` directories, with a custom class name resolver that understands module namespaces.

Events can live anywhere (they're just classes). The discovery only applies to listeners, since listeners need to be registered with the event dispatcher.

## Default configuration

```php
'listeners' => [
    'active' => true,
    'patterns' => [
        '*/Listeners',
    ],
],
```

## How it works

When this feature is active, two things happen:

1. Laravel's `shouldDiscoverEvents()` returns `true`, enabling automatic event discovery.
2. The directories to scan are extended to include each module's `Listeners/` directory (from the listeners scout).

For listener files under the modules root, the package provides a custom class name resolver. It converts the file path to a fully-qualified class name using the module namespace (`Modules/Blog/Listeners/NotifyFollowers.php` becomes `Modules\Blog\Listeners\NotifyFollowers`). For non-module listener files, Laravel's default resolver handles the conversion.

## Directory layout

```
Modules/Blog/
├── Events/
│   ├── PostPublished.php
│   └── PostDeleted.php
└── Listeners/
    ├── NotifyFollowers.php
    └── Analytics/
        └── TrackPageView.php

Modules/Shop/
├── Events/
│   └── OrderPlaced.php
└── Listeners/
    ├── SendReceipt.php
    ├── UpdateInventory.php
    └── Notifications/
        └── NotifyWarehouse.php
```

## Writing events and listeners

Events are plain classes. They don't need any special base class or interface:

```php
namespace Modules\Blog\Events;

class PostPublished
{
    public function __construct(
        public Post $post,
    ) {}
}
```

Listeners are classes with a `handle()` method that type-hints the event:

```php
namespace Modules\Blog\Listeners;

use Modules\Blog\Events\PostPublished;

class NotifyFollowers
{
    public function handle(PostPublished $event): void
    {
        // Send notifications to the post author's followers
        $event->post->author->followers->each(function ($follower) use ($event) {
            $follower->notify(new NewPostNotification($event->post));
        });
    }
}
```

Laravel's auto-discovery reads the type-hint on `handle()` and wires the connection automatically. You don't need to register the event-listener mapping anywhere.

## Cross-module listeners

A listener in one module can handle events from another module (or from the application itself). The event type-hint is what matters, not which module the listener lives in:

```php
namespace Modules\Shop\Listeners;

use Modules\Blog\Events\PostPublished;

class IndexPostForSearch
{
    public function handle(PostPublished $event): void
    {
        SearchIndex::update($event->post);
    }
}
```

## Dispatching events

Dispatch events using Laravel's standard approach:

```php
use Modules\Blog\Events\PostPublished;

// Using the event() helper
event(new PostPublished($post));

// Using the Event facade
Event::dispatch(new PostPublished($post));

// Using the static dispatch method
PostPublished::dispatch($post);
```

## Nested listener directories

Subdirectories inside `Listeners/` are scanned recursively. A listener at `Listeners/Analytics/TrackPageView.php` is discovered and mapped to the namespace `Modules\Blog\Listeners\Analytics\TrackPageView`.

## Disabling

Set `'listeners.active' => false` to disable event listener discovery for modules entirely. This also disables the `shouldDiscoverEvents()` override, so Laravel falls back to its default discovery behavior (which only scans the application's `Listeners/` directory).
