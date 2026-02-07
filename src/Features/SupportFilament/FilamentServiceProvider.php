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
    public static function asset(): array
    {
        return [
            AssetType::FilamentResources,
            AssetType::FilamentPages,
            AssetType::FilamentWidgets,
            AssetType::FilamentClusters,
        ];
    }

    public static function shouldRegisterFeature(): bool
    {
        return parent::shouldRegisterFeature()
            && class_exists(Filament::class);
    }

    public function register(): void
    {
        $this->callAfterResolving(PanelRegistry::class, function (PanelRegistry $panelRegistry): void {
            $resources = AssetType::FilamentResources->isActive()
                ? AssetType::FilamentResources->scout()->collect()
                : collect();

            $pages = AssetType::FilamentPages->isActive()
                ? AssetType::FilamentPages->scout()->collect()
                : collect();

            $widgets = AssetType::FilamentWidgets->isActive()
                ? AssetType::FilamentWidgets->scout()->collect()
                : collect();

            $clusters = AssetType::FilamentClusters->isActive()
                ? AssetType::FilamentClusters->scout()->collect()
                : collect();

            $modulesNamespace = (string) config('modules.modules_namespace');
            $livewireReflection = new ReflectionProperty(Panel::class, 'livewireComponents');

            collect($panelRegistry->all())
                ->each(function (Panel $panel) use ($resources, $pages, $widgets, $clusters, $modulesNamespace, $livewireReflection): void {
                    $panelId = strtolower($panel->getId());

                    $resources->where('panel', $panelId)
                        ->each(fn (array $asset): Panel => $panel->discoverResources(
                            in: $asset['path'],
                            for: $asset['namespace']
                        ));

                    $pages->where('panel', $panelId)
                        ->each(fn (array $asset): Panel => $panel->discoverPages(
                            in: $asset['path'],
                            for: $asset['namespace']
                        ));

                    $widgets->where('panel', $panelId)
                        ->each(fn (array $asset): Panel => $panel->discoverWidgets(
                            in: $asset['path'],
                            for: $asset['namespace']
                        ));

                    $clusters->where('panel', $panelId)
                        ->each(fn (array $asset): Panel => $panel->discoverClusters(
                            in: $asset['path'],
                            for: $asset['namespace']
                        ));

                    /** @var array<string, class-string> $livewireComponents */
                    $livewireComponents = $livewireReflection->getValue($panel);

                    collect($livewireComponents)
                        ->filter(
                            fn (string $class): bool => str_starts_with(
                                haystack: $class,
                                needle: $modulesNamespace
                            )
                        )
                        ->flip()
                        ->each(fn (string $name, string $class) => Livewire::component($name, $class));
                });
        });
    }
}
