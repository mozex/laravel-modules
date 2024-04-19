<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\First\Models\User;

Broadcast::channel('Chat.{userId}', fn(User $user, int $userId) => $user->id === $userId);
