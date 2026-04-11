---
title: Policies
weight: 10
---

Laravel's authorization system uses `Gate::guessPolicyNamesUsing` to find the policy class for a given model. Out of the box, it only looks in the `App\Policies` namespace. This feature adds a module-aware resolver so that `Modules\Blog\Models\Post` automatically maps to `Modules\Blog\Policies\PostPolicy`.

Like the [Models & Factories](./models-factories) feature, this is a namespace-based resolver with no runtime scanning.

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

## How the guessing works

When Laravel needs a policy for a module model, the resolver:

1. Extracts the module name from the model's namespace
2. Replaces the models sub-namespace with the policies sub-namespace
3. Appends `Policy` to the class name

| Model | Policy |
|---|---|
| `Modules\Blog\Models\Post` | `Modules\Blog\Policies\PostPolicy` |
| `Modules\Blog\Models\Comments\Comment` | `Modules\Blog\Policies\Comments\CommentPolicy` |
| `Modules\Shop\Models\Order` | `Modules\Shop\Policies\OrderPolicy` |

For classes outside the modules namespace (`App\Models\User`, for example), the resolver falls back to Laravel's default behavior.

## Directory layout

```
Modules/Blog/
├── Models/
│   ├── Post.php
│   └── Comments/
│       └── Comment.php
└── Policies/
    ├── PostPolicy.php
    └── Comments/
        └── CommentPolicy.php
```

## Writing a module policy

Module policies work exactly like application policies:

```php
namespace Modules\Blog\Policies;

use Modules\Blog\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }
}
```

Then use standard Laravel authorization:

```php
// In a controller
$this->authorize('update', $post);

// In a Blade template
@can('update', $post)
    <a href="{{ route('blog.posts.edit', $post) }}">Edit</a>
@endcan

// Via the Gate facade
Gate::allows('delete', $post);
```

## Custom namespaces

If your models or policies use non-standard directories, update the namespace settings:

```php
// Models in Modules/Blog/Entities/
'models' => [
    'active' => true,
    'namespace' => 'Entities\\',
],

// Policies in Modules/Blog/Auth/Policies/
'policies' => [
    'active' => true,
    'namespace' => 'Auth\\Policies\\',
],
```

## Disabling

Set `'policies.active' => false` to disable module policy guessing. You can still register policies manually using `Gate::policy()` in a service provider if needed.
