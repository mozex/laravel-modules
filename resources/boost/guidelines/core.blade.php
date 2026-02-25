@verbatim
## mozex/laravel-modules

Auto-discovers module assets from `Modules/` (not `app/Modules`). Namespace: `Modules\{Module}\...`
Config: `config/modules.php`. Cache: `php artisan modules:cache` / `modules:clear` (avoid caching in local dev).

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
  Database/Factories/             → Model↔Factory auto-mapping by namespace (nested preserved)
  Database/Migrations/            → registered with migrator
  Database/Seeders/               → only {Module}DatabaseSeeder; via Modules::seeders()
  Filament/{PanelId}/Resources|Pages|Widgets|Clusters/  → per-panel (dir=panel id lowercase)
  Helpers/*.php                   → require_once in register(); guard with function_exists
  Lang/                           → __('module::file.key') + JSON translations
  Listeners/                      → Laravel event auto-discovery
  Livewire/                       → <livewire:module::name /> or nested.path
  Models/                         → factory/policy guessing by namespace (set $model in factories for IDE)
  Nova/                           → Nova\Resource subclasses (excl. ActionResource)
  Policies/                       → Models\X → Policies\XPolicy (nested: Models\A\B → Policies\A\BPolicy)
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
Modules::getRouteGroups()                       // inspect registered groups
Modules::getRegisterRoutesUsing()               // inspect custom registrars
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
| modules.{Name}.active | true | enable/disable specific module |
| modules.{Name}.order | 0 | load order (lower=earlier) |

### Testing
PHPUnit: `<directory>./Modules/*/Tests</directory>` + `<include><directory>./Modules</directory></include>`
Pest: `uses(TestCase::class)->in('../Modules/*/Tests/*/*');`
PHPStan: `phpstan.php` with `...glob(__DIR__.'/Modules/*', GLOB_ONLYDIR)` in paths, excluding Tests/Database/Resources.

### Docs (read before working on a feature)
| Feature | File |
|---------|------|
| Blade Components | `vendor/mozex/laravel-modules/docs/features/blade-components.md` |
| Caching | `vendor/mozex/laravel-modules/docs/features/caching.md` |
| Commands | `vendor/mozex/laravel-modules/docs/features/commands.md` |
| Configs | `vendor/mozex/laravel-modules/docs/features/configs.md` |
| Events & Listeners | `vendor/mozex/laravel-modules/docs/features/events-listeners.md` |
| Filament | `vendor/mozex/laravel-modules/docs/features/filament.md` |
| Helpers | `vendor/mozex/laravel-modules/docs/features/helpers.md` |
| Livewire | `vendor/mozex/laravel-modules/docs/features/livewire-components.md` |
| Migrations | `vendor/mozex/laravel-modules/docs/features/migrations.md` |
| Models & Factories | `vendor/mozex/laravel-modules/docs/features/models-factories.md` |
| Nova | `vendor/mozex/laravel-modules/docs/features/nova-resources.md` |
| Policies | `vendor/mozex/laravel-modules/docs/features/policies.md` |
| Routes | `vendor/mozex/laravel-modules/docs/features/routes.md` |
| Seeders | `vendor/mozex/laravel-modules/docs/features/seeders.md` |
| Service Providers | `vendor/mozex/laravel-modules/docs/features/service-providers.md` |
| Translations | `vendor/mozex/laravel-modules/docs/features/translations.md` |
| Views | `vendor/mozex/laravel-modules/docs/features/views.md` |
| PHPUnit | `vendor/mozex/laravel-modules/docs/integrations/phpunit.md` |
| Pest | `vendor/mozex/laravel-modules/docs/integrations/pest.md` |
| PHPStan | `vendor/mozex/laravel-modules/docs/integrations/phpstan.md` |
| Overview & Facade API | `vendor/mozex/laravel-modules/docs/README.md` |
| Config reference | `config/modules.php` |
@endverbatim
