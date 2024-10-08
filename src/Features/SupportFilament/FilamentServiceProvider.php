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

        $this->callAfterResolving(PanelRegistry::class, function (PanelRegistry $panelRegistry): void {
            collect($panelRegistry->all())
                ->each(function (Panel $panel): void {
                    if (AssetType::FilamentResources->isActive()) {
                        AssetType::FilamentResources->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset): Panel => $panel->discoverResources(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentPages->isActive()) {
                        AssetType::FilamentPages->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset): Panel => $panel->discoverPages(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentWidgets->isActive()) {
                        AssetType::FilamentWidgets->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset): Panel => $panel->discoverWidgets(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentClusters->isActive()) {
                        AssetType::FilamentClusters->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset): Panel => $panel->discoverClusters(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    /** @var array<string, class-string> $livewireComponents */
                    $livewireComponents = (new ReflectionProperty($panel, 'livewireComponents'))
                        ->getValue($panel);

                    collect($livewireComponents)
                        ->filter(
                            fn (string $class): bool => str_starts_with(
                                haystack: $class,
                                needle: (string) config('modules.modules_namespace')
                            )
                        )
                        ->flip()
                        ->each(fn (string $name, string $class) => Livewire::component($name, $class));
                });
        });
    }
}
