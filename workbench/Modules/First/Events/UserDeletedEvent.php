<?php

namespace Modules\First\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct() {}
}
