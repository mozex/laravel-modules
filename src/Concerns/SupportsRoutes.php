<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mozex\Modules\Facades\Modules;

trait SupportsRoutes
{
    public function bootRoutes(): void
    {
        Modules::getModulesAssets(config('modules.route_patterns'))
            ->each(function (array $asset): void {
                $groupName = File::name($asset['path']);

                if ($groupName === 'api') {
                    $group = [
                        'middleware' => config('modules.api_middleware', []),
                        'prefix' => 'api',
                    ];
                } else {
                    $group = [
                        'middleware' => array_merge(
                            config('modules.default_middleware', []),
                            config('modules.route_groups')[$groupName]['middleware'] ?? []
                        ),
                        'as' => config('modules.route_groups')[$groupName]['as'] ?? '',
                        'prefix' => config('modules.route_groups')[$groupName]['prefix'] ?? '',
                    ];

                    if ($groupName === 'localized'
                        || (config('modules.route_groups')[$groupName]['localized'] ?? false)) {
                        $group['middleware'] = array_merge($group['middleware'], [
                            'localeSessionRedirect',
                            'localizationRedirect',
                        ]);

                        $group['prefix'] = sprintf(
                            '%s/%s',
                            LaravelLocalization::setLocale(),
                            ltrim(
                                $group['prefix'],
                                '/'
                            )
                        );

                        if ($group['prefix'] == '/') {
                            $group['prefix'] = '';
                        }
                    }
                }

                Route::group(array_filter($group), function () use ($asset): void {
                    $this->loadRoutesFrom($asset['path']);
                });
            });
    }
}
