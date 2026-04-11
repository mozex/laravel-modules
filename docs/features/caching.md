---
title: Caching
weight: 14
---

The package scans your modules directory on every request to discover assets. In development, that's fine. In production, you can cache the discovery results so the scanning only happens once, during deployment.

## Commands

Build the discovery cache:

```bash
php artisan modules:cache
```

This scans all active modules and writes the results to individual cache files in `bootstrap/cache/`. Each asset type gets its own cache file (for example, `bootstrap/cache/modules-views.php`, `bootstrap/cache/modules-routes.php`).

Clear the discovery cache:

```bash
php artisan modules:clear
```

This deletes all module cache files. The next request will trigger fresh discovery.

## What gets cached

Every active feature's discovery results: the file paths, class names, namespaces, and metadata for all module assets. This includes configs, service providers, helpers, commands, migrations, seeders, translations, views, Blade components, routes, listeners, Livewire components, Filament resources/pages/widgets/clusters, and Nova resources.

The cache stores the computed discovery data, not the raw files. When the cache exists, the package skips directory scanning and class reflection entirely, loading the pre-built arrays directly.

## When to rebuild

Rebuild the cache whenever the discovery results would change:

- After adding, renaming, moving, or deleting module files or directories
- After changing discovery patterns in `config/modules.php`
- After enabling or disabling modules
- After adding or removing a module entirely

In a typical deployment workflow, run `modules:cache` as part of your deploy script, right alongside `config:cache`, `route:cache`, and `view:cache`:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan modules:cache
```

## Development workflow

Don't cache during development. Without a cache, the package re-scans on every request, so new files, renamed classes, and changed directory structures are picked up immediately.

If you accidentally cached during development and things seem stale, run `modules:clear` to get back to live discovery.

## Cache file format

Cache files are plain PHP arrays stored in `bootstrap/cache/`. Each file is named `modules-{asset-type}.php` (for example, `modules-blade-components.php`). The files are fast to load because PHP can opcache them, and they don't require any serialization or deserialization.

## Troubleshooting

**New files aren't being discovered**: You probably have a stale cache. Run `php artisan modules:clear` and then `php artisan modules:cache` if you're in production, or just `modules:clear` if you're developing locally.

**Error during caching**: The cache command loads and reflects on your module classes. If a class has a syntax error or missing dependency, caching will fail. Fix the PHP error first, then retry.
