<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\First\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

