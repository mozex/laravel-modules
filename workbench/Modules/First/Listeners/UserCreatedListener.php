<?php

declare(strict_types=1);

namespace Modules\First\Listeners;

use App\Events\TestEvent;

class UserCreatedListener
{
    public function handle(TestEvent $event): void {}
}
