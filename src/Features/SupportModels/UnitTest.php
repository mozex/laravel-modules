<?php

use App\Models\Nested\NestedTest;
use App\Models\Test;
use Database\Factories\Nested\NestedTestFactory;
use Database\Factories\TestFactory;
use Modules\First\Database\Factories\Nested\NestedUserFactory;
use Modules\First\Database\Factories\UserFactory;
use Modules\First\Models\Nested\NestedUser;
use Modules\First\Models\User;
use Modules\Second\Database\Factories\Nested\NestedTeamFactory;
use Modules\Second\Database\Factories\TeamFactory;
use Modules\Second\Models\Nested\NestedTeam;
use Modules\Second\Models\Team;

it('does not emit deprecations when guessing non-module model names', function (): void {
    $deprecations = [];

    set_error_handler(function (int $errno, string $errstr) use (&$deprecations): bool {
        // Unrelated deprecations can fire from old framework versions on new
        // PHP; only the reflection call this package makes is under test.
        if (str_contains($errstr, 'ReflectionProperty::setValue()')) {
            $deprecations[] = $errstr;
        }

        return true;
    }, E_DEPRECATED | E_USER_DEPRECATED);

    try {
        (new TestFactory)->modelName();
    } finally {
        restore_error_handler();
    }

    expect($deprecations)->toBeEmpty();
});

it('can guess model name', function (): void {
    expect((new TestFactory)->modelName())->toBe(Test::class)
        ->and((new UserFactory)->modelName())->toBe(User::class)
        ->and((new TeamFactory)->modelName())->toBe(Team::class)
        ->and((new NestedTestFactory)->modelName())->toBe(NestedTest::class)
        ->and((new NestedUserFactory)->modelName())->toBe(NestedUser::class)
        ->and((new NestedTeamFactory)->modelName())->toBe(NestedTeam::class);
});
