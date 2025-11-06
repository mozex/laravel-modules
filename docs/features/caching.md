# Caching

## Overview

Speed up module discovery with two Artisan commands. Discovery caching stores the locations and metadata for all active module assets to avoid scanning on each request.

## Commands

- Build the cache
  ```bat
  php artisan modules:cache
  ```

- Clear the cache
  ```bat
  php artisan modules:clear
  ```

## What is cached

- The discovery metadata for each active feature (views, routes, blade components, helpers, migrations, models/factories, policies, listeners, events discovery paths, livewire components, filament assets, nova resources, configs, translations, service providers, commands, schedules).
- Your application code (classes, views) is not modified; this only affects how quickly assets are found.

## When to rebuild

- After adding, renaming, or moving module files or directories.
- After changing discovery patterns in `config/modules.php`.
- Before deploying to production (recommended).

## Troubleshooting

- Cache didn’t change behavior: ensure the feature you’re using is active and that new files match configured patterns.
- Error during cache: fix any PHP errors in newly added classes/files first; caching requires loading classes.

## See also

- [Routes](./routes.md)
- [Views](./views.md)
- [Service Providers](./service-providers.md)

