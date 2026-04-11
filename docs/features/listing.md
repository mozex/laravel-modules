---
title: Listing Modules
weight: 15
---

The `modules:list` Artisan command shows every module in your project with its status, load order, and a count of discovered assets by type.

## Usage

```bash
php artisan modules:list
```

The output groups information by module, sorted by load order. Each module's header shows whether it's enabled or disabled and its configured order number. Below that, a table lists only the asset types where at least one item was found.

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

Disabled modules show "No assets discovered" because the package skips them entirely during scanning.

## When it's useful

**After creating a new module**: Run the command to confirm the package is picking it up. If the module doesn't appear, check that the directory is inside the configured modules directory and that the module isn't set to `active: false`.

**When something isn't loading**: If a route file, view directory, or command class isn't working, check the list output. A zero count (or a missing asset type row) tells you the discovery scanner didn't find anything matching the configured patterns for that feature.

**For an overview of your project**: On a project with many modules, the command gives you a quick summary of what each module contains without having to browse the file system.

## Related commands

- `php artisan modules:cache` caches the discovery results for production use. See [Caching](./caching).
- `php artisan modules:clear` removes the discovery cache so the next request scans fresh.
