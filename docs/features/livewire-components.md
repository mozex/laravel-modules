---
title: Livewire Components
weight: 17
---

Livewire components inside modules are registered with namespaced aliases using the `<livewire:module::component />` syntax. This version of the package targets Livewire v3 and discovers class-based components from each module's `Livewire/` directory.

This feature is conditional. If Livewire isn't installed, the package skips it entirely. No configuration needed.

## Default configuration

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
],
```

The `patterns` array controls where class-based components are discovered. The package scans each matched directory for non-abstract classes extending `Livewire\Component`.

## Class-based components

Put a PHP class in the `Livewire/` directory and a matching Blade view in `Resources/views/livewire/`:

```php
// Modules/Blog/Livewire/PostEditor.php
namespace Modules\Blog\Livewire;

use Livewire\Component;
use Modules\Blog\Models\Post;

class PostEditor extends Component
{
    public Post $post;
    public string $title = '';
    public string $body = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->body = $post->body;
    }

    public function save(): void
    {
        $this->post->update([
            'title' => $this->title,
            'body' => $this->body,
        ]);

        session()->flash('saved', true);
    }

    public function render()
    {
        return view('blog::livewire.post-editor');
    }
}
```

The view file lives under the module's view directory:

```blade
{{-- Modules/Blog/Resources/views/livewire/post-editor.blade.php --}}
<form wire:submit="save">
    <input wire:model="title" type="text">
    <textarea wire:model="body"></textarea>
    <button type="submit">Save</button>

    @if(session('saved'))
        <span>Saved!</span>
    @endif
</form>
```

Use it in any Blade template:

```blade
<livewire:blog::post-editor :post="$post" />
```

## Directory layout

```
Modules/Blog/
├── Livewire/
│   ├── PostEditor.php              // <livewire:blog::post-editor />
│   └── Comments/
│       └── CommentList.php         // <livewire:blog::comments.comment-list />
└── Resources/
    └── views/
        └── livewire/
            ├── post-editor.blade.php
            └── comments/
                └── comment-list.blade.php
```

## Naming rules

The module name becomes a kebab-case prefix. Path segments are kebab-cased and dot-joined:

| Component location | Tag |
|---|---|
| `Blog/Livewire/Posts.php` | `<livewire:blog::posts />` |
| `Blog/Livewire/Comments/CommentList.php` | `<livewire:blog::comments.comment-list />` |
| `PWA/Livewire/Icons.php` | `<livewire:pwa::icons />` |
| `UserAdmin/Livewire/Dashboard.php` | `<livewire:user-admin::dashboard />` |

The component is registered with Livewire using `Livewire::component($name, $class)`, so the namespaced tag resolves to the correct class automatically.

## Alternative syntax

Both Blade tag syntax and the `@livewire` directive work:

```blade
{{-- Tag syntax --}}
<livewire:blog::post-editor :post="$post" />

{{-- Directive syntax --}}
@livewire('blog::post-editor', ['post' => $post])
```

## Testing Livewire components

Test module Livewire components using the namespaced alias:

```php
use Livewire\Livewire;

Livewire::test('blog::post-editor', ['post' => $post])
    ->set('title', 'Updated Title')
    ->call('save')
    ->assertHasNoErrors();
```

## Version compatibility

This version of the package requires Livewire v3. It does not support Livewire v4 features like single-file components (SFC) or multi-file components (MFC). If you need v4 support, use the [3.x version](https://github.com/mozex/laravel-modules) of this package.

## Disabling

Set `'livewire-components.active' => false` to disable Livewire component registration. Adjust `'livewire-components.patterns'` if your modules use a different directory for Livewire components.
