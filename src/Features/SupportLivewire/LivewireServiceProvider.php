<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Livewire\Livewire;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class LivewireServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::LivewireComponents;
    }

    #[Override]
    public static function shouldRegisterFeature(): bool
    {
        return parent::shouldRegisterFeature()
            && class_exists(Livewire::class);
    }

    #[Override]
    public function boot(): void
    {
        $config = static::asset()->config();
        $namespaces = [];

        static::asset()->scout()->collect()
            ->each(function (array $asset) use ($config, &$namespaces): void {
                $namespace = $this->getName($asset['module']);
                $namespaces[] = $namespace;

                $viewDirectory = sprintf(
                    '%s/%s',
                    dirname($asset['path']),
                    $config['view_path']
                );

                Livewire::addNamespace(
                    namespace: $namespace,
                    viewPath: $viewDirectory,
                    classNamespace: $asset['namespace'],
                    classPath: $asset['path'],
                    classViewPath: $viewDirectory,
                );
            });

        // Workaround: https://github.com/livewire/livewire/pull/10076
        if ($namespaces) {
            Livewire::resolveMissingComponent(function (string $name) use ($namespaces): ?string {
                if (str_contains($name, '::')) {
                    return null;
                }

                $finder = app('livewire.finder');

                foreach ($namespaces as $namespace) {
                    $class = $finder->resolveClassComponentClassName($namespace.'::'.$name);

                    if ($class !== null) {
                        return $class;
                    }
                }

                return null;
            });
        }
    }
}
