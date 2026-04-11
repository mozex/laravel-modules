---
title: Views
weight: 2
---

Each module's view directory is registered as a namespaced view location, using the same `namespace::view` syntax that Laravel packages use. The module name becomes a kebab-case namespace prefix: `Blog` maps to `blog`, `UserAdmin` maps to `user-admin`, `PWA` maps to `pwa`.

This feature also gives you anonymous Blade components for free. Any `.blade.php` file in a module's `Resources/views/components/` directory becomes a usable `<x-module::name />` component without writing a PHP class.

## Default configuration

```php
'views' => [
    'active' => true,
    'patterns' => [
        '*/Resources/views',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Resources/
    └── views/
        ├── home.blade.php
        ├── pages/
        │   ├── index.blade.php
        │   └── show.blade.php
        ├── partials/
        │   └── sidebar.blade.php
        └── components/
            ├── alert.blade.php
            └── form/
                └── input.blade.php
```

## Rendering views

Use the `namespace::path` syntax anywhere you'd normally reference a view:

```php
// In a controller
return view('blog::home');
return view('blog::pages.show', ['post' => $post]);

// In a route closure
Route::get('/blog', fn () => view('blog::pages.index'));
```

The path maps directly to the file structure inside `Resources/views/`. Dots replace directory separators: `blog::pages.show` resolves to `Modules/Blog/Resources/views/pages/show.blade.php`.

## Blade directives

All Blade directives work with namespaced views:

```blade
@include('blog::partials.sidebar')
@extends('blog::layouts.app')
@each('blog::partials.post-card', $posts, 'post')

@section('content')
    {{-- content here --}}
@endsection
```

You can also reference views across modules. A Shop module's layout can include a Shared module's partial:

```blade
{{-- Modules/Shop/Resources/views/checkout.blade.php --}}
@extends('shared::layouts.main')
@include('shared::partials.footer')
```

## Anonymous Blade components

Files inside `Resources/views/components/` work as anonymous Blade components. No PHP class needed.

```
Modules/Blog/
└── Resources/
    └── views/
        └── components/
            ├── alert.blade.php           // <x-blog::alert />
            ├── badge.blade.php           // <x-blog::badge />
            └── form/
                ├── input.blade.php       // <x-blog::form.input />
                └── select.blade.php      // <x-blog::form.select />
```

Use them in any Blade template:

```blade
<x-blog::alert type="warning" message="Draft post" />
<x-blog::form.input name="title" label="Post Title" />
```

Define props at the top of the anonymous component file, just like any Laravel anonymous component:

```blade
{{-- Modules/Blog/Resources/views/components/alert.blade.php --}}
@props(['type' => 'info', 'message'])

<div class="alert alert-{{ $type }}">
    {{ $message }}
</div>
```

This is different from class-based Blade components (covered in the [Blade Components](./blade-components) docs). Anonymous components live in the view directory and don't need a backing PHP class. Class-based components live in `View/Components/` and have a dedicated PHP class with a `render()` method.

## View overriding

You can override any module view without editing the module itself. Drop a file at `resources/views/vendor/{module}/{view}.blade.php` (using the kebab-cased module name) and Laravel will use that file instead of the one inside the module.

This is handled by Laravel's `loadViewsFrom()` helper, which the package uses to register each module's view namespace. Before pointing the namespace at the module directory, `loadViewsFrom()` checks whether `resources/views/vendor/{namespace}/` exists and, if so, registers it as a higher-priority path for the same namespace. The view finder walks paths in order and returns the first match, so the override wins.

For example, to override `blog::pages.show` without touching the Blog module, create `resources/views/vendor/blog/pages/show.blade.php`. A call to `view('blog::pages.show')` will now render the override file.

## Disabling

Set `'views.active' => false` to stop registering module view namespaces. Adjust `'views.patterns'` if your modules use a different directory for views.
