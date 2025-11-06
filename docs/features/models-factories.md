# Models & Factories

## Overview

This feature makes Eloquent work seamlessly inside modules by wiring the two guessing mechanisms Laravel uses:

- Model → Factory
- Factory → Model

With this in place, calling `Module\Model::factory()` or `new Module\Factory()->modelName()` works exactly as it does for app-level classes, while respecting your module namespaces and folder layout.

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

- Detect module from namespace: the module name is parsed from the fully-qualified class name (e.g., `Modules\\Blog\\Models\\Post` → `Blog`).
- Model → Factory:
  - Input: `Modules\\Blog\\Models\\Post`
  - Output: `Modules\\Blog\\Database\\Factories\\PostFactory`
- Factory → Model:
  - Input: `Modules\\Blog\\Database\\Factories\\PostFactory`
  - Output: `Modules\\Blog\\Models\\Post`
- Nested namespaces are preserved after the configured sub-namespace:
  - `Modules\\Shop\\Models\\Nested\\Item` ↔︎ `Modules\\Shop\\Database\\Factories\\Nested\\ItemFactory`

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
  - Change `'models.namespace'` and `'factories.namespace'` to match your folder structure.

## Backward compatibility

- If a class does not belong to a module namespace, the feature temporarily resets Laravel’s default resolvers, defers to the framework’s built-in logic (e.g., `Factory::resolveFactoryName()` / `$factory->modelName()`), and then restores the module-aware resolvers.

## Testing hints

- Assert both directions resolve as expected (including nested namespaces):
  ```php
  expect(Modules\Shop\Models\Nested\Item::factory())
      ->toBeInstanceOf(Modules\Shop\Database\Factories\Nested\ItemFactory::class);

  expect((new Modules\Shop\Database\Factories\Nested\ItemFactory)->modelName())
      ->toBe(Modules\Shop\Models\Nested\Item::class);
  ```

## Troubleshooting

- Unexpected class resolved:
  - Verify `'models.namespace'` and `'factories.namespace'` in `config/modules.php`.
  - Ensure your classes are under `Modules\\{Module}\\...` and follow the configured sub-namespaces.
- Factory not found for a model:
  - Create a matching Factory under the configured factories namespace with a `Factory` suffix.

## See also

- [Policies](./policies.md)
- [Configs](./configs.md)
- [Seeders](./seeders.md)

