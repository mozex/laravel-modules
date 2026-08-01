<?php

declare(strict_types=1);

use App\Models\Test;
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

it('re-registers the same guesser closure after a fallback guess', function (): void {
    $gate = app(Illuminate\Contracts\Auth\Access\Gate::class);
    $property = new ReflectionProperty($gate, 'guessPolicyNamesUsingCallback');

    $before = $property->getValue($gate);

    Gate::getPolicyFor(Test::class);

    expect($property->getValue($gate))->toBe($before);
});

it('can guess policy name', function (): void {
    expect(Gate::getPolicyFor(Test::class))->toBeInstanceOf(TestPolicy::class)
        ->and(Gate::getPolicyFor(User::class))->toBeInstanceOf(UserPolicy::class)
        ->and(Gate::getPolicyFor(Team::class))->toBeInstanceOf(TeamPolicy::class)
        ->and(Gate::getPolicyFor(NestedUser::class))->toBeInstanceOf(NestedUserPolicy::class)
        ->and(Gate::getPolicyFor(NestedTeam::class))->toBeInstanceOf(NestedTeamPolicy::class);
});
