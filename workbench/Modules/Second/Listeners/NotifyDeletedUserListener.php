<?php

namespace Modules\Second\Listeners;

use Modules\First\Events\UserDeletedEvent;

class NotifyDeletedUserListener
{
    public function handle(UserDeletedEvent $event): void
    {
    }
}
