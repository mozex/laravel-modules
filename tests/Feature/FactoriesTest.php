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

it('can guess factory name', function () {
    expect(Test::factory())->toBeInstanceOf(TestFactory::class)
        ->and(User::factory())->toBeInstanceOf(UserFactory::class)
        ->and(Team::factory())->toBeInstanceOf(TeamFactory::class)
        ->and(NestedTest::factory())->toBeInstanceOf(NestedTestFactory::class)
        ->and(NestedUser::factory())->toBeInstanceOf(NestedUserFactory::class)
        ->and(NestedTeam::factory())->toBeInstanceOf(NestedTeamFactory::class);
});
