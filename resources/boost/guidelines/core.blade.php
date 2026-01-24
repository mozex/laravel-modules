## mozex/laravel-modules — Modular Laravel, zero‑config

Split a Laravel app into first‑class modules under `Modules/`. Auto‑discovers: configs, routes, views, Blade components, translations, helpers, commands, service providers, migrations, seeders, models, factories, policies, events, listeners, Livewire, Filament, Nova, schedules.

### Boost integration
- Boost "search-docs" does NOT include this package — prefer local `./docs/**`.
- Useful tools: `list-artisan-commands`, `get-config` (modules.*), `list-routes`, `tinker`.

### Conventions
- Root: `Modules/` (not `app/Modules`). Namespace: `Modules\{Module}\...`
- Kebab‑cased aliases: `Blog` → `blog`, `UserAdmin` → `user-admin`
- Config: `config/modules.php` (feature toggles, patterns, options)
- Cache: `php artisan modules:cache` / `modules:clear` — avoid caching in local dev

### Module activation & ordering
```php
// config/modules.php
'modules' => [
    'Shared' => ['active' => true, 'order' => 1],  // loads first
    'Blog' => ['active' => true, 'order' => 2],
    'Legacy' => ['active' => false],  // disabled entirely
],
```
- `active`: false disables module completely (no asset discovery)
- `order`: lower values load earlier; default 0. Use for inter-module dependencies.

### Module layout
```
Modules/{Module}/
├── Config/                         # merged configs (filename = key)
├── Console/Commands/               # artisan commands
├── Console/Kernel.php              # schedule() for Laravel <10
├── Database/{Factories,Migrations,Seeders}/
├── Filament/{Panel}/{Resources,Pages,Widgets,Clusters}/
├── Helpers/                        # auto-required PHP files
├── Lang/                           # en/*.php (namespaced) + *.json
├── Listeners/                      # event listeners
├── Livewire/                       # livewire components
├── Models/                         # eloquent models
├── Nova/                           # nova resources
├── Policies/                       # gate policies
├── Providers/                      # service providers (auto-registered)
├── Resources/views/                # views + anonymous components
├── Routes/                         # web.php, api.php, channels.php, console.php, custom
└── View/Components/                # class-based Blade components
```

---

## Modules Facade API

```php
use Mozex\Modules\Facades\Modules;

// Path helpers
Modules::basePath('path/to/file');      // base_path() + suffix
Modules::modulesPath('Blog/Config');    // Modules directory + suffix

// Module name extraction (useful for dynamic logic)
Modules::moduleNameFromNamespace('Modules\\Blog\\Models\\Post');  // 'Blog'
Modules::moduleNameFromPath('/path/Modules/Blog/file.php');       // 'Blog'

// Get all module seeders
Modules::seeders();  // ['Modules\\Blog\\Database\\Seeders\\BlogDatabaseSeeder', ...]

// Route customization (call from service provider register() method)
Modules::routeGroup('admin', prefix: 'admin', middleware: ['web','auth'], as: 'admin::');
Modules::registerRoutesUsing('localized', fn ($attrs, $routes) => Route::localized(...));

// Inspect registered groups
Modules::getRouteGroups();        // ['api' => [...], 'web' => [...], ...]
Modules::getRegisterRoutesUsing();

// Testing: override base path
Modules::setBasePath('/custom/path');
```

---

## Feature reference

@verbatim
<code-snippet name="Views & components" lang="blade">
{{-- Views: Modules/Blog/Resources/views/home.blade.php --}}
{{ view('blog::home') }}
@include('invoice::inc.view')

{{-- Anonymous components: Resources/views/components/ --}}
<x-blog::form.input />
<x-shop::button.checkout />

{{-- Class-based: View/Components/Post/Card.php --}}
<x-blog::post.card :post="$post" />
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Livewire" lang="blade">
<livewire:blog::posts />
<livewire:blog::nested.manage.comments />
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Routes" lang="php">
// Modules/Blog/Routes/web.php (uses 'web' middleware)
Route::get('/blog', fn () => 'Blog');

// Modules/Shop/Routes/api.php (uses 'api' prefix + 'api' middleware)
Route::get('/products', fn () => Product::all());

// Default groups (registered in Modules constructor):
// - 'api': prefix 'api', middleware ['api']
// - 'web': middleware ['web']

// Custom group (call from service provider register())
Modules::routeGroup('admin', prefix: 'admin', middleware: ['web','auth'], as: 'admin::');

// Custom registrar for special routing (e.g., localization)
Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(fn () => Route::group($attributes, $routes));
});

// Broadcast channels: Modules/*/Routes/channels.php
// Console routes: Modules/*/Routes/console.php (Laravel 10+)
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Configs & translations" lang="php">
// Config: Modules/Blog/Config/blog.php → config('blog.key')
// Merging runs when config is NOT cached; module values win when priority=true (default)
config('blog.feature.enabled');

// Translations: Lang/en/messages.php + Lang/*.json
__('blog::messages.welcome');  // namespaced
__('Welcome');                 // JSON (any module)
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Models, factories & policies" lang="php">
// Auto-mapped: Models\Post ↔ Database\Factories\PostFactory ↔ Policies\PostPolicy
Modules\Blog\Models\Post::factory();
(new PostFactory)->modelName();  // → Modules\Blog\Models\Post
Gate::getPolicyFor(Post::class); // → Modules\Blog\Policies\PostPolicy

// Nested namespaces preserved:
// Models\Nested\Item ↔ Database\Factories\Nested\ItemFactory ↔ Policies\Nested\ItemPolicy

// IDE hint: set protected $model = Post::class in factories
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Commands, helpers & providers" lang="php">
// Command: Console/Commands/*.php extending Illuminate\Console\Command
protected $signature = 'shop:sync';

// Helper: Helpers/*.php (auto-required at registration, use function_exists guard)
if (! function_exists('format_price')) { function format_price(int $cents): string { /* ... */ } }

// Provider: Providers/*.php extending ServiceProvider (auto-registered)
class BlogServiceProvider extends Illuminate\Support\ServiceProvider {}
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Seeders & events" lang="php">
// Seeder: Database/Seeders/{Module}DatabaseSeeder.php (exact naming required)
$this->call(Modules::seeders());  // seed all modules

// Events: Listeners/*.php auto-discovered
// Listeners can handle events from any module or app
Event::dispatch(new Modules\Blog\Events\PostPublished($post));
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Schedules" lang="php">
// Laravel 10+: Routes/console.php
use Illuminate\Support\Facades\Schedule;
Schedule::command('blog:reindex')->hourly();

// Laravel <10: Console/Kernel extending Mozex\Modules\Contracts\ConsoleKernel
class Kernel extends ConsoleKernel {
    public function schedule(Schedule $schedule): void {
        $schedule->command('blog:sync')->daily();
    }
}
</code-snippet>
@endverbatim

### Filament
Place assets in `Modules/{Module}/Filament/{Panel}/{Resources|Pages|Widgets|Clusters}`.
Panel folder name (lowercased) = panel id: `Admin/` → `admin`, `Dashboard/` → `dashboard`.
Auto-discovered per panel; ensure matching panel providers exist in app.

### Nova
Resources extending `Laravel\Nova\Resource` in `*/Nova` auto-registered (excludes ActionResource).

---

## Configuration reference

Key `config/modules.php` options:

| Key | Default | Purpose |
|-----|---------|---------|
| `modules_directory` | `'Modules'` | Root directory for modules |
| `modules_namespace` | `'Modules\\'` | PSR-4 namespace prefix |
| `modules.{Name}.active` | `true` | Enable/disable specific module |
| `modules.{Name}.order` | `0` | Load order (lower = earlier) |
| `configs.priority` | `true` | Module configs override app (true) or provide defaults (false) |
| `routes.commands_filenames` | `['console']` | Files treated as console routes |
| `routes.channels_filenames` | `['channels']` | Files treated as broadcast channels |

Each feature section has `active` (bool) and `patterns` (array of globs).

---

## Testing integration

@verbatim
<code-snippet name="PHPUnit/Pest config" lang="xml">
<!-- phpunit.xml -->
<testsuite name="Modules"><directory>./Modules/*/Tests</directory></testsuite>
<source><include><directory>./app</directory><directory>./Modules</directory></include></source>
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Pest setup" lang="php">
// tests/Pest.php
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/*/*');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Test with custom base path" lang="php">
// In test setup, override modules path if needed
Modules::setBasePath('/path/to/workbench');
</code-snippet>
@endverbatim

PHPStan: use `phpstan.php` globbing `Modules/*`, excluding Tests/Database/Resources.
