# Models & Factories

## Overview

Wires Laravel's two guessing mechanisms so Eloquent works seamlessly inside modules:

- **Model → Factory**: `Modules\Blog\Models\Post::factory()` resolves to `Modules\Blog\Database\Factories\PostFactory`
- **Factory → Model**: `(new PostFactory)->modelName()` resolves to `Modules\Blog\Models\Post`

No runtime scanning is needed — two namespace-based resolvers handle the mapping using your configured sub-namespaces.

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

## How name guessing works

1. The module name is parsed from the fully-qualified class name (e.g., `Modules\Blog\Models\Post` → `Blog`)
2. The sub-namespace after the configured prefix is swapped:
   - `Models\Post` ↔ `Database\Factories\PostFactory`
   - `Models\Nested\Comment` ↔ `Database\Factories\Nested\CommentFactory`
3. For non-module classes, Laravel's default resolvers are used as fallback

## Directory layout

```
Modules/Blog/
├── Models/
│   ├── Post.php
│   └── Nested/
│       └── Comment.php
└── Database/
    └── Factories/
        ├── PostFactory.php
        └── Nested/
            └── CommentFactory.php
```

## Usage

```php
// Model → Factory
$factory = Modules\Blog\Models\Post::factory();

// Factory → Model
$modelClass = (new Modules\Blog\Database\Factories\PostFactory)->modelName();
```

## Configuration

- `'models.active' => false` disables Factory → Model guessing.
- `'factories.active' => false` disables Model → Factory guessing.
- Change `'models.namespace'` and `'factories.namespace'` to match your directory structure.

## IDE hint

IDEs may not pick up the custom resolvers. To help, set `$model` explicitly on factories:

```php
class PostFactory extends Factory
{
    protected $model = Post::class;
}
```

## See also

- [Policies](./policies.md)
- [Seeders](./seeders.md)
