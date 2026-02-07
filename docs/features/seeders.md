# Seeders

## Overview

Discovers top-level module database seeders. Only classes named exactly `{Module}DatabaseSeeder` extending `Illuminate\Database\Seeder` are discovered. Other seeders should be called from within the module's main seeder.

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
        ├── BlogDatabaseSeeder.php        // discovered
        └── PostSeeder.php                // NOT discovered (call from BlogDatabaseSeeder)
```

## Usage

In your application `DatabaseSeeder`:

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

Inside the module seeder, call internal seeders:

```php
class BlogDatabaseSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        $this->call(PostSeeder::class);
    }
}
```

## Configuration

- Set `'seeders.active' => false` to disable seeder discovery.
- Edit `'seeders.patterns'` to change discovery directories.

## See also

- [Migrations](./migrations.md)
- [Models & Factories](./models-factories.md)
