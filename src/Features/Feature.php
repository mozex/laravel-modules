<?php

namespace Mozex\Modules\Features;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;

abstract class Feature extends ServiceProvider
{
    /**
     * @return AssetType|array<AssetType>
     */
    abstract public static function asset(): AssetType|array;

    public static function shouldRegisterFeature(): bool
    {
        foreach (Arr::wrap(static::asset()) as $asset) {
            if ($asset->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }

    protected function getName(string $name): string
    {
        $str = str($name);

        if ($name === $str->upper()->toString()) {
            return $str->lower()->toString();
        }

        return $str
            ->replaceMatches('/(?<! )[A-Z]/', '-$0')
            ->replaceFirst('-', '')
            ->lower()
            ->toString();
    }

    /**
     * @param  array{module: string, path: string, namespace: class-string}  $asset
     */
    protected function getViewName(array $asset, AssetType $type): string
    {
        foreach ($type->patterns() as $pattern) {
            $sub = str(realpath($asset['path']))
                ->replaceFirst(realpath(Modules::modulesPath()), '')
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
                $this->getName($asset['module']),
                $sub->map($this->getName(...))
                    ->implode('.')
            );
        }

        return sprintf(
            '%s::%s',
            $this->getName($asset['module']),
            strtolower(class_basename($asset['namespace']))
        );
    }
}
