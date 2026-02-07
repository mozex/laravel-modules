# Caching

## Overview

Speed up module discovery with two Artisan commands. Discovery caching stores the locations and metadata for all active module assets to avoid scanning on each request.

## Commands

- Build the cache:
  ```bash
  php artisan modules:cache
  ```

- Clear the cache:
  ```bash
  php artisan modules:clear
  ```

## What is cached

The discovery metadata for each active feature: views, routes, blade components, helpers, migrations, listeners, livewire components, filament assets, nova resources, configs, translations, service providers, commands, and schedules.

## When to rebuild

- After adding, renaming, or moving module files or directories.
- After changing discovery patterns in `config/modules.php`.
- Before deploying to production (recommended).

## Troubleshooting

- **Cache didn't change behavior**: ensure the feature is active and new files match configured patterns.
- **Error during cache**: fix PHP errors in newly added classes first; caching requires loading classes.
