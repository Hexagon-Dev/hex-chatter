<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteConversationRequest;
use App\Http\Requests\SendMessageRequest;
use App\Jobs\SendMessage;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class MessageController extends Controller
{
    public function getGroups(): array
    {
        $group_data = null;

        $groups = Group::all()->toArray();

        foreach ($groups as $group) {
            $message1 = Message::query()
                ->where('receiver', $group['id'])
                ->where('sender', Auth::id())
                ->where('is_group', true)
                ->orderBy('sent_at', 'desc')
                ->first();
            $message2 = Message::query()
                ->where('receiver', Auth::id())
                ->where('sender', $group['id'])
                ->where('is_group', true)
                ->orderBy('sent_at', 'desc')
                ->first();

            $last_message = $this->compareMessages($message1, $message2);

            $group_data[] = [
                'id' => $group['id'],
                'name' => $group['name'],
                'message' => $last_message['message'],
                'date' => $last_message['sent_at'],
                'is_group' => true,
            ];
        }

        return $group_data;
    }

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

            $last_message = $this->compareMessages($message1, $message2);

            $data[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'message' => $last_message['message'],
                'date' => $last_message['sent_at'],
            ];
        }

        $group_data = $this->getGroups();

        $messages = array_merge($data, $group_data);

        usort($messages, static function($a, $b) {
            $ad = new DateTime($a['date']);
            $bd = new DateTime($b['date']);

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? -1 : 1;
        });

        return view('users', ['data' => $messages]);
    }

    public function compareMessages($message1, $message2): array
    {
        if (!is_null($message1) && !is_null($message2)) {
            if ($message1->toArray()['sent_at'] > $message2->toArray()['sent_at']) {
                return $message1->toArray();
            }

            return $message2->toArray();
        }

        if (!is_null($message1)) {
            return $message1->toArray();
        }

        if (!is_null($message2)) {
            return $message2->toArray();
        }

        return [
            'message' => '',
            'sent_at' => ''
        ];
    }

    public function getUsername(int $id)
    {
        return User::query()->where('id', $id)->get('name')->toArray()[0]['name'];
    }

    public function getMessages(string $recipient)
    {
        $user_id = Auth::id();
        $is_group = false;

        if (is_null($recipient_id = User::query()->where('name', $recipient)->get('id')->first())) {
            $recipient_id = Group::query()->where('name', $recipient)->get('id')->first();
            $is_group = true;
        }

        $recipient_id = $recipient_id->toArray()['id'];

        if($is_group) {
            $sender_messages = Message::query()
                ->where('receiver', $recipient_id)
                ->where('is_group', true)
                ->get()
                ->toArray();
            foreach ($sender_messages as $key => $sender_message) {
                $sender_messages[$key]['sender_name'] = $this->getUsername($sender_message['sender']);
            }
            $recipient_messages = [];
        } else {
            $sender_messages = Message::query()
                ->where('sender', $user_id)
                ->where('receiver', $recipient_id)
                ->get()
                ->toArray();
            foreach ($sender_messages as $key => $sender_message) {
                $sender_messages[$key]['sender_name'] = $this->getUsername($sender_message['sender']);
            }
            $recipient_messages = Message::query()
                ->where('sender', $recipient_id)
                ->where('receiver', $user_id)
                ->get()
                ->toArray();
            foreach ($recipient_messages as $key => $recipient_message) {
                $recipient_messages[$key]['sender_name'] = $this->getUsername($recipient_message['sender']);
            }
        }

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
            'recipient_id' => $recipient_id,
            'recipient_name' => $recipient,
            'messages' => $messages,
            'is_group' => $is_group,
            ];

        return view('messenger', $data);
    }

    public function sendMessage(SendMessageRequest $request): string
    {
        $is_group = false;
        $recipient_id = $request->validated()['recipient'];

        if (Group::query()->find($recipient_id)) {
            $is_group = true;
        } else {
            User::query()->findOrFail($recipient_id);
        }

        $data = [
            'text' => $request->validated()['message'],
            'sender_id' => Auth::id(),
            'recipient_id' => $recipient_id,
            'is_group' => $is_group,
        ];

        SendMessage::dispatchSync($data);

        return response()->json(['message' => 'Message sent'], Response::HTTP_OK);
    }

    public function conversationDelete(DeleteConversationRequest $request): void
    {
        foreach ($request->validated()['id_array'] as $id) {
            if (!is_numeric($id)) {
                throw new UnprocessableEntityHttpException();
            }

            Message::query()
                ->where('sender', Auth::id())
                ->where('receiver', $id)
                ->delete();

            Message::query()
                ->where('sender', $id)
                ->where('receiver', Auth::id())
                ->delete();
        }
    }
}
