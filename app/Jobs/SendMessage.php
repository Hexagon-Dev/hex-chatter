<?php

namespace App\Jobs;

use App\Events\NewMessageNotification;
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
    public int $id;
    public bool $is_group;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $text, int $id, bool $is_group)
    {
        $this->text = $text;
        $this->id = $id;
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
            'receiver' => $this->id,
            'is_group' => $this->is_group,
            'message' => $this->text,
        ]);

        event(new NewMessageNotification($this->text, User::query()->where('id', $this->id)->firstOrFail()->toArray()['name'], $this->id)   );

        Log::debug('Message sent');
    }
}
