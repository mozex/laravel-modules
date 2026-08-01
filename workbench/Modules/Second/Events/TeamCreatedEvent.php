<?php

declare(strict_types=1);

namespace Modules\Second\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
