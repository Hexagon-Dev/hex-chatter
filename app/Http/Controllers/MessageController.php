<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function index(Chat $chat): JsonResponse
    {
        $user = auth()->user();

        if (is_null($user)) {
            return response()->json('Unauthorized', 401);
        }

        if (!$user->chats->contains($chat)) {
            return response()->json('Forbidden', 403);
        }

        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->paginate(100);

        return response()->json($messages);
    }

    public function store(Chat $chat, MessageRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (is_null($user)) {
            return response()->json('Unauthorized', 401);
        }

        if (!$user->chats->contains($chat)) {
            return response()->json('Forbidden', 403);
        }

        $data = $request->validated();

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => auth()->user()->id,
            'content' => $data['content'],
        ]);

        return response()->json(MessageResource::make($message), 201);
    }

    public function update(Chat $chat, Message $message, MessageRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (is_null($user)) {
            return response()->json('Unauthorized', 401);
        }

        if (!$user->chats->contains($chat)) {
            return response()->json('Forbidden', 403);
        }

        if ($message->user_id !== $user->id) {
            return response()->json('Forbidden', 403);
        }

        $data = $request->validated();

        $message->update([
            'content' => $data['content'],
        ]);

        return response()->json(MessageResource::make($message));
    }

    public function destroy(Chat $chat, Message $message): JsonResponse
    {
        $user = auth()->user();

        if (is_null($user)) {
            return response()->json('Unauthorized', 401);
        }

        if (!$user->chats->contains($chat)) {
            return response()->json('Forbidden', 403);
        }

        $message->delete();

        return response()->json(null, 204);
    }
}
