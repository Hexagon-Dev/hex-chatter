<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', static function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{toUserId}', static function ($user, $toUserId) {
    return $user->id === $toUserId;
});

Broadcast::channel('test', static function ($user) {
    return true;
});
