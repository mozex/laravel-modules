---
title: Filament
weight: 18
---

Filament resources, pages, widgets, and clusters inside modules are discovered and registered with their respective panels. The panel ID is inferred from the directory structure: `Filament/Admin/Resources/` registers with the `admin` panel, `Filament/Dashboard/Widgets/` registers with the `dashboard` panel.

Each of the four asset types (resources, pages, widgets, clusters) is independently configurable. And like Livewire, this feature is conditional: if Filament isn't installed, the package skips it entirely.

## Default configuration

```php
'filament-resources' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Resources'],
],
'filament-pages' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Pages'],
],
'filament-widgets' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Widgets'],
],
'filament-clusters' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Clusters'],
],
```

## How panel mapping works

The key convention is the directory name between `Filament/` and the asset type. That directory name, lowercased, becomes the panel ID:

```
Modules/Blog/Filament/Admin/Resources/PostResource.php
                      ^^^^^ 
                      Panel ID = "admin"
```

During registration, the package iterates each Filament panel registered in your application. For each panel, it collects the module assets that match that panel ID and calls Filament's own discovery methods (`$panel->discoverResources()`, `$panel->discoverPages()`, etc.) with the correct paths and namespaces.

This means your Filament panel providers don't need any changes. The module assets show up alongside your application's Filament assets.

## Directory layout

A single module can have assets for multiple panels. The package only cares about the outer pattern: something that lives under `Filament/{Panel}/{Resources|Pages|Widgets|Clusters}/`. The internal structure of each resource (whether you use subdirectories for `Pages`, `Schemas`, and `Tables`, or keep everything flat) is entirely up to you, since the package's discovery doesn't look inside resource directories. The example below uses the newer convention with `Pages`, `Schemas`, and `Tables` subdirectories inside each resource:

```
Modules/Blog/
└── Filament/
    ├── Admin/
    │   ├── Resources/
    │   │   └── Posts/
    │   │       ├── PostResource.php
    │   │       ├── Pages/
    │   │       │   ├── CreatePost.php
    │   │       │   ├── EditPost.php
    │   │       │   └── ListPosts.php
    │   │       ├── Schemas/
    │   │       │   └── PostForm.php
    │   │       └── Tables/
    │   │           └── PostsTable.php
    │   ├── Pages/
    │   │   └── BlogSettings.php
    │   └── Widgets/
    │       └── PostStatsWidget.php
    └── Dashboard/
        └── Widgets/
            └── RecentPostsWidget.php
```

In this example, `PostResource`, `BlogSettings`, and `PostStatsWidget` register with the `admin` panel, while `RecentPostsWidget` registers with the `dashboard` panel. The `Schemas/` and `Tables/` subdirectories are Filament's newer convention for keeping form and table configuration out of the resource class itself. If you prefer a flatter layout with `form()` and `table()` methods directly on the resource, that works the same way with this package; the discovery doesn't look inside resource directories.

## Writing Filament classes

Module Filament classes are standard Filament classes. Write them exactly as you would in your application's `app/Filament/` directory. The only difference is the namespace and file location.

A resource class. Under the newer Filament convention, form and table configuration live in their own classes under `Schemas/` and `Tables/`, and the resource class just delegates to them:

```php
namespace Modules\Blog\Filament\Admin\Resources\Posts;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Blog\Filament\Admin\Resources\Posts\Pages\CreatePost;
use Modules\Blog\Filament\Admin\Resources\Posts\Pages\EditPost;
use Modules\Blog\Filament\Admin\Resources\Posts\Pages\ListPosts;
use Modules\Blog\Filament\Admin\Resources\Posts\Schemas\PostForm;
use Modules\Blog\Filament\Admin\Resources\Posts\Tables\PostsTable;
use Modules\Blog\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
```

The form class:

```php
namespace Modules\Blog\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            RichEditor::make('body')->required(),
        ]);
    }
}
```

The table class:

```php
namespace Modules\Blog\Filament\Admin\Resources\Posts\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title'),
            TextColumn::make('created_at')->dateTime(),
        ]);
    }
}
```

A widget:

```php
namespace Modules\Blog\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Blog\Models\Post;

class PostStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::count()),
            Stat::make('Published', Post::where('status', 'published')->count()),
            Stat::make('Drafts', Post::where('status', 'draft')->count()),
        ];
    }
}
```

## Panel ID matching

Your application must have a panel provider with a matching ID. If your `AdminPanelProvider` uses `->id('admin')`, then the `Filament/Admin/` directory registers with that panel. The match is case-insensitive: `Admin` in the directory name matches a panel with ID `admin`.

The directory structure must include a panel segment between `Filament/` and the asset type directory. If the scout can't extract a panel name from the path (for example, `Filament/Resources/PostResource.php` with no panel directory in between), it throws an exception during asset discovery.

If the panel segment is present but the extracted panel ID doesn't match any registered Filament panel, the assets are silently skipped. Double-check that your directory name matches one of your panel IDs exactly (case-insensitive).

## Multiple modules, same panel

Multiple modules can register assets with the same panel. A `Blog` module and a `Shop` module can both have `Filament/Admin/Resources/`, and all resources from both modules appear in the admin panel.

## Version compatibility

This version of the package requires Filament v5, which itself requires Livewire v4. If your application uses Filament v3, v4, or Livewire v3, use the [2.x branch](https://github.com/mozex/laravel-modules/tree/2.x) instead.

## Disabling

Each Filament asset type can be disabled independently:

```php
'filament-resources' => ['active' => false],   // disable resources
'filament-pages' => ['active' => false],        // disable pages
'filament-widgets' => ['active' => false],      // disable widgets
'filament-clusters' => ['active' => false],     // disable clusters
```

You can also adjust the glob patterns if your directory structure differs from the convention.
