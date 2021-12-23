<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Jobs\SendMessage;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function showUsers()
    {
        $data = null;

        $users = User::all()->toArray();
        foreach ($users as $user) {
            $message1 = Message::query()
                ->where('receiver', $user['id'])
                ->where('sender', Auth::id())
                ->orderBy('sent_at', 'desc')
                ->first();
            $message2 = Message::query()
                ->where('receiver', Auth::id())
                ->where('sender', $user['id'])
                ->orderBy('sent_at', 'desc')
                ->first();

            if (!is_null($message1) && !is_null($message2)) {
                if ($message1->toArray()['sent_at'] > $message2->toArray()['sent_at']) {
                    $last_message = $message1->toArray();
                } else {
                    $last_message = $message2->toArray();
                }
            } else if (!is_null($message1)) {
                $last_message = $message1->toArray();
            } else {
                $last_message = $message2->toArray();
            }
            $data[] = [
                'name' => $user['name'],
                'message' => $last_message['message'],
                'date' => $last_message['sent_at'],
            ];
        }

        return view('users', ['data' => $data]);
    }

    public function writeMessage(string $recipient)
    {
        $user_id = Auth::id();
        $recipient_id = User::query()->where('name', $recipient)->get('id')->firstOrFail()->toArray();

        $sender_messages = Message::query()
            ->where('sender', $user_id)
            ->where('receiver', $recipient_id)
            ->get()
            ->toArray();
        $recipient_messages = Message::query()
            ->where('sender', $recipient_id)
            ->where('receiver', $user_id)
            ->get()
            ->toArray();

        $messages = array_merge($sender_messages, $recipient_messages);

        usort($messages, static function($a, $b) {
            $ad = new DateTime($a['sent_at']);
            $bd = new DateTime($b['sent_at']);

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? -1 : 1;
        });

        $data = [
            'user_id' => $user_id,
            'recipient_id' => $recipient_id['id'],
            'recipient_name' => $recipient,
            'messages' => $messages,
            ];

        return view('messenger', $data);
    }

    public function send(SendMessageRequest $request): string
    {
        $is_group = false;
        $recipient_id = $request->validated()['recipient'];

        if (Group::query()->find($recipient_id)) {
            $is_group = true;
        } else {
            User::query()->findOrFail($recipient_id);
        }

        SendMessage::dispatchSync($request->validated()['message'], Auth::id(), $recipient_id, $is_group);

        return response()->json(['message' => 'Message sent'], Response::HTTP_OK);
    }
}
