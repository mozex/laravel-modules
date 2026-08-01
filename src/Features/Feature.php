<?php

namespace Mozex\Modules\Features;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Override;

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

    #[Override]
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
        return Modules::kebabName($name);
    }

    /**
     * @param  array{module: string, path: string, namespace: class-string}  $asset
     */
    protected function getViewName(array $asset, AssetType $type): string
    {
        return Modules::viewName($asset, $type);
    }
}
