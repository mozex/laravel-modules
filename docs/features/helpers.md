# Helpers

## Overview

Auto-discovers PHP helper files within modules and `require_once`s them during the container registration phase, making helper functions globally available.

## Default configuration

```php
'helpers' => [
    'active' => true,
    'patterns' => [
        '*/Helpers/*.php',
    ],
],
```

## Directory layout

```
Modules/Blog/
└── Helpers/
    ├── formatting.php
    └── strings.php

Modules/Shop/
└── Helpers/
    └── pricing.php
```

## Usage

Guard helpers with `function_exists` to avoid redeclaration across modules:

```php
if (! function_exists('format_price')) {
    function format_price(int $cents): string { /* ... */ }
}
```

After discovery, call them anywhere:

```php
$label = format_price(1999);
```

## Configuration

- Set `'helpers.active' => false` to disable requiring helper files.
- Edit `'helpers.patterns'` to change discovery directories.
