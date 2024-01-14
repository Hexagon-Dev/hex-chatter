<?php

namespace App\Observers;

use App\Events\MessageCreatedEvent;
use App\Events\MessageDeletedEvent;
use App\Events\MessageUpdatedEvent;
use App\Models\Message;

class MessageObserver
{
    public function created(Message $message): void
    {
        broadcast(new MessageCreatedEvent($message->chat_id, $message));
    }

    public function updated(Message $message): void
    {
        broadcast(new MessageUpdatedEvent($message->chat_id, $message));
    }

    public function deleted(Message $message): void
    {
        broadcast(new MessageDeletedEvent($message->chat_id, $message->id));
    }
}
