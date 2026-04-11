---
title: Seeders
weight: 6
---

The package discovers one seeder per module: the main database seeder, named `{Module}DatabaseSeeder`. For a module called `Blog`, that's `BlogDatabaseSeeder`. For `UserAdmin`, it's `UserAdminDatabaseSeeder`. Other seeder classes in the directory are ignored by discovery but can be called from within the main seeder.

Unlike most other features, seeders aren't auto-registered during boot. Instead, you call `Modules::seeders()` from your application's `DatabaseSeeder` to get the list of discovered seeder classes.

## Default configuration

```php
'seeders' => [
    'active' => true,
    'patterns' => [
        '*/Database/Seeders',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Database/
    └── Seeders/
        ├── BlogDatabaseSeeder.php     // discovered
        ├── PostSeeder.php             // NOT discovered
        └── CommentSeeder.php          // NOT discovered
```

Only `BlogDatabaseSeeder` is picked up. The naming convention is strict: it must be `{ModuleName}DatabaseSeeder` and it must extend `Illuminate\Database\Seeder`.

## Wiring seeders into your application

Add one line to your application's `DatabaseSeeder`:

```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mozex\Modules\Facades\Modules;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed the app's own data
        $this->call(UserSeeder::class);

        // Seed all active modules
        $this->call(Modules::seeders());
    }
}
```

`Modules::seeders()` returns an array of fully-qualified class names, filtered to only include active modules. Disabled modules are excluded.

## Calling sub-seeders

Inside the module's main seeder, call whatever other seeders you need:

```php
namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;

class BlogDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PostSeeder::class,
            CommentSeeder::class,
            TagSeeder::class,
        ]);
    }
}
```

This two-level approach (one entry point per module, with sub-seeders inside) keeps the discovery mechanism simple while giving you full control over each module's seeding logic. It also lets you order seeding steps within a module, handle dependencies, and conditionally seed based on the environment.

## Seed order

Seeders run in module load order. If `Shared` has `order: 1` and `Blog` has `order: 2`, `SharedDatabaseSeeder` runs before `BlogDatabaseSeeder`. Set explicit `order` values in the `modules` config section when inter-module seeding dependencies exist.

## Running seeders

```bash
# Run all seeders (app + modules)
php artisan db:seed

# Run a specific module seeder
php artisan db:seed --class="Modules\Blog\Database\Seeders\BlogDatabaseSeeder"

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

## Disabling

Set `'seeders.active' => false` to disable seeder discovery. `Modules::seeders()` will return an empty array.
