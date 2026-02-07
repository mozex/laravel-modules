# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`mozex/laravel-modules` is a zero-config Laravel package that auto-discovers module assets from a `Modules/` directory. It supports configs, routes, views, Blade components, translations, helpers, commands, service providers, migrations, seeders, models, factories, policies, events, listeners, Livewire, Filament, Nova, and schedules.

## Commands

```bash
# Full test suite (lint + rector + phpstan + type-coverage + pest)
composer test

# Individual
composer test:unit              # pest tests
composer test:lint              # pint --test (check only)
composer test:types             # phpstan analyse
composer test:type-coverage     # pest --type-coverage --min=100
composer test:refactor          # rector --dry-run

# Run single test
vendor/bin/pest --filter="test name"
vendor/bin/pest tests/path/to/Test.php
vendor/bin/pest src/Features/SupportRoutes/UnitTest.php

# Fix code style / apply refactoring
composer lint                   # pint (fix)
composer refactor               # rector + pint
```

## Architecture

### Core flow

`ModulesServiceProvider` (extends Spatie's `PackageServiceProvider`) registers config, two artisan commands (`modules:cache`, `modules:clear`), then registers all feature service providers in a fixed order via `getFeatures()`.

### Feature pattern

Every feature lives in `src/Features/Support{Name}/` and contains exactly three files:

| File | Role |
|------|------|
| `{Name}Scout.php` | Discovery — extends `BaseScout` (via `ModuleClassScout`, `ModuleFileScout`, or `ModuleDirectoryScout`) |
| `{Name}ServiceProvider.php` | Registration — extends `Feature` (which extends Laravel `ServiceProvider`) |
| `UnitTest.php` | Pest tests — discovered via `tests/Pest.php` which scans `src/Features` |

**Discovery pipeline:** `AssetType` enum → `scout()` → glob/class-scan → `transform()` (sorts by module order, filters inactive) → `collect()` → feature provider registers assets with Laravel.

### Scout hierarchy

```
BaseScout (template: get/cache/clear/transform)
├── ModuleClassScout    → Spatie StructureDiscoverer (parallel PHP class scanning)
├── ModuleFileScout     → glob for files
└── ModuleDirectoryScout → glob for directories (GLOB_ONLYDIR)
```

### Key types

- **`AssetType` enum** (`src/Enums/AssetType.php`): Maps each feature to its config key, scout, patterns, and active state. Central registry for all discoverable asset types.
- **`Modules` service** (`src/Modules.php`): Path helpers, module name extraction, route group registration, seeders API. Accessed via the `Modules` facade.
- **`Feature` base** (`src/Features/Feature.php`): Provides `getName()` (directory → kebab alias) and `getViewName()` (asset → namespaced view name).

### Config-driven

Each feature section in `config/modules.php` has `active` (bool) and `patterns` (glob array). Features check `AssetType::isDeactive()` early-return in boot. Per-module `active`/`order` keys control which modules load and in what order.

### Caching

Scouts use `FileDiscoverCacheDriver` writing to `bootstrap/cache/modules-{asset-type}.php`. The `modules:cache` command calls `cache()` on all active scouts; `modules:clear` calls `clear()`.

## Testing

- **Framework**: Orchestra Testbench with Workbench (mini Laravel app in `workbench/`)
- **Test modules**: `workbench/Modules/{First,Second,PWA,Disabled}/` — First (order 1), Second (order 2), PWA (all-caps naming), Disabled (active: false)
- **Test location**: `tests/` for general tests, `src/Features/*/UnitTest.php` for feature tests (both scanned by Pest.php)
- **Common pattern**: Each feature is tested with and without cache via `->with(['without cache' => false, 'with cache' => true])`
- **Arch tests**: `tests/ArchTest.php` forbids dd, dump, ray, die, var_dump, print_r
- **100% type coverage** is enforced (`--min=100`)

## CI matrix

Tests run on PHP 8.2/8.3/8.4 × Laravel 10/11/12 × prefer-lowest/prefer-stable on both Ubuntu and Windows.

## Adding a new feature

1. Create `src/Features/Support{Name}/` with Scout, ServiceProvider, and UnitTest
2. Add a case to `AssetType` enum with its scout mapping
3. Add config section in `config/modules.php` (active + patterns)
4. Register the service provider in `ModulesServiceProvider::getFeatures()` (order matters)
5. Add test module fixtures in `workbench/Modules/` as needed
6. Create documentation in `docs/features/{name}.md`
