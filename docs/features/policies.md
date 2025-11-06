# Policies

## Overview

The package wires Laravel’s policy name guessing so that models inside modules automatically resolve to their corresponding policy classes. You can keep your policies under each module and Gate will find them without manual mapping.

## What gets discovered

- No scanning is required. Instead, Laravel’s `Gate::guessPolicyNamesUsing` is configured to map Models → Policies using your configured module sub-namespaces.
- Namespaces used for guessing come from `config/modules.php`:
  - `models.namespace` (default: `Models\\`)
  - `policies.namespace` (default: `Policies\\`)

## Default configuration

In `config/modules.php`:

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

- Detect module from namespace: the module name is parsed from the model class namespace (e.g., `Modules\Blog\Models\Post` → `Blog`).
- Model → Policy mapping:
  - Input: `Modules\Blog\Models\Post`
  - Output: `Modules\Blog\Policies\PostPolicy`
- Nested namespaces are preserved beyond the configured sub-namespace:
  - `Modules\Shop\Models\Nested\Item` → `Modules\Shop\Policies\Nested\ItemPolicy`

## Directory layout examples

```
Modules/Blog/
├── Models/
│   ├── Post.php                           // Modules\Blog\Models\Post
│   └── Nested/
│       └── Comment.php                    // Modules\Blog\Models\Nested\Comment
└── Policies/
    ├── PostPolicy.php                     // Modules\Blog\Policies\PostPolicy
    └── Nested/
        └── CommentPolicy.php              // Modules\Blog\Policies\Nested\CommentPolicy
```

## Usage

- With guessing enabled, registering policies is automatic:
  ```php
  Gate::getPolicyFor(Modules\Blog\Models\Post::class);
  // returns an instance of Modules\Blog\Policies\PostPolicy
  ```
- You can still define abilities inside your policy methods as usual.

## Configuration options

- Toggle feature
  - Set `'policies.active' => false` to disable module policy guessing.
- Customize sub-namespaces
  - Change `'models.namespace'` and `'policies.namespace'` to match your directory structure.

## Troubleshooting

- Policy not found: ensure the policy class exists under your module’s `Policies` namespace and ends with `Policy`.
- Namespace mismatch: confirm the model and policy namespaces match their PSR‑4 paths.

## See also

- [Models & Factories](./models-factories.md)
- [Configs](./configs.md)
