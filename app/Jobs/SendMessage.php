<?php

namespace App\Jobs;

use App\Events\NewMessageNotification;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessage
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $text;
    public int $sender_id;
    public int $recipient_id;
    public bool $is_group;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $text, int $sender_id, int $recipient_id, bool $is_group)
    {
        $this->text = $text;
        $this->sender_id = $sender_id;
        $this->recipient_id = $recipient_id;
        $this->is_group = $is_group;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $message = new Message;
        $message->query()->insert([
            'sender' => $this->sender_id,
            'receiver' => $this->recipient_id,
            'is_group' => $this->is_group,
            'message' => $this->text,
        ]);

        if ($this->is_group) {
            $name = Group::query()->where('id', $this->recipient_id)->firstOrFail()->toArray()['name'];
        } else {
            $name = User::query()->where('id', $this->recipient_id)->firstOrFail()->toArray()['name'];
        }

        event(new NewMessageNotification($this->text, $name, now()->toDateTimeString(), $this->recipient_id, $this->is_group));

        Log::debug('Message sent');
    }
}
