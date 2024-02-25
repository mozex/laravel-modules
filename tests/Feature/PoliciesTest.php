<?php

use App\Models\Nested\NestedTest;
use App\Models\Test;
use App\Policies\Nested\NestedTestPolicy;
use App\Policies\TestPolicy;
use Illuminate\Support\Facades\Gate;
use Modules\First\Models\Nested\NestedUser;
use Modules\First\Models\User;
use Modules\First\Policies\Nested\NestedUserPolicy;
use Modules\First\Policies\UserPolicy;
use Modules\Second\Models\Nested\NestedTeam;
use Modules\Second\Models\Team;
use Modules\Second\Policies\Nested\NestedTeamPolicy;
use Modules\Second\Policies\TeamPolicy;

it('can guess policy name', function () {
    expect(Gate::getPolicyFor(Test::class))->toBeInstanceOf(TestPolicy::class)
        ->and(Gate::getPolicyFor(User::class))->toBeInstanceOf(UserPolicy::class)
        ->and(Gate::getPolicyFor(Team::class))->toBeInstanceOf(TeamPolicy::class)
        // Laravel does not support nested policy by default
        // ->and(Gate::getPolicyFor(NestedTest::class))->toBeInstanceOf(NestedTestPolicy::class)
        ->and(Gate::getPolicyFor(NestedUser::class))->toBeInstanceOf(NestedUserPolicy::class)
        ->and(Gate::getPolicyFor(NestedTeam::class))->toBeInstanceOf(NestedTeamPolicy::class);
});
