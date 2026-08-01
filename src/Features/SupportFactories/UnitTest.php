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
use Mozex\Modules\Enums\AssetType;

it('guesses module factory names from a stale published config without a namespace key', function (): void {
    $config = config('modules.'.AssetType::Factories->value);

    unset($config['namespace']);

    config()->set('modules.'.AssetType::Factories->value, $config);

    $warnings = [];

    set_error_handler(function (int $errno, string $errstr) use (&$warnings): bool {
        if (str_contains($errstr, 'Undefined array key')) {
            $warnings[] = $errstr;
        }

        return true;
    }, E_WARNING);

    try {
        $factory = User::factory();
    } finally {
        restore_error_handler();
    }

    expect($warnings)->toBeEmpty()
        ->and($factory)->toBeInstanceOf(UserFactory::class);
});

it('can guess factory name', function (): void {
    expect(Test::factory())->toBeInstanceOf(TestFactory::class)
        ->and(User::factory())->toBeInstanceOf(UserFactory::class)
        ->and(Team::factory())->toBeInstanceOf(TeamFactory::class)
        ->and(NestedTest::factory())->toBeInstanceOf(NestedTestFactory::class)
        ->and(NestedUser::factory())->toBeInstanceOf(NestedUserFactory::class)
        ->and(NestedTeam::factory())->toBeInstanceOf(NestedTeamFactory::class);
});
