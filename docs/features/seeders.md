# Seeders

## Overview

The package discovers top-level module Database seeders so you can easily run them from your application seeder or tests. A module seeder is a class extending `Illuminate\Database\Seeder` named `{Module}DatabaseSeeder` and placed under `Modules/{Module}/Database/Seeders`.

## What gets discovered

- Classes that:
  - extend `Illuminate\Database\Seeder`
  - are named exactly `{Module}DatabaseSeeder` (e.g., `BlogDatabaseSeeder` for the `Blog` module)
  - live in `Modules/{Module}/Database/Seeders`
- Nested or other seeders (e.g., `UserSeeder`, `TeamDatabaseSeeder`) are not returned by discovery; reference them from your `{Module}DatabaseSeeder`.

## Default configuration

In `config/modules.php`:

```php
'seeders' => [
    'active' => true,
    'patterns' => [
        '*/Database/Seeders',
    ],
],
```

## Directory layout examples

```
Modules/Blog/
└── Database/
    └── Seeders/
        ├── BlogDatabaseSeeder.php        // discovered
        └── PostSeeder.php                // not discovered directly

Modules/Shop/
└── Database/
    └── Seeders/
        ├── ShopDatabaseSeeder.php        // discovered
        └── ProductSeeder.php             // not discovered directly
```

## Usage

- In your application `DatabaseSeeder`, call discovered module seeders:
  ```php
  use Mozex\Modules\Facades\Modules;

  class DatabaseSeeder extends \Illuminate\Database\Seeder
  {
      public function run(): void
      {
          $this->call(Modules::seeders());
      }
  }
  ```
- Inside each `{Module}DatabaseSeeder`, call your internal seeders as needed:
  ```php
  class BlogDatabaseSeeder extends \Illuminate\Database\Seeder
  {
      public function run(): void
      {
          $this->call(PostSeeder::class);
      }
  }
  ```

## Configuration options

- Toggle discovery
  - Set `'seeders.active' => false` to disable seeder discovery.
- Change discovery patterns
  - Edit `'seeders.patterns'` to add/remove directories, relative to each module root.

## Performance and caching

- Discovered seeders are collected at boot; modules caching (`php artisan modules:cache`) speeds up discovery only.

## Testing hints

- In integration tests, call `Modules::seeders()` and run `$this->seed($class)` per item to seed module data.
- Unit-test each `{Module}DatabaseSeeder` by verifying it calls expected internal seeders using Laravel’s Container spies or fakes.

## Troubleshooting

- Seeder not discovered: ensure the class name matches `{Module}DatabaseSeeder`, extends `Seeder`, and is under `Database/Seeders`.
- No seeders returned: confirm `'seeders.active' => true` and patterns include your modules.

## See also

- [Migrations](./migrations.md)
- [Configs](./configs.md)
- [Routes](./routes.md)

