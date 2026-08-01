<?php

declare(strict_types=1);

use Mozex\Modules\Facades\Modules as ModulesFacade;
use Mozex\Modules\Modules;

it('resolves the same instance from the container and the facade', function (): void {
    expect(app(Modules::class))
        ->toBe(app(Modules::class))
        ->toBe(ModulesFacade::getFacadeRoot());
});

it('shares facade-registered route groups with injected instances', function (): void {
    ModulesFacade::routeGroup(
        name: 'admin',
        prefix: 'admin',
        middleware: ['web']
    );

    expect(app(Modules::class)->getRouteGroups())
        ->toHaveKey('admin');
});
