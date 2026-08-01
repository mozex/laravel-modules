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

    private ?string $namespacePattern = null;

    private ?string $pathPattern = null;

    private ?string $modulesRealPath = null;

    /** @var array<string, string> */
    private array $kebabNames = [];

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
        $this->namespacePattern = null;
        $this->pathPattern = null;
        $this->modulesRealPath = null;
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
        $this->namespacePattern ??= '/'.config('modules.modules_directory').'\\\\(.*?)\\\\/';

        return Regex::match(
            pattern: $this->namespacePattern,
            subject: $namespace
        )->groupOr(1, '');
    }

    public function moduleNameFromPath(string $path): ?string
    {
        $this->pathPattern ??= '/'.config('modules.modules_directory').'\/(.*?)\//';

        return Regex::match(
            pattern: $this->pathPattern,
            subject: str($path)->replace('\\', '/')->toString()
        )->groupOr(1, '');
    }

    public function kebabName(string $name): string
    {
        return $this->kebabNames[$name] ??= str($name)
            ->replaceMatches('/([A-Z]+)([A-Z][a-z])/', '$1-$2')
            ->replaceMatches('/([a-z])([A-Z])/', '$1-$2')
            ->lower()
            ->toString();
    }

    /**
     * @param  array{module: string, path: string, namespace: class-string}  $asset
     */
    public function viewName(array $asset, AssetType $type): string
    {
        $this->modulesRealPath ??= (string) realpath($this->modulesPath());

        foreach ($type->patterns() ?? [] as $pattern) {
            $sub = str((string) realpath($asset['path']))
                ->replaceFirst($this->modulesRealPath, '')
                ->replace('\\', '/')
                ->replaceFirst('/', '')
                ->replaceMatches(
                    str($pattern)
                        ->replaceFirst('*', '.*?')
                        ->replace('/', '\/')
                        ->prepend('/')
                        ->append('\//')
                        ->toString(),
                    ''
                )
                ->before('.php')
                ->explode('/')
                ->filter();

            if ($sub->first() === $asset['module'] && $sub->count() > 1) {
                continue;
            }

            return sprintf(
                '%s::%s',
                $this->kebabName($asset['module']),
                $sub->map($this->kebabName(...))
                    ->implode('.')
            );
        }

        return sprintf(
            '%s::%s',
            $this->kebabName($asset['module']),
            strtolower(class_basename($asset['namespace']))
        );
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
