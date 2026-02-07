# Policies

## Overview

Wires Laravel's `Gate::guessPolicyNamesUsing` so that models inside modules automatically resolve to their corresponding policy classes. No scanning is needed — the mapping uses configured sub-namespaces.

## Default configuration

```php
'policies' => [
    'active' => true,
    'namespace' => 'Policies\\',
],

'models' => [
    'active' => true,
    'namespace' => 'Models\\',
],
```

## How name guessing works

1. Module name is parsed from the model's namespace (e.g., `Modules\Blog\Models\Post` → `Blog`)
2. The models sub-namespace is replaced with the policies sub-namespace + `Policy` suffix:
   - `Modules\Blog\Models\Post` → `Modules\Blog\Policies\PostPolicy`
   - `Modules\Shop\Models\Nested\Item` → `Modules\Shop\Policies\Nested\ItemPolicy`
3. For non-module classes, Laravel's default resolver is used as fallback

## Directory layout

```
Modules/Blog/
├── Models/
│   ├── Post.php
│   └── Nested/
│       └── Comment.php
└── Policies/
    ├── PostPolicy.php
    └── Nested/
        └── CommentPolicy.php
```

## Usage

```php
Gate::getPolicyFor(Modules\Blog\Models\Post::class);
// returns Modules\Blog\Policies\PostPolicy instance
```

## Configuration

- Set `'policies.active' => false` to disable module policy guessing.
- Change `'models.namespace'` and `'policies.namespace'` to match your directory structure.

## See also

- [Models & Factories](./models-factories.md)
