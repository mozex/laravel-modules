---
title: Nova Resources
weight: 19
---

Nova resource classes inside modules are discovered and registered during Nova's `serving` event. This means they only load when someone accesses the Nova admin panel, not on every application request.

Classes extending `Laravel\Nova\Actions\ActionResource` are automatically excluded from discovery, since those are internal to Nova's action system.

Like Livewire and Filament, this feature is conditional. If Nova isn't installed, the package skips it entirely.

## Default configuration

```php
'nova-resources' => [
    'active' => true,
    'patterns' => [
        '*/Nova',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Nova/
    ├── Post.php
    └── Category.php

Modules/Shop/
└── Nova/
    ├── Product.php
    └── Order.php
```

## Writing Nova resources

Module Nova resources are standard Nova resource classes. The only difference is the namespace:

```php
namespace Modules\Blog\Nova;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Blog\Models\Post;

class Post extends Resource
{
    public static $model = Post::class;

    public static $title = 'title';

    public static $search = ['id', 'title'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->sortable()->rules('required'),
            Markdown::make('Body')->rules('required'),
        ];
    }
}
```

The resource appears in the Nova sidebar alongside your application's resources. No manual registration needed.

## Model and policy resolution

Nova resources in modules work with the [Models & Factories](./models-factories) and [Policies](./policies) features. A `Modules\Blog\Nova\Post` resource pointing to `Modules\Blog\Models\Post` will automatically find `Modules\Blog\Policies\PostPolicy` for authorization.

## Registration timing

Nova resources are registered inside a `Nova::serving()` callback. They don't load during normal web requests or Artisan commands, only when Nova's routes are being served. This keeps the overhead at zero for non-Nova requests.

## Disabling

Set `'nova-resources.active' => false` to disable discovery. Adjust `'nova-resources.patterns'` if your Nova resources live in a different directory.
