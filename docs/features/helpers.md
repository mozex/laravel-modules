---
title: Helpers
weight: 8
---

Helper files are PHP files containing global functions. The package discovers them and loads them via `require_once` in the service provider's `register()` phase, before Laravel's boot phase runs. That makes the helper functions available everywhere in your application from the earliest possible point.

## Default configuration

```php
'helpers' => [
    'active' => true,
    'patterns' => [
        '*/Helpers/*.php',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Helpers/
    ├── formatting.php
    └── strings.php

Modules/Shop/
└── Helpers/
    └── pricing.php
```

## Writing helper files

Helper files are plain PHP files that define functions. Always wrap each function in a `function_exists()` check to prevent redeclaration errors if multiple modules define the same helper name:

```php
// Modules/Blog/Helpers/formatting.php

if (! function_exists('format_reading_time')) {
    function format_reading_time(string $text): string
    {
        $words = str_word_count(strip_tags($text));
        $minutes = max(1, (int) ceil($words / 200));

        return "{$minutes} min read";
    }
}

if (! function_exists('excerpt')) {
    function excerpt(string $text, int $limit = 150): string
    {
        return Str::limit(strip_tags($text), $limit);
    }
}
```

After discovery, call them anywhere in your application:

```php
$readTime = format_reading_time($post->body);
$summary = excerpt($post->body, 200);
```

## Load order

Helpers load in module order, and within each module, files load in the order the glob pattern returns them (typically alphabetical). If two modules define the same function name, the `function_exists()` guard means the first one loaded wins.

If you need a specific module's helpers to load first (for example, a `Shared` module with common utilities), set that module's `order` to a low number in the `modules` config.

## When to use helpers vs. other approaches

Helper files work well for small, stateless utility functions that don't need dependency injection. For anything that depends on services, config values, or application state, a service class or a facade is a better fit. Helpers are global and can't be mocked in tests without extra effort.

## Disabling

Set `'helpers.active' => false` to stop loading helper files. Adjust `'helpers.patterns'` to change which files are discovered.
