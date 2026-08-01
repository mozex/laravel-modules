<?php

declare(strict_types=1);

namespace Mozex\Modules\Features\SupportBladeComponents;

use Illuminate\View\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Mozex\Modules\Facades\Modules;
use Override;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

/**
 * @method \Illuminate\Support\Collection<array-key, array{module: string, path: string, namespace: class-string, alias?: string}> collect()
 */
class BladeComponentsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::BladeComponents;
    }

    #[Override]
    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(Component::class));
    }

    /**
     * @param  array<array-key, string|DiscoveredClass>  $result
     * @return array<array-key, array{module: string, path: string, namespace: class-string, alias: string}>
     */
    #[Override]
    public function transform(array $result): array
    {
        return collect(parent::transform($result))
            ->map(fn (array $item): array => [
                ...$item,
                'alias' => Modules::viewName($item, $this->asset()),
            ])
            ->all();
    }
}
