<?php

namespace Mozex\Modules\Features\SupportFilament;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Livewire\Livewire;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use ReflectionProperty;

class FilamentServiceProvider extends Feature
{
    public function register(): void
    {
        if (! class_exists(Filament::class)) {
            return;
        }

        $this->callAfterResolving(PanelRegistry::class, function (PanelRegistry $panelRegistry) {
            collect($panelRegistry->all())
                ->each(function (Panel $panel): void {
                    if (AssetType::FilamentResources->isActive()) {
                        AssetType::FilamentResources->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverResources(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentPages->isActive()) {
                        AssetType::FilamentPages->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverPages(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentWidgets->isActive()) {
                        AssetType::FilamentWidgets->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverWidgets(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentClusters->isActive()) {
                        AssetType::FilamentClusters->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverClusters(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    collect(
                        (new ReflectionProperty($panel, 'livewireComponents'))
                            ->getValue($panel)
                    )
                        ->filter(
                            fn (string $class) => str_starts_with(
                                haystack: $class,
                                needle: config('modules.modules_namespace')
                            )
                        )
                        ->flip()
                        ->each(Livewire::component(...));
                });
        });
    }
}
