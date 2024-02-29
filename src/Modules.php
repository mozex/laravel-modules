<?php

namespace Mozex\Modules;

use Mozex\Modules\Enums\AssetType;
use Spatie\Regex\Regex;

class Modules
{
    public string $base_path;

    public array $routeGroups = [];

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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getRouteGroups(): array
    {
        return $this->routeGroups;
    }

    public function getRouteGroup(string $name): array
    {
        if (! isset($this->getRouteGroups()[$name])) {
            return [];
        }

        return collect($this->getRouteGroups()[$name])
            ->filter()
            ->map(fn ($value) => is_callable($value) ? $value() : $value)
            ->toArray();
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
