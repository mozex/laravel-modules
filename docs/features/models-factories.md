# Models & Factories

## Overview

This feature makes Eloquent work seamlessly inside modules by wiring the two guessing mechanisms Laravel uses:

- Model → Factory
- Factory → Model

With this in place, calling `Module\Model::factory()` or `new Module\Factory()->modelName()` works exactly as it does for app-level classes, while respecting your module namespaces and
directory layout.

## What gets discovered

- No runtime scanning of classes is needed. Instead, two resolvers are registered with `Illuminate\Database\Eloquent\Factories\Factory` to map between module Models and Factories using your configured sub-namespaces.
- Namespaces used for guessing come from `config/modules.php`:
  - `models.namespace` (default: `Models\\`)
  - `factories.namespace` (default: `Database\\Factories\\`)

## Default configuration

In `config/modules.php`:

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

- Detect module from namespace: the module name is parsed from the fully-qualified class name (e.g., `Modules\Blog\Models\Post` → `Blog`).
- Model → Factory:
  - Input: `Modules\Blog\Models\Post`
  - Output: `Modules\Blog\Database\Factories\PostFactory`
- Factory → Model:
  - Input: `Modules\Blog\Database\Factories\PostFactory`
  - Output: `Modules\Blog\Models\Post`
- Nested namespaces are preserved after the configured sub-namespace:
  - `Modules\Shop\Models\Nested\Item` ↔︎ `Modules\Shop\Database\Factories\Nested\ItemFactory`

## Directory layout examples

```
Modules/Blog/
├── Models/
│   ├── Post.php                           // Modules\Blog\Models\Post
│   └── Nested/
│       └── Comment.php                    // Modules\Blog\Models\Nested\Comment
└── Database/
    └── Factories/
        ├── PostFactory.php                // Modules\Blog\Database\Factories\PostFactory
        └── Nested/
            └── CommentFactory.php         // Modules\Blog\Database\Factories\Nested\CommentFactory
```

## Usage

- Model → Factory
  ```php
  $factory = Modules\Blog\Models\Post::factory();
  // resolves to Modules\Blog\Database\Factories\PostFactory
  ```

- Factory → Model
  ```php
  $modelClass = (new Modules\Blog\Database\Factories\PostFactory)->modelName();
  // returns Modules\Blog\Models\Post::class
  ```

## Configuration options

- Toggle per direction
  - `'models.active' => false` disables Factory → Model guessing.
  - `'factories.active' => false` disables Model → Factory guessing.
- Customize sub-namespaces
  - Change `'models.namespace'` and `'factories.namespace'` to match your directory structure.

## Troubleshooting

- Model/Factory not resolving: verify `'models.namespace'` and `'factories.namespace'` match your directory layout and namespaces.
- Missing factory: create a matching factory class under `Database/Factories` with the `Factory` suffix.

## Editor hints

- Some IDEs (e.g., PhpStorm) don’t pick up the custom model/factory resolvers for autocompletion and navigation. Functionally, everything works; this only affects IDE hints.
- To help your editor, set the `$model` property explicitly on your factories:
  ```php
  namespace Modules\Blog\Database\Factories;

  use Illuminate\Database\Eloquent\Factories\Factory;
  use Modules\Blog\Models\Post;

  class PostFactory extends Factory
  {
      protected $model = Post::class;

      public function definition(): array
      {
          return [/* ... */];
      }
  }
  ```

## See also

- [Policies](./policies.md)
- [Seeders](./seeders.md)
