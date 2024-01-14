<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\User;
use App\Structures\Enums\ChatTypesEnum;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (is_null($user)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(['chats' => ChatResource::collection($user->chats)]);
    }

    public function store(ChatRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (is_null($user)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validated();

        $users = collect($data['users'])->pluck('id')->push($user->id)->unique()->values()->filter()->toArray();

        if (count($users) < 2) {
            return response()->json(['message' => 'You must specify at least one user'], 422);
        }

        if (count($users) > 2) {
            return response()->json(['message' => 'You can only create a chat with two users'], 422);
        }

        if (Chat::where('type', ChatTypesEnum::PERSONAL)->whereIn('id', $users)->exists()) {
            return response()->json(['message' => 'You already have a private chat with this user'], 422);
        }

        /** @var Chat $chat */
        $chat = $user->chats()->create(['type' => ChatTypesEnum::from($data['type'])]);

        $chat->users()->sync($users);

        return response()->json(['chat' => ChatResource::make($chat)], 201);
    }
}
