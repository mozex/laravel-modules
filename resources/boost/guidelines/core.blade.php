## mozex/laravel-modules — Modular Laravel, zero‑config

Use this package to split a Laravel app into first‑class modules under the project root `Modules/`. Most assets are auto‑discovered and registered: configs, routes (web/api/console/broadcast), views, Blade components, translations, helpers, commands, service providers, migrations/seeders, models/factories, policies, events/listeners, Livewire, Filament, Nova, schedules, and more.

### Important: How to use this with Boost
- The Boost “search-docs” tool does NOT include this package. Prefer the local docs in `./docs/**` linked below.
- Use Boost tools where helpful:
  - list-artisan-commands → discover `modules:cache` / `modules:clear`
  - get-config → inspect `config('modules.*')`
  - list-routes → verify module routes were loaded
  - tinker → sanity‑check class existence or Gate policy resolution

### Conventions (memorize these)
- Modules root: `Modules/` at the project base (not `app/Modules`).
- Module namespace: `Modules\{Module}\...` (configurable in `config/modules.php`).
- Kebab‑cased view/component/livewire namespace: `Blog` → `blog`, `UserAdmin` → `user-admin`.
- Central config: `config/modules.php` (feature toggles, patterns, options).
- Rebuild discovery after structure changes: `php artisan modules:cache` (clear with `php artisan modules:clear`).
- Try not to cache modules in local environment to ease development.

### Minimal module layout
```
Modules/
└── Blog/
    ├── Config/
    ├── Console/                    # Commands + (optional) Kernel for schedules (<10)
    ├── Database/
    │   ├── Factories/
    │   ├── Migrations/
    │   └── Seeders/
    ├── Filament/
    ├── Lang/                       # PHP array (en/*.php) & JSON (en.json) translations
    ├── Listeners/
    ├── Livewire/
    ├── Models/
    ├── Nova/
    ├── Policies/
    ├── Providers/                  # extend Illuminate\Support\ServiceProvider
    ├── Resources/
    │   └── views/                  # view templates & anonymous components
    ├── Routes/                     # web.php, api.php, channels.php, console.php, custom groups
    └── View/
        └── Components/             # class‑based Blade components
```

### Quick‑use reference (copy / adapt)
@verbatim
<code-snippet name="Views & Anonymous Components" lang="blade">
{{-- Views --}}
{{ view('blog::home') }}  {{-- Modules/Blog/Resources/views/home.blade.php --}}

{{-- Blade include --}}
@include('invoice::inc.view') {{-- Modules/Invoice/Resources/views/inc/view.blade.php --}}

{{-- Anonymous components in Resources/views/components --}}
<x-blog::form.input />
<x-shop::button.checkout />
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Class‑based Blade component" lang="blade">
<x-blog::post.card :post="$post" />  {{-- Blog\View\Components\Post\Card --}}
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Livewire component" lang="blade">
<livewire:blog::posts />
<livewire:blog::nested.manage.comments />
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Routes: groups & custom registrars" lang="php">
use Mozex\Modules\Facades\Modules;
use Illuminate\Support\Facades\Route;

// Define a custom group named 'admin' (file: Modules/*/Routes/admin.php)
Modules::routeGroup('admin', prefix: 'admin', middleware: ['web','auth'], as: 'admin::');

// Register a custom registrar named 'localized' (file: Modules/*/Routes/localized.php)
Modules::registerRoutesUsing('localized', function (array $attributes, $routes) {
    Route::localized(function () use ($attributes, $routes) {
        Route::group(attributes: $attributes, routes: $routes);
    });
});

// Important: call Modules::routeGroup / registerRoutesUsing from a service provider's register() method
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Schedules" lang="php">
// Laravel 10+: schedule inside Modules/*/Routes/console.php using Schedule facade

// Laravel < 10: define Modules/{Module}/Console/Kernel extending
// Mozex\Modules\Contracts\ConsoleKernel and implement schedule(Schedule $schedule)
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Models & Factories mapping" lang="php">
// Model → Factory
Modules\Blog\Models\Post::factory();              // → Modules\Blog\Database\Factories\PostFactory

// Factory → Model
(new Modules\Blog\Database\Factories\PostFactory)->modelName(); // → Modules\Blog\Models\Post

// IDE hint: set protected $model = Post::class in factories for autocompletion.
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Policies mapping" lang="php">
Gate::getPolicyFor(Modules\Blog\Models\Post::class); // → Modules\Blog\Policies\PostPolicy
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Translations (PHP & JSON)" lang="php">
__('blog::messages.welcome'); // Modules/Blog/Lang/en/messages.php → ['welcome' => '...']
__('Welcome');                 // JSON translations from Modules/*/Lang/*.json
</code-snippet>
@endverbatim

### Filament structure & panels
```
Modules/Blog/
└── Filament/
    ├── Admin/                         # panel id: admin
    │   ├── Resources/                 # e.g., PostResource.php
    │   ├── Pages/                     # e.g., SettingsPage.php
    │   └── Widgets/                   # e.g., PostOverviewWidget.php
    └── Dashboard/                     # panel id: dashboard
        ├── Resources/                 # e.g., NestedPostResource.php
        ├── Pages/                     # e.g., CreatePostPage.php
        └── Clusters/                  # e.g., NestedPost
```
- Place classes under `Modules/{Module}/Filament/{Panel}/{Resources|Pages|Widgets|Clusters}`.
- The `{Panel}` folder name determines the panel id (lowercased), e.g., `Admin` → `admin`.
- Ensure your app defines matching Filament panels (e.g., AdminPanelProvider/DashboardPanelProvider).
- The package auto‑discovers and registers module Filament assets per panel; no manual registration calls are needed.

### Service Providers
- Put providers under `Providers/` and extend `Illuminate\Support\ServiceProvider`; they are auto‑registered.

### Key Artisan
```bash
php artisan modules:cache
php artisan modules:clear
```

### Editor & tooling tips
- PhpStorm factories autocompletion: set `protected $model` in factory classes.
- PHPStan: use a `phpstan.php` that globs module paths and excludes Resources/Database/Tests. See package docs.
- PHPUnit / Pest: include `Modules/*/Tests` in your test suite and for Pest add:
@verbatim
<code-snippet name="Pest include module tests" lang="php">
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('../Modules/*/Tests/*/*');
</code-snippet>
@endverbatim

### Configuration highlights (config/modules.php)
- `modules_directory`, `modules_namespace`
- Per‑feature sections: `active`, `patterns`, and feature‑specific options (e.g. routes groups, configs priority)

---

## Feature cheat sheet (essentials)

- Blade Components: class-based components discovered in `*/View/Components`, aliased as `<x-module::path.name/>`.
@verbatim
<code-snippet name="Blade Components usage" lang="blade">
<x-blog::post.card :post="$post" />
</code-snippet>
@endverbatim

- Views & Anonymous Components: `*/Resources/views` namespaced as `module::...`; anonymous in `views/components` → `<x-module::.../>`.
@verbatim
<code-snippet name="Views & anonymous" lang="blade">
{{ view('blog::home') }}
@include('invoice::inc.view')
<x-blog::form.input />
</code-snippet>
@endverbatim

- Livewire: components in `*/Livewire` → `<livewire:module::alias/>`.
@verbatim
<code-snippet name="Livewire usage" lang="blade">
<livewire:shop::list-products />
</code-snippet>
@endverbatim

- Routes: files in `*/Routes/*.php`; grouped by filename (web, api, admin, ...). Special: `channels.php`, `console.php`. Custom groups/registrars supported.
@verbatim
<code-snippet name="Routes basics" lang="php">
// Modules/Blog/Routes/web.php
Route::get('/blog', fn () => 'Blog');
</code-snippet>
@endverbatim

- Configs: merge `*/Config/*.php` (filename = key). Priority controls override direction.
@verbatim
<code-snippet name="Configs read" lang="php">
$config = config('blog.feature.enabled');
</code-snippet>
@endverbatim

- Translations: `*/Lang` PHP arrays (namespaced) and JSON.
@verbatim
<code-snippet name="Translations" lang="php">
__('blog::messages.welcome'); __('Welcome');
</code-snippet>
@endverbatim

- Service Providers: classes extending `Illuminate\\Support\\ServiceProvider` in `*/Providers` auto-registered.
@verbatim
<code-snippet name="Service Provider skeleton" lang="php">
class BlogServiceProvider extends Illuminate\Support\ServiceProvider {}
</code-snippet>
@endverbatim

- Helpers: PHP files in `*/Helpers/*.php` required once.
@verbatim
<code-snippet name="Helper guard" lang="php">
if (! function_exists('format_price')) { function format_price(int $cents): string { /* ... */ } }
</code-snippet>
@endverbatim

- Commands: classes extending `Illuminate\\Console\\Command` in `*/Console/Commands`.
@verbatim
<code-snippet name="Command signature" lang="php">
protected $signature = 'shop:sync';
</code-snippet>
@endverbatim

- Migrations: directories `*/Database/Migrations` registered with migrator.
@verbatim
<code-snippet name="Run migrations" lang="bash">
php artisan migrate
</code-snippet>
@endverbatim

- Seeders: `{Module}DatabaseSeeder` in `*/Database/Seeders`; call all via facade.
@verbatim
<code-snippet name="Seed all modules" lang="php">
$this->call(Mozex\Modules\Facades\Modules::seeders());
</code-snippet>
@endverbatim

- Models & Factories: two‑way guessing between `Models\` and `Database\Factories\`.
@verbatim
<code-snippet name="Model ↔ Factory" lang="php">
Modules\Blog\Models\Post::factory();
(new Modules\Blog\Database\Factories\PostFactory)->modelName();
</code-snippet>
@endverbatim

- Policies: Gate guessing `Models\*` → `Policies\*Policy`.
@verbatim
<code-snippet name="Policy mapping" lang="php">
Gate::getPolicyFor(Modules\Blog\Models\Post::class);
</code-snippet>
@endverbatim

- Events & Listeners: listener discovery under `*/Listeners` (can listen to app or other modules).
@verbatim
<code-snippet name="Dispatch event" lang="php">
Event::dispatch(new Modules\Blog\Events\PostPublished($post));
</code-snippet>
@endverbatim

- Schedules: Laravel 10+ use `Routes/console.php`; <10 use per‑module Console\Kernel `schedule()`.
@verbatim
<code-snippet name="Console route schedule" lang="php">
use Illuminate\Support\Facades\Schedule;
Schedule::command('blog:reindex')->hourly();
</code-snippet>
@endverbatim

- Filament: per‑panel assets `*/Filament/{Panel}/{Resources|Pages|Widgets|Clusters}`; `{Panel}` lowercased as panel id.
@verbatim
<code-snippet name="Filament panels" lang="text">
Modules/Blog/Filament/{Admin,Dashboard}/{Resources,Pages,Widgets,Clusters}
</code-snippet>
@endverbatim

- Nova: resources extending `Laravel\\Nova\\Resource` in `*/Nova` (excludes ActionResource).
@verbatim
<code-snippet name="Nova resource" lang="php">
class Post extends Laravel\Nova\Resource {}
</code-snippet>
@endverbatim

- Caching: discovery cache commands.
@verbatim
<code-snippet name="Modules cache" lang="bash">
php artisan modules:cache
php artisan modules:clear
</code-snippet>
@endverbatim

---

## Integrations (essentials)

- PHPStan: `phpstan.php` globs `Modules/*`; excludes Tests/Database/Resources; sets migrations path; level configurable.
@verbatim
<code-snippet name="Run PHPStan" lang="bat">
phpstan analyse -c phpstan.php
</code-snippet>
@endverbatim

- PHPUnit: add Modules testsuite and include app + Modules in source.
@verbatim
<code-snippet name="phpunit.xml essentials" lang="xml">
<testsuite name="Modules"><directory>./Modules/*/Tests</directory></testsuite>
<source><include><directory>./app</directory><directory>./Modules</directory></include></source>
</code-snippet>
@endverbatim
@verbatim
<code-snippet name="Run PHPUnit" lang="bat">
vendor\bin\phpunit -c phpunit.xml
</code-snippet>
@endverbatim

- Pest: include module tests from `tests/Pest.php`.
@verbatim
<code-snippet name="Pest include" lang="php">
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('../Modules/*/Tests/*/*');
</code-snippet>
@endverbatim
@verbatim
<code-snippet name="Run Pest" lang="bat">
vendor\bin\pest -c phpunit.xml
</code-snippet>
@endverbatim
