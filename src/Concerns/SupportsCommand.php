<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Console\Command;
use Mozex\Modules\Facades\Modules;
use ReflectionClass;
use ReflectionException;

trait SupportsCommand
{
    public function bootCommands(): void
    {
        $this->commands(
            Modules::getModulesAssets(config('modules.command_patterns'))
                ->map(Modules::makeNamespaceForAsset(...))
                ->filter($this->isCommand(...))
                ->toArray()
        );
    }

    /**
     * @throws ReflectionException
     */
    public function isCommand(string $namespace): bool
    {
        return is_subclass_of($namespace, Command::class)
            && ! (new ReflectionClass($namespace))->isAbstract();
    }
}
