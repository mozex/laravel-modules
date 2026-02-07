# Translations

## Overview

Discovers module translation directories and registers both PHP array (namespaced) and JSON translation paths with Laravel's translator.

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

```
Modules/Blog/
└── Lang/
    ├── en/
    │   └── messages.php              // return ['welcome' => 'Welcome']
    ├── tr/
    │   └── messages.php
    └── en.json                       // {"Welcome": "Welcome"}
```

## Usage

PHP array translations use the module namespace (kebab-case) + file + key:

```php
__('blog::messages.welcome')
trans('shop::cart.added')
```

JSON translations are available via `__('Text')` from any module's `Lang/*.json` files.

## Configuration

- Set `'translations.active' => false` to disable translation loading.
- Edit `'translations.patterns'` to change discovery directories.

## Troubleshooting

- **Key not found**: verify namespace mapping — `Modules/Blog/Lang/en/messages.php` → `__('blog::messages.key')`.
- **JSON not loading**: place JSON files directly under the `Lang` directory (`Modules/Blog/Lang/en.json`).
