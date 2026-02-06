@verbatim
## mozex/laravel-modules

Auto-discovers module assets from `Modules/`. Namespace: `Modules\{Module}\...`
Config: `config/modules.php`. Cache: `php artisan modules:cache` / `modules:clear`.
Docs: `./docs/**` (Boost search-docs excludes this package).

### Module config
```php
'modules' => [
    'Shared' => ['active' => true, 'order' => 1], // lower=earlier
    'Legacy' => ['active' => false],               // fully disabled
],
```

### Structure & auto-discovery
Each feature has `active` (bool) + `patterns` (globs) in config/modules.php.
```
Modules/{Module}/
  Config/*.php                    → config('filename.key'); priority: true=module wins
  Console/Commands/               → Artisan commands (extends Command)
  Console/Kernel.php              → schedule() (extends Mozex\Modules\Contracts\ConsoleKernel)
  Database/Factories/             → Model↔Factory auto-mapping by namespace
  Database/Migrations/            → registered with migrator
  Database/Seeders/               → only {Module}DatabaseSeeder; via Modules::seeders()
  Filament/{PanelId}/Resources|Pages|Widgets|Clusters/  → per-panel (dir=panel id lowercase)
  Helpers/*.php                   → require_once in register(); guard with function_exists
  Lang/                           → __('module::file.key') + JSON translations
  Listeners/                      → Laravel event auto-discovery
  Livewire/                       → <livewire:module::name /> or nested.path
  Models/                         → factory/policy guessing by namespace
  Nova/                           → Nova\Resource subclasses (excl. ActionResource)
  Policies/                       → Models\X → Policies\XPolicy auto-mapping
  Providers/                      → ServiceProvider subclasses auto-registered in register()
  Resources/views/                → view('module::path') + <x-module::component />
  Routes/*.php                    → grouped by filename; channels.php, console.php special
  View/Components/                → <x-module::path.component />
```

### Naming
Module dir → kebab-case alias: Blog→blog, UserAdmin→user-admin, PWA→pwa (all-caps→lowercase).

### Facade (Mozex\Modules\Facades\Modules)
```php
Modules::basePath('suffix')                     // project base + suffix
Modules::modulesPath('Blog/Config')             // Modules dir + suffix
Modules::moduleNameFromNamespace($fqcn)         // → 'Blog'
Modules::moduleNameFromPath($path)              // → 'Blog'
Modules::seeders()                              // all {Module}DatabaseSeeder classes
Modules::routeGroup('name', prefix:, middleware:, as:)
Modules::registerRoutesUsing('name', Closure)   // custom route registrar
Modules::setBasePath('/path')                   // test override
```

### Routes detail
Defaults: 'api' (prefix:'api', mw:['api']), 'web' (mw:['web']). Filename=group key.
Unmatched filenames → no middleware/prefix. Attributes accept closures.
`channels.php` → broadcast channels. `console.php` → console kernel (Laravel 10+).
Custom: call `Modules::routeGroup()` / `Modules::registerRoutesUsing()` in provider register().

### Config keys
| Key | Default | Purpose |
|-----|---------|---------|
| modules_directory | Modules | root dir |
| modules_namespace | Modules\\ | PSR-4 prefix |
| configs.priority | true | true=module overrides app |
| routes.commands_filenames | ['console'] | console route files |
| routes.channels_filenames | ['channels'] | channel files |
| models.namespace | Models\\ | model sub-namespace |
| factories.namespace | Database\\Factories\\ | factory sub-namespace |
| policies.namespace | Policies\\ | policy sub-namespace |

### Testing
PHPUnit: `<directory>./Modules/*/Tests</directory>` + `<include><directory>./Modules</directory></include>`
Pest: `uses(TestCase::class)->in('../Modules/*/Tests/*/*');`
PHPStan: `phpstan.php` with `...glob(__DIR__.'/Modules/*', GLOB_ONLYDIR)` in paths.
@endverbatim
