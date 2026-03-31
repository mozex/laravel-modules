# Listing Modules

## Overview

The `modules:list` command gives you a quick overview of every module in your project: which ones are active, their load order, and how many assets each one has discovered.

## Usage

```bash
php artisan modules:list
```

Each module is displayed as its own section, sorted by load order. The header shows the module name, status (Enabled/Disabled), and configured order. Below it, a compact table lists only the asset types that have at least one discovery.

Example output:

```
  Shared [Enabled | Order: 1]
  +---------------------+-------+
  | Asset               | Count |
  +---------------------+-------+
  | Helpers             | 3     |
  | Service Providers   | 1     |
  | Configs             | 2     |
  +---------------------+-------+

  Blog [Enabled | Order: 2]
  +---------------------+-------+
  | Asset               | Count |
  +---------------------+-------+
  | Commands            | 1     |
  | Migrations          | 4     |
  | Views               | 8     |
  | Routes              | 2     |
  | Blade Components    | 3     |
  +---------------------+-------+

  Legacy [Disabled]
  No assets discovered
```

Disabled modules show no assets because discovery skips them entirely.

## When to use

- After adding a new module, to confirm it's being picked up.
- When debugging missing assets (a zero count, or missing asset type, tells you the scout found nothing).
- To get a bird's-eye view of how your modules are structured.

## Related commands

- [`modules:cache`](./caching.md) caches discovery results for production.
- [`modules:clear`](./caching.md) clears the discovery cache.
