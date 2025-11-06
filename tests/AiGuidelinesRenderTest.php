<?php

it('renders the Boost guidelines Blade doc without error', function () {
    $path = sprintf(
        '%s/../resources/boost/guidelines/core.blade.php',
        __DIR__
    );

    expect(is_file($path))->toBeTrue();

    $html = view()->file($path)->render();

    expect($html)->toBeString()
        ->and($html)->toContain('mozex/laravel-modules');
});
