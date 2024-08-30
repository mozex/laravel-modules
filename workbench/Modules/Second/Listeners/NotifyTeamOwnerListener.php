<?php

namespace Modules\Second\Listeners;

use Modules\Second\Events\TeamCreatedEvent;

class NotifyTeamOwnerListener
{
    public function handle(TeamCreatedEvent $event): void {}
}
