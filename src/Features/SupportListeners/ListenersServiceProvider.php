<?php

namespace Mozex\Modules\Features\SupportListeners;

use Illuminate\Foundation\Events\DiscoverEvents;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use ReflectionMethod;
use ReflectionProperty;
use SplFileInfo;

class ListenersServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Listeners->isDeactive()) {
            return;
        }

        DiscoverEvents::guessClassNamesUsing(function (SplFileInfo $file, string $basePath) {
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

                (new ReflectionProperty($discoverEvent, 'guessClassNamesUsingCallback'))
                    ->setValue(null, null);

                $reflection = (new ReflectionMethod($discoverEvent, 'classFromFile'));

                return $reflection->invoke($discoverEvent, $file, $basePath);
            } finally {
                $this->boot();
            }
        });
    }
}
