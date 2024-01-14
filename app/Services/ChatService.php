<?php

namespace App\Services;

use App\Models\Chat;
use App\Structures\Enums\ChatTypesEnum;

class ChatService
{
    public static function create(ChatTypesEnum $type)
    {
        return Chat::create(['type' => $type]);
    }
}
