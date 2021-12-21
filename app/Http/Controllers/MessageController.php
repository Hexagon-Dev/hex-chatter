<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Jobs\SendMessage;
use App\Models\Group;
use App\Models\User;

class MessageController extends Controller
{
    public function send(SendMessageRequest $request, int $id, bool $is_group): void
    {
        if ($is_group) {
            Group::query()->findOrFail($id);
        } else {
            User::query()->findOrFail($id);
        }

        SendMessage::dispatchAfterResponse($request->validated()['text'], $id, $is_group);

    }
}
