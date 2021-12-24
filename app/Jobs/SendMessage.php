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
    public function __construct(array $data)
    {
        $this->text = $data['text'];
        $this->sender_id = $data['sender_id'];
        $this->recipient_id = $data['recipient_id'];
        $this->is_group = $data['is_group'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Message::create([
            'sender' => $this->sender_id,
            'receiver' => $this->recipient_id,
            'is_group' => $this->is_group,
            'message' => $this->text,
        ]);

        if ($this->is_group) {
            $recipient_name = Group::query()->where('id', $this->recipient_id)->firstOrFail()->toArray()['name'];
        } else {
            $recipient_name = User::query()->where('id', $this->recipient_id)->firstOrFail()->toArray()['name'];
        }

        $sender_name = User::query()->where('id', $this->sender_id)->firstOrFail()->toArray()['name'];

        $data = [
            'text' => $this->text,
            'sender_id' => $this->sender_id,
            'recipient_id' => $this->recipient_id,
            'recipient_name' => $recipient_name,
            'sender_name' => $sender_name,
            'datetime' => now()->toDateTimeString(),
            'is_group' => $this->is_group,
        ];

        event(new NewMessageNotification($data));

        Log::debug('Message sent');
    }
}
