---
title: Livewire Components
weight: 17
---

Livewire components inside modules are registered with namespaced aliases using the `<livewire:module::component />` syntax. The package supports all three Livewire v4 component types: class-based, single-file (SFC), and multi-file (MFC).

This feature is conditional. If Livewire isn't installed, the package skips it entirely. No configuration needed.

## Default configuration

```php
'livewire-components' => [
    'active' => true,
    'patterns' => [
        '*/Livewire',
    ],
    'view_path' => 'Resources/views/livewire',
],
```

The `patterns` control where class-based components are discovered. The `view_path` controls where SFC and MFC components live, relative to the module root.

## Class-based components

The traditional approach: a PHP class in the `Livewire/` directory with a separate Blade view.

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

The view goes in `Resources/views/livewire/`:

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

Use it in templates:

```blade
<livewire:blog::post-editor :post="$post" />
```

## Single-file components (SFC)

SFCs combine PHP logic and Blade markup in a single `.blade.php` file, placed in the `view_path` directory:

```blade
{{-- Modules/Blog/Resources/views/livewire/counter.blade.php --}}
<?php

use Livewire\Component;

new class extends Component {
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }
};
?>

<div>
    <span>Count: {{ $count }}</span>
    <button wire:click="increment">+</button>
</div>
```

This is accessible as `<livewire:blog::counter />`. No separate PHP class needed.

## Multi-file components (MFC)

MFCs separate the PHP class and Blade view into individual files inside a named directory. Both files must share the directory name:

```php
// Modules/Blog/Resources/views/livewire/toggle/toggle.php
<?php

use Livewire\Component;

new class extends Component {
    public bool $on = false;

    public function toggle(): void
    {
        $this->on = ! $this->on;
    }
};
```

```blade
{{-- Modules/Blog/Resources/views/livewire/toggle/toggle.blade.php --}}
<div>
    <span>{{ $on ? 'On' : 'Off' }}</span>
    <button wire:click="toggle">Toggle</button>
</div>
```

Use it as `<livewire:blog::toggle />`. You can also add `.js`, `.css`, and `.test.php` files to the same directory for co-located assets and tests.

## Directory layout

Here's a module using all three component types:

```
Modules/Blog/
├── Livewire/
│   ├── PostEditor.php                         // Class-based
│   └── Comments/
│       └── CommentList.php                    // Nested class-based
└── Resources/
    └── views/
        └── livewire/
            ├── post-editor.blade.php          // View for PostEditor
            ├── counter.blade.php              // SFC
            ├── toggle/                        // MFC
            │   ├── toggle.php
            │   └── toggle.blade.php
            └── comments/
                └── comment-list.blade.php     // View for CommentList
```

## Naming rules

The module name becomes a kebab-case prefix. Path segments are kebab-cased and dot-joined:

| Component location | Tag |
|---|---|
| `Blog/Livewire/PostEditor.php` | `<livewire:blog::post-editor />` |
| `Blog/Livewire/Comments/CommentList.php` | `<livewire:blog::comments.comment-list />` |
| `PWA/Livewire/Icons.php` | `<livewire:pwa::icons />` |
| `Blog/Resources/views/livewire/counter.blade.php` | `<livewire:blog::counter />` |
| `Blog/Resources/views/livewire/toggle/toggle.blade.php` | `<livewire:blog::toggle />` |

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

This version of the package requires Livewire v4. If your application uses Livewire v3, use the [2.x branch](https://github.com/mozex/laravel-modules/tree/2.x) instead.

## Disabling

Set `'livewire-components.active' => false` to disable Livewire component registration. Adjust `'livewire-components.patterns'` to change class-based discovery paths, and `'livewire-components.view_path'` to change the SFC/MFC directory location.
