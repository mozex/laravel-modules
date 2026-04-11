---
title: Pest
weight: 3
---

Pest runs on top of PHPUnit, so you'll need the [PHPUnit configuration](./phpunit.md) in place first (the `Modules` test suite in `phpunit.xml`). On top of that, Pest needs a `tests/Pest.php` file that tells it which base class and traits to apply to module tests.

## Setup

Make sure `phpunit.xml` has the Modules test suite:

```xml
<testsuite name="Modules">
    <directory>./Modules/*/Tests</directory>
</testsuite>
```

Then update `tests/Pest.php` to include module test paths:

```php
<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// App tests
uses(TestCase::class)->in('Feature', 'Unit');

// Module tests (path is relative to tests/Pest.php)
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/*');
```

The `../Modules/*/Tests/*` pattern is relative to the `tests/` directory where `Pest.php` lives. It matches every direct child of each module's `Tests/` directory, whether that's a subdirectory like `Tests/Feature/` or a test file placed directly in `Tests/`. Pest walks into each matched directory recursively, so deeply nested test files are covered too.

Adjust the traits (`RefreshDatabase`, etc.) based on what your module tests need. If you want different traits applied to feature tests and unit tests, split the calls:

```php
uses(TestCase::class, RefreshDatabase::class)->in('../Modules/*/Tests/Feature/*');
uses(TestCase::class)->in('../Modules/*/Tests/Unit/*');
```

Feature tests get `RefreshDatabase` for transactional database isolation, while unit tests skip the trait entirely so they stay fast.

## Running tests

```bash
# Everything
./vendor/bin/pest

# Only module tests
./vendor/bin/pest --testsuite Modules

# A specific module
./vendor/bin/pest Modules/Blog/Tests

# A specific test file
./vendor/bin/pest Modules/Blog/Tests/Feature/PostTest.php

# Filter by test name
./vendor/bin/pest --filter="can create a post"
```

## Writing Pest tests in modules

Module Pest tests work exactly like application Pest tests:

```php
// Modules/Blog/Tests/Feature/PostTest.php

use Modules\Blog\Models\Post;

it('can list published posts', function () {
    Post::factory()->published()->count(3)->create();
    Post::factory()->draft()->create();

    $response = $this->get('/blog');

    $response->assertOk();
    $response->assertViewHas('posts', fn ($posts) => $posts->count() === 3);
});

it('can view a single post', function () {
    $post = Post::factory()->published()->create();

    $response = $this->get("/blog/{$post->slug}");

    $response->assertOk();
    $response->assertSee($post->title);
});
```

## Troubleshooting

**No tests found**: Check that `phpunit.xml` has the `Modules/*/Tests` suite directory and that the `->in()` path in `Pest.php` matches your actual directory structure. The path is relative to `tests/Pest.php`, not the project root.

**TestCase not applied**: If tests fail because they're missing the application context (no `$this->get()`, etc.), the `uses()` pattern in `Pest.php` probably isn't matching your test files. Verify the glob pattern depth matches your directory nesting.
