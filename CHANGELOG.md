# Changelog

All notable changes to `laravel-modules` will be documented in this file.

## 3.3.0 - 2026-04-22

### What's new

**In-memory cache layer.** Scout discovery results now live in memory for the lifetime of the PHP process. The first access in a process discovers (or reads the `modules:cache` file if one exists); every access after that returns from memory. No effect on PHP-FPM or `php artisan serve`, which start a fresh process per request. Repeat scout access becomes free on Octane, Vapor, and long-running queue workers.

**Custom cache drivers via `BaseScout::useCacheDriverFactory()`.** You can now swap the default driver at runtime with a factory closure:

```php
use Mozex\Modules\Contracts\BaseScout;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;

BaseScout::useCacheDriverFactory(
    fn (BaseScout $scout): DiscoverCacheDriver => new YourDriver($scout->cacheFile())
);

```
Any class implementing Spatie's `DiscoverCacheDriver` works. Pass `null` to restore the default. See the [caching docs](https://mozex.dev/docs/laravel-modules/v3/features/caching#custom-cache-drivers) for details.

**`Persistable` interface.** Drivers that need to separate runtime caching (`put()`) from explicit deploy-time persistence can implement `Mozex\Modules\Features\SupportCaching\Persistable`. When a driver implements it, `BaseScout::cache()` calls `persist()`; otherwise it falls back to `put()`. The default tiered driver uses this split so runtime cache warming stays in memory while `modules:cache` writes to disk.

### Changes

- Default driver is now `TieredDiscoverCacheDriver` (in-memory over file), installed by `RuntimeCache::install()` during package registration.
- `BaseScout::get()` auto-populates the cache driver on a discovery miss, so the in-memory layer warms up naturally on first read.
- `BaseScout::cache()` routes through `persist()` for drivers implementing `Persistable`, `put()` otherwise.

### Docs

- Added "Custom cache drivers" section to the caching guide and the Boost skill.

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.2.0...3.3.0

## 3.2.0 - 2026-04-11

### What's changed

#### Fixes

- **Module name kebab-casing now handles acronyms correctly.** Names with consecutive uppercase letters no longer split into individual segments. `MyAPI` → `my-api` (was `my-a-p-i`), `HTMLParser` → `html-parser`, `IOStream` → `io-stream`. All-uppercase names (`PWA`, `CRM`) and standard PascalCase (`Blog`, `UserAdmin`) are unchanged. If you have a module with consecutive uppercase letters in its name, double-check your view, Blade component, Livewire, and translation references after upgrading.
  
- **`modules:list` reports real file counts for directory-based asset types.** Migrations, views, translations, listeners, Livewire components, and Filament assets were showing `1` per module. The command now counts files recursively inside each matched directory.
  

#### New

- **Laravel Boost skill.** Ships at `resources/boost/skills/laravel-modules/SKILL.md` and loads automatically on `php artisan boost:install`. Covers the full facade API, route customization, Livewire/Filament patterns, name guessing, Inertia (Vue and React), and testing setup. The existing Boost guideline has been rewritten to stay lean and focused.

#### Documentation

- Complete rewrite of every feature page and integration guide.
- New **Inertia** integration guide covering Vue and React: Vite alias with regex, Inertia page resolver for module-scoped pages, controller usage, TypeScript paths config, typed props via Spatie TypeScript Transformer (including the `script.globalTypeFiles` trick for Vue and the plain-TypeScript flow for React), and module CSS imports.
- Updated package descriptions in `composer.json` and `context7.json`.

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.1.0...3.2.0

## 3.1.0 - 2026-03-31

* add modules:list command

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.0.4...3.1.0

## 3.0.4 - 2026-03-30

### What's Changed

* Bump ramsey/composer-install from 3 to 4 by @dependabot[bot] in https://github.com/mozex/laravel-modules/pull/21
* bump livewire dependency
* remove livewire workaround

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.0.3...3.0.4

## 3.0.3 - 2026-03-08

* Fix Route::livewire() failing with class references for components registered via addNamespace()

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.0.2...3.0.3

## 3.0.2 - 2026-03-08

* Fixed Route::livewire() not resolving module components when referenced by class (e.g., Teams::class) — workaround for
  https://github.com/livewire/livewire/pull/10076

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.0.1...3.0.2

## 3.0.1 - 2026-02-26

* Update composer.json

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/3.0.0...3.0.1

## 3.0.0 - 2026-02-25

### Laravel Modules v3.0.0

A major release that brings full Livewire v4 and Filament v5 support, adds Single-File and Multi-File Component discovery, and modernizes the platform requirements.

#### Highlights

##### Full Livewire v4 Component Support

All three Livewire v4 component types are now auto-discovered from your modules:

- **Class-based** components (as before)
- **Single-File Components (SFC)** — PHP and Blade in one `.blade.php` file
- **Multi-File Components (MFC)** — a named directory with `{name}.php` and `{name}.blade.php`

Place SFCs and MFCs in your module's `Resources/views/livewire/` directory and they're automatically registered under the module namespace:

```blade
<livewire:blog::counter />  {{-- SFC --}}
<livewire:blog::toggle />   {{-- MFC --}}


```
##### Namespace-Based Registration

Livewire component registration now uses `Livewire::addNamespace()`, aligning with Livewire v4's recommended approach for packages. This replaces the previous per-class `Livewire::component()` registration, resulting in cleaner integration and native support for all component types.

##### Filament v5 Support

Full compatibility with Filament v5. Module-based Resources, Pages, Widgets, and Clusters continue to be auto-discovered per panel.

#### Breaking Changes

- **PHP 8.3+ required** (dropped PHP 8.2)
- **Laravel 11.29+ required** (dropped Laravel 10)
- **Livewire v4 required** (dropped v3) — Composer will prevent installation with Livewire <4.0
- **Filament v5 required** (dropped v3/v4) — Composer will prevent installation with Filament <5.0
- **`LivewireComponentsScout`** changed from `ModuleClassScout` to `ModuleDirectoryScout` — scout output is now directory-based instead of class-based
- **Removed `SupportSchedules` feature** and `Mozex\Modules\Contracts\ConsoleKernel` — use `Routes/console.php` with the `Schedule` facade instead

#### New Configuration

Add `view_path` to the `livewire-components` section if you've published the config:

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
    'view_path' => 'Resources/views/livewire', // New in v3
],


```
#### Upgrading

See [UPGRADE.md](UPGRADE.md) for the full step-by-step upgrade guide.

If your application is not ready to upgrade to Livewire v4 or Filament v5, continue using the [2.x branch](https://github.com/mozex/laravel-modules/tree/2.x) which supports PHP 8.2, Laravel 10/11/12, Livewire v3, and Filament v3/v4.

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.8.0...3.0.0

## 2.8.0 - 2026-02-07

### What's Changed

* Refine docs for precision and rewrite AI guidelines for minimal token usage by @mozex in https://github.com/mozex/laravel-modules/pull/18
* Optimize performance with scout memoization and caching by @mozex in https://github.com/mozex/laravel-modules/pull/19

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.7.2...2.8.0

## 2.7.2 - 2026-01-24

### What's Changed

* Bump actions/cache from 4 to 5 by @dependabot[bot] in https://github.com/mozex/laravel-modules/pull/17
* Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/mozex/laravel-modules/pull/16
* improve docs

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.7.1...2.7.2

## 2.7.1 - 2025-11-06

* Fix AI guidelines rendering

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.7.0...2.7.1

## 2.7.0 - 2025-11-06

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.6.3...2.7.0

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 6 to 7 by @dependabot[bot] in https://github.com/mozex/laravel-modules/pull/15
* Bump actions/checkout from 4 to 5 by @dependabot[bot] in https://github.com/mozex/laravel-modules/pull/14
* Bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/mozex/laravel-modules/pull/13
* Complete documentation
* Add Laravel Boost support

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.6.3...2.7.0

## 2.6.3 - 2025-10-08

* fix event service provider

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.6.2...2.6.3

## 2.6.2 - 2025-09-23

* require amphp/parallel

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.6.1...2.6.2

## 2.6.1 - 2025-07-10

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot in https://github.com/mozex/laravel-modules/pull/12
* Make type coverage compact
* Make config loading priority
* Fix deprecated reflection values

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.6.0...2.6.1

## 2.6.0 - 2025-02-25

### What's Changed

* Add Laravel 12 compatibility by @mozex in https://github.com/mozex/laravel-modules/pull/11

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.5.2...2.6.0

## 2.5.2 - 2025-02-22

* fix guess model name for factories caused by laravel/framework#54644

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.5.1...2.5.2

## 2.5.1 - 2025-02-10

### What's Changed

* Add registerRoutesUsing to route groups
* Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/mozex/laravel-modules/pull/9
* Bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/mozex/laravel-modules/pull/10

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.5.0...2.5.1

## 2.5.0 - 2025-01-11

### What's Changed

* Improve the structure of the package
* improve class discovering

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.4.3...2.5.0

## 2.4.3 - 2024-05-17

* fix naming components

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.4.2...2.4.3

## 2.4.2 - 2024-05-05

### What's Changed

* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/mozex/laravel-modules/pull/7
* fix booting commands outside the console

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.4.1...2.4.2

## 2.4.1 - 2024-04-19

* fix registering broadcast auth route

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.4.0...2.4.1

## 2.4.0 - 2024-04-19

### What's Changed

* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/mozex/laravel-modules/pull/6
* add support for channels.php files in the routes directory

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.3.1...2.4.0

## 2.3.1 - 2024-04-11

### What's Changed

* Fix loading commands from the console.php file when routes are cached
* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/mozex/laravel-modules/pull/5

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.3.0...2.3.1

## 2.3.0 - 2024-03-14

* Add Laravel 11 commands file support

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.2.4...2.3.0

## 2.2.4 - 2024-03-03

* fix translations prefix and directory

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.2.3...2.2.4

## 2.2.3 - 2024-03-01

* fix livewire naming

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.2.2...2.2.3

## 2.2.2 - 2024-03-01

* fix views naming

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.2.1...2.2.2

## 2.2.1 - 2024-02-29

* fix filament problem with livewire component registration

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.2.0...2.2.1

## 2.2.0 - 2024-02-29

* add support for filament (resources, pages, widgets, clusters)

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.1.0...2.2.0

## 2.1.0 - 2024-02-28

* add support for defining route groups

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.0.1...2.1.0

## 2.0.1 - 2024-02-28

* add "as" for routes

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/2.0.0...2.0.1

## 2.0.0 - 2024-02-28

### What's Changed

* refactoring
* add caching support
* add support for Model name guessing
* improve Scheduling
* add support for Blade Components
* fix livewire naming
* improve routes
* improve factories
* improve policies
* improve views
* improve configs
* add tests
* add support for Laravel 11
* add support for listeners

**Full Changelog**: https://github.com/mozex/laravel-modules/compare/1.3.0...2.0.0

## 1.3.0 - 2023-11-22

- Support Service Providers

## 1.2.1 - 2023-11-16

- Fix schedule loading

## 1.2.0 - 2023-11-15

- Support Schedules

## 1.1.0 - 2023-11-13

- Add support for Policies

## 1.0.0 - 2023-10-15

Initial Release
