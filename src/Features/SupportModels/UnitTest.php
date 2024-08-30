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

it('can guess model name', function () {
    expect((new TestFactory)->modelName())->toBe(Test::class)
        ->and((new UserFactory)->modelName())->toBe(User::class)
        ->and((new TeamFactory)->modelName())->toBe(Team::class)
        ->and((new NestedTestFactory)->modelName())->toBe(NestedTest::class)
        ->and((new NestedUserFactory)->modelName())->toBe(NestedUser::class)
        ->and((new NestedTeamFactory)->modelName())->toBe(NestedTeam::class);
});
