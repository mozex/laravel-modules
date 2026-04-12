@verbatim
## mozex/laravel-modules

Laravel package that auto-discovers module assets from a `Modules/` directory at the project root. Requires PHP ^8.2 and Laravel ^10.34|^11.29|^12|^13. Optional: Livewire ^3, Filament ^3 or ^4.

This is the 2.x version. If the project uses Livewire v4 or Filament v5, it needs the 3.x version of this package instead.

Modules live in `Modules/` at the project root (NOT under `app/Modules/`), and every class inside a module uses the `Modules\{ModuleName}\...` namespace. The `Modules\\` PSR-4 mapping is added to the project's `composer.json` once during package installation; don't re-check or re-add it when creating modules.

### Module structure

Each module is a PascalCase directory. Create only the subdirectories the module needs.

```
Modules/Blog/
├── Config/*.php               merged into config() (filename = config key)
├── Console/Commands/          Artisan command classes
├── Console/Kernel.php         per-module schedule() hook (extends Mozex\Modules\Contracts\ConsoleKernel)
├── Database/Factories/        auto-mapped from models by namespace
├── Database/Migrations/       auto-registered with the migrator
├── Database/Seeders/          only {Module}DatabaseSeeder is discovered
├── Events/                    event classes
├── Filament/{Panel}/          Resources, Pages, Widgets, Clusters per panel id
├── Helpers/*.php              required in register() phase
├── Lang/                      PHP namespaced + JSON translations
├── Listeners/                 auto-discovered via handle() type hints
├── Livewire/                  class-based Livewire v3 components
├── Models/                    auto-mapped to factories/policies
├── Nova/                      Nova resources
├── Policies/                  auto-mapped from models
├── Providers/                 auto-registered service providers
├── Resources/views/           view('module::path') + anonymous components
├── Routes/                    web.php, api.php, console.php, channels.php
└── View/Components/           class-based Blade components
```

### Namespace prefixing

The module directory name is converted to kebab-case for every namespaced asset (views, Blade components, Livewire components, translations):

- `Blog` → `blog`: `view('blog::home')`, `<x-blog::card/>`, `__('blog::messages.welcome')`
- `UserAdmin` → `user-admin`
- `PWA` → `pwa` (all-caps names get lowercased without hyphens)
- `MyAPI` → `my-api` (consecutive uppercase runs stay together as acronyms)

Routes don't use this prefix. They're grouped by the route filename: `web.php` gets the `web` middleware group, `api.php` gets the `api` prefix and middleware.

### Module activation and load order

`config/modules.php` controls which modules are active and the order they load:

```php
'modules' => [
    'Shared' => ['active' => true, 'order' => 1],  // loads first
    'Blog'   => ['active' => true, 'order' => 2],
    'Legacy' => ['active' => false],                // skipped entirely
],
```

Modules not listed here default to `active: true` with order `9999`.

### Commands

- `php artisan modules:cache` - build the discovery cache (run on deploy, skip in local dev)
- `php artisan modules:clear` - clear the cache
- `php artisan modules:list` - show every module with status, order, and asset counts per type

### Detailed usage

For the full Modules facade API, custom route groups and registrars, route file organization by concern, Livewire v3 component registration, Filament v3/v4 panel mapping, model-factory-policy name guessing, config merging strategy, view overriding, event listener discovery, module scheduling, real-world service provider patterns (morph maps, third-party model policies), Inertia with Vue or React frontend setup, and PHPStan/Pest/PHPUnit setup, activate the `laravel-modules` skill. It covers the detailed implementation patterns that aren't needed on every session.
@endverbatim
