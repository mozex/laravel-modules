# Views

## Overview

Auto-discovers view directories within modules and registers each as a view namespace. Supports both named views (`view('module::path')`) and anonymous Blade components from `Resources/views/components/`.

## Default configuration

```php
'views' => [
    'active' => true,
    'patterns' => [
        '*/Resources/views',
    ],
],
```

## Naming

Module name → kebab-case namespace: `Blog` → `blog`, `PWA` → `pwa`, `UserAdmin` → `user-admin`.

## Directory layout

```
Modules/Blog/
└── Resources/
    └── views/
        ├── home.blade.php              // view('blog::home')
        ├── pages/
        │   └── show.blade.php          // view('blog::pages.show')
        └── components/
            ├── filter.blade.php        // <x-blog::filter />
            └── form/
                └── input.blade.php     // <x-blog::form.input />

Modules/PWA/
└── Resources/
    └── views/
        ├── head.blade.php              // view('pwa::head')
        └── components/
            └── manifest.blade.php      // <x-pwa::manifest />
```

## Usage

Views:

```php
view('blog::home')
view('blog::pages.show')
```

Blade includes:

```blade
@include('blog::partials.sidebar')
```

Anonymous components (from `Resources/views/components/`):

```blade
<x-blog::filter />
<x-blog::form.input />
<x-pwa::manifest />
```

## Configuration

- Set `'views.active' => false` to disable view namespace registration.
- Edit `'views.patterns'` to change discovery directories.

## Troubleshooting

- **View not found**: verify the namespaced key matches the file path — `blog::pages.show` for `Modules/Blog/Resources/views/pages/show.blade.php`.
- **Anonymous component not found**: ensure it's under `Resources/views/components/` and use `<x-module::path />`.

## See also

- [Blade Components](./blade-components.md)
