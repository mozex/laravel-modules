<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\First\Models\User;

Broadcast::channel('App.Models.User.{id}', function (User $user, string $id): bool {
    return (int) $user->id === (int) $id;
});
