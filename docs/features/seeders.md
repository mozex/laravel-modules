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

## Troubleshooting

- Not discovered: the top-level seeder must be named `{Module}DatabaseSeeder` and extend `Illuminate\Database\Seeder`.
- Nothing runs: ensure your app’s `DatabaseSeeder` calls `$this->call(Modules::seeders())`.

## See also

- [Migrations](./migrations.md)
- [Models & Factories](./models-factories.md)
