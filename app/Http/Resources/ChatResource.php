<?php

namespace App\Http\Resources;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Chat $this */

        return array(
            'id' => $this->id,
            'type' => $this->type,
            'users' => $this->users,
            'last_message' => MessageResource::make($this->lastMessage),
        );
    }
}
