<?php

namespace Mozex\Modules;

use Closure;
use Mozex\Modules\Enums\AssetType;
use Spatie\Regex\Regex;

class Modules
{
    public string $base_path;

    /** @var array<string, array<string, mixed>> */
    public array $routeGroups = [];

    /** @var array<string, Closure> */
    public array $registerRoutesUsing = [];

    public function __construct()
    {
        $this->setBasePath();

        $this->routeGroup(
            name: 'api',
            prefix: 'api',
            middleware: ['api']
        );

        $this->routeGroup(
            name: 'web',
            middleware: ['web'],
        );
    }

    public function routeGroup(string|callable $name, mixed ...$args): void
    {
        $this->routeGroups[$name] = $args;
    }

    public function registerRoutesUsing(string|callable $name, Closure $closure): void
    {
        $this->registerRoutesUsing[$name] = $closure;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getRouteGroups(): array
    {
        return $this->routeGroups;
    }

    /**
     * @return array<string, Closure>
     */
    public function getRegisterRoutesUsing(): array
    {
        return $this->registerRoutesUsing;
    }

    public function setBasePath(?string $path = null): void
    {
        $this->base_path = $path ?? base_path();
    }

    public function basePath(string $path = ''): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->base_path, '/'),
            ltrim($path, '/')
        );
    }

    public function modulesPath(string $path = ''): string
    {
        return $this->basePath(
            sprintf(
                '%s/%s',
                config('modules.modules_directory'),
                ltrim($path, '/')
            )
        );
    }

    public function moduleNameFromNamespace(string $namespace): string
    {
        return Regex::match(
            pattern: '/'.config('modules.modules_directory').'\\\\(.*?)\\\\/',
            subject: $namespace
        )->groupOr(1, '');
    }

    public function moduleNameFromPath(string $path): ?string
    {
        return Regex::match(
            pattern: '/'.config('modules.modules_directory').'\/(.*?)\//',
            subject: str($path)->replace('\\', '/')->toString()
        )->groupOr(1, '');
    }

    /**
     * @return array<array-key, class-string>
     */
    public function seeders(): array
    {
        if (AssetType::Seeders->isDeactive()) {
            return [];
        }

        return AssetType::Seeders->scout()->collect()
            ->pluck('namespace')
            ->toArray();
    }
}
