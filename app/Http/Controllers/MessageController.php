<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Jobs\SendMessage;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $data = array('user_id' => $user_id);

        return view('broadcast', $data);
    }

    public function send(SendMessageRequest $request): string
    {
        $is_group = false;
        $id = $request->validated()['recipient'];

        if (Group::query()->find($id)) {
            $is_group = true;
        } else {
            User::query()->findOrFail($id);
        }

        SendMessage::dispatchSync($request->validated()['message'], $id, $is_group);

        return 'Message sent.';
    }
}
