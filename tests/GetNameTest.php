<?php

use Mozex\Modules\Features\Feature;

function getNameViaReflection(string $name): string
{
    $feature = new class(app()) extends Feature
    {
        public static function asset(): never
        {
            throw new RuntimeException('Not implemented');
        }
    };

    $method = new ReflectionMethod($feature, 'getName');

    return $method->invoke($feature, $name);
}

it('converts all-uppercase names to lowercase', function (string $input, string $expected): void {
    expect(getNameViaReflection($input))->toBe($expected);
})->with([
    ['PWA', 'pwa'],
    ['CRM', 'crm'],
    ['API', 'api'],
    ['IO', 'io'],
    ['A', 'a'],
]);

it('converts PascalCase names to kebab-case', function (string $input, string $expected): void {
    expect(getNameViaReflection($input))->toBe($expected);
})->with([
    ['Blog', 'blog'],
    ['UserAdmin', 'user-admin'],
    ['First', 'first'],
    ['Second', 'second'],
    ['MyModule', 'my-module'],
]);

it('handles mixed-case names with consecutive uppercase letters', function (string $input, string $expected): void {
    expect(getNameViaReflection($input))->toBe($expected);
})->with([
    ['MyAPI', 'my-api'],
    ['HTMLParser', 'html-parser'],
    ['IOStream', 'io-stream'],
    ['getURL', 'get-url'],
]);
