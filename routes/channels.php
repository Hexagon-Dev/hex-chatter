<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function (User $user, int $chatId) {
    return $user->chats()->where('chat_id', $chatId)->exists();
});
