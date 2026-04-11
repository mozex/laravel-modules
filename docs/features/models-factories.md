---
title: "Models & Factories"
weight: 9
---

Laravel's Eloquent uses "name guessing" to connect models to their factories and vice versa. By default, it expects factories in the `Database\Factories` namespace of your application. That doesn't work for modules, where factories live under `Modules\Blog\Database\Factories`.

This feature registers custom guessing callbacks that understand the module namespace structure. No scanning or discovery happens at runtime; it's just two namespace-based resolvers that swap the right segments.

## Default configuration

```php
'models' => [
    'active' => true,
    'namespace' => 'Models\\',
],

'factories' => [
    'active' => true,
    'namespace' => 'Database\\Factories\\',
],
```

## How the guessing works

When you call `Post::factory()` on a module model, the resolver:

1. Extracts the module name from the class namespace (`Modules\Blog\Models\Post` gives `Blog`)
2. Strips the models sub-namespace (`Models\Post` becomes `Post`)
3. Builds the factory class name by prepending the factory sub-namespace and appending `Factory`: `Modules\Blog\Database\Factories\PostFactory`

The reverse works the same way. Given a factory class, the resolver strips the factory namespace, removes the `Factory` suffix, and builds the model class name.

Nested namespaces are preserved:

| Model | Factory |
|---|---|
| `Modules\Blog\Models\Post` | `Modules\Blog\Database\Factories\PostFactory` |
| `Modules\Blog\Models\Comments\Comment` | `Modules\Blog\Database\Factories\Comments\CommentFactory` |
| `Modules\Shop\Models\Order` | `Modules\Shop\Database\Factories\OrderFactory` |

For classes that aren't in a module namespace, the resolvers fall back to Laravel's defaults. Your application's `App\Models\User` still resolves to `Database\Factories\UserFactory` as expected.

## Directory layout

```
Modules/Blog/
├── Models/
│   ├── Post.php
│   ├── Tag.php
│   └── Comments/
│       └── Comment.php
└── Database/
    └── Factories/
        ├── PostFactory.php
        ├── TagFactory.php
        └── Comments/
            └── CommentFactory.php
```

## Usage

Call `factory()` on your module models just like you would on application models:

```php
use Modules\Blog\Models\Post;

// Create a single instance
$post = Post::factory()->create();

// Create multiple with state
$posts = Post::factory()
    ->count(10)
    ->published()
    ->create();

// With relationships
$post = Post::factory()
    ->has(Comment::factory()->count(3))
    ->create();
```

## Custom namespaces

If your modules use different directory structures, update the `namespace` settings:

```php
// If models live in Modules/Blog/Entities/ instead of Modules/Blog/Models/
'models' => [
    'active' => true,
    'namespace' => 'Entities\\',
],

// If factories live in Modules/Blog/Tests/Factories/
'factories' => [
    'active' => true,
    'namespace' => 'Tests\\Factories\\',
],
```

## IDE support

Some IDEs can't follow the custom guessing callbacks and may not autocomplete `Post::factory()` return types. You can help your IDE by setting the `$model` property on factories explicitly:

```php
namespace Modules\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Blog\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => fake()->paragraphs(3, true),
        ];
    }
}
```

This is optional. The guessing works with or without the explicit `$model` property.

## Disabling

- `'factories.active' => false` disables the Model-to-Factory guessing callback. `Post::factory()` won't resolve to the module factory.
- `'models.active' => false` disables the Factory-to-Model guessing callback. Factories won't automatically know which model they belong to.
