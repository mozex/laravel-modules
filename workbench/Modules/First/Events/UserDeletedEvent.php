<?php

declare(strict_types=1);

namespace Modules\First\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
