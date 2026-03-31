<?php

namespace Mozex\Modules\Features\SupportCaching;

use Illuminate\Console\Command;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;

class ListCommand extends Command
{
    protected $signature = 'modules:list';

    protected $description = 'List all modules and their discovered assets.';

    public function handle(): void
    {
        $modules = $this->getModules();

        if ($modules === []) {
            $this->components->warn('No modules found.');

            return;
        }

        $counts = $this->getAssetCounts();
        $isFirst = true;

        foreach ($modules as $name => $module) {
            if (! $isFirst) {
                $this->newLine();
            }

            $isFirst = false;
            $status = $module['active'] ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>';
            $order = $module['order'] === 9999 ? '' : " | Order: {$module['order']}";

            $this->line("  <fg=bright-white;options=bold>{$name}</> [{$status}{$order}]");

            $moduleCounts = $counts[$name] ?? [];

            if ($moduleCounts === []) {
                $this->line('  <fg=gray>No assets discovered</>');

                continue;
            }

            $this->table(
                ['Asset', 'Count'],
                collect($moduleCounts)
                    ->map(fn (int $count, string $type): array => [
                        AssetType::from($type)->title(),
                        $count,
                    ])
                    ->values()
                    ->toArray(),
            );
        }
    }

    /**
     * @return array<string, array{active: bool, order: int}>
     */
    protected function getModules(): array
    {
        $modulesPath = Modules::modulesPath();

        if (! is_dir($modulesPath)) {
            return [];
        }

        $directories = glob($modulesPath.'/*', GLOB_ONLYDIR);

        if ($directories === false || $directories === []) {
            return [];
        }

        /** @var array<string, array{active?: bool, order?: int}> $config */
        $config = config('modules.modules', []);

        $modules = [];

        foreach ($directories as $directory) {
            $name = basename($directory);
            $modules[$name] = [
                'active' => $config[$name]['active'] ?? true,
                'order' => $config[$name]['order'] ?? 9999,
            ];
        }

        uasort($modules, fn (array $a, array $b): int => $a['order'] <=> $b['order']);

        return $modules;
    }

    /**
     * @return array<string, array<string, int>>
     */
    protected function getAssetCounts(): array
    {
        $counts = [];

        foreach (AssetType::cases() as $type) {
            $scout = $type->scout();

            if ($scout === null) {
                continue;
            }

            foreach ($scout->collect() as $asset) {
                $module = $asset['module'];
                $counts[$module][$type->value] = ($counts[$module][$type->value] ?? 0) + 1;
            }
        }

        return $counts;
    }
}
