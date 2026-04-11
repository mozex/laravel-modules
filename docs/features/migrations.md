---
title: Migrations
weight: 5
---

Module migration directories are registered with Laravel's migrator, so `php artisan migrate` picks up migrations from all active modules alongside your application's migrations. There's nothing to configure beyond the standard Laravel migration file format.

## Default configuration

```php
'migrations' => [
    'active' => true,
    'patterns' => [
        '*/Database/Migrations',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Database/
    └── Migrations/
        ├── 2024_01_10_100000_create_posts_table.php
        ├── 2024_01_11_110000_add_published_to_posts_table.php
        └── 2024_03_05_090000_add_slug_to_posts_table.php
```

Migration files follow Laravel's standard naming convention with a timestamp prefix. Write them exactly as you would in `database/migrations/`:

```php
// Modules/Blog/Database/Migrations/2024_01_10_100000_create_posts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

## Running migrations

All module migration paths are registered during boot, so the standard commands work as expected:

```bash
# Run all pending migrations (app + all modules)
php artisan migrate

# Roll back the last batch
php artisan migrate:rollback

# Reset and re-run all migrations
php artisan migrate:fresh
```

To run migrations for a single module only:

```bash
php artisan migrate --path=Modules/Blog/Database/Migrations
```

## Timestamp coordination

Laravel runs migrations in timestamp order across all sources. A migration timestamped `2024_01_10` in the Blog module runs before a `2024_01_15` migration in the Shop module, regardless of module load order.

Use unique timestamps across modules to avoid collisions. If two migrations from different modules share the same timestamp, the execution order between them becomes unpredictable.

## Migration status

`php artisan migrate:status` shows all migrations, including those from modules. The path column tells you which module each migration belongs to.

## Disabling

Set `'migrations.active' => false` to stop registering module migration paths. Existing migrations that have already run won't be affected; they just won't appear in future `migrate` commands.
