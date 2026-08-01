<?php

namespace Mozex\Modules\Features\SupportListeners;

use Illuminate\Foundation\Events\DiscoverEvents;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use Override;
use ReflectionMethod;
use ReflectionProperty;
use SplFileInfo;

class ListenersServiceProvider extends Feature
{
    private ?ReflectionProperty $callbackProperty = null;

    private ?ReflectionMethod $classFromFileMethod = null;

    public static function asset(): AssetType
    {
        return AssetType::Listeners;
    }

    /**
     * Installed at register time: event discovery runs in the application's
     * booting callbacks, before any provider's boot() is called.
     */
    #[Override]
    public function register(): void
    {
        $callback = function (SplFileInfo $file, string $basePath) use (&$callback) {
            if (Modules::moduleNameFromPath($file->getRealPath())) {
                return str($file->getRealPath())
                    ->after(realpath(Modules::basePath()).DIRECTORY_SEPARATOR)
                    ->before('.php')
                    ->replace(DIRECTORY_SEPARATOR, '\\')
                    ->ucfirst()
                    ->toString();
            }

            try {
                $discoverEvent = $this->app->make(DiscoverEvents::class);

                ($this->callbackProperty ??= new ReflectionProperty($discoverEvent, 'guessClassNamesUsingCallback'))
                    ->setValue(null, null);

                return ($this->classFromFileMethod ??= new ReflectionMethod($discoverEvent, 'classFromFile'))
                    ->invoke($discoverEvent, $file, $basePath);
            } finally {
                // Re-register the same closure so per-call allocations and
                // closure-identity churn are avoided on the fallback path.
                DiscoverEvents::guessClassNamesUsing($callback);
            }
        };

        DiscoverEvents::guessClassNamesUsing($callback);
    }
}
