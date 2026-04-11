---
title: Translations
weight: 13
---

Module translation directories are registered with Laravel's translator, supporting both PHP array files (namespaced) and JSON translation files. The module name becomes a kebab-case namespace prefix, so `Blog` module translations are accessed as `__('blog::messages.key')`.

## Default configuration

```php
'translations' => [
    'active' => true,
    'patterns' => [
        '*/Lang',
    ],
],
```

## Directory layout

The `Lang/` directory follows Laravel's standard translation structure:

```
Modules/Blog/
└── Lang/
    ├── en/
    │   ├── messages.php
    │   └── validation.php
    ├── fr/
    │   ├── messages.php
    │   └── validation.php
    └── fr.json
```

## PHP array translations

PHP translation files return an associative array:

```php
// Modules/Blog/Lang/en/messages.php
return [
    'welcome' => 'Welcome to the blog',
    'post_created' => 'Your post ":title" has been published.',
    'comments' => '{0} No comments|{1} :count comment|[2,*] :count comments',
];
```

Access them with the `namespace::file.key` syntax:

```php
__('blog::messages.welcome')
// "Welcome to the blog"

__('blog::messages.post_created', ['title' => 'My First Post'])
// "Your post "My First Post" has been published."

trans_choice('blog::messages.comments', 5, ['count' => 5])
// "5 comments"
```

The namespace is the module name in kebab-case. The file name (without `.php`) is the next segment. Then the array key.

## JSON translations

JSON translation files sit directly in the `Lang/` directory, named by locale. For a module translating into French, that's `Modules/Blog/Lang/fr.json`:

```json
{
    "Read more": "Lire la suite",
    "Published on :date": "Publié le :date"
}
```

JSON translations don't use namespaces. They're accessed through the `__()` helper with the original string as the key:

```php
__('Read more')
__('Published on :date', ['date' => $post->created_at->format('M j, Y')])
```

JSON translations from all modules (and the application) merge into a single pool. If two modules define the same key, the last one loaded wins, based on module order.

## Using translations in Blade

```blade
<h1>{{ __('blog::messages.welcome') }}</h1>

@lang('blog::messages.post_created', ['title' => $post->title])

<p>{{ trans_choice('blog::messages.comments', $post->comments_count) }}</p>

{{-- JSON translations --}}
<a href="#">{{ __('Read more') }}</a>
```

## Cross-module translations

One module can reference another module's translations:

```php
// In a Shop module controller, using a Shared module's translations
__('shared::common.save')
__('shared::common.cancel')
```

This is useful when a `Shared` module provides common UI strings that other modules reuse.

## Disabling

Set `'translations.active' => false` to stop registering module translation paths. Adjust `'translations.patterns'` if your modules use a different directory name for translations.
