<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param string $text
     * @param int $id
     * @param bool $is_group
     * @return void
     */
    public function handle(string $text, int $id, bool $is_group): void
    {
        Message::query()->insert([
            'receiver' => $id,
            'is_group' => $is_group,
            'message' => $text,
            'sent_at' => now()->timestamp,
        ]);

        // TODO: Broadcast to recipients the message
    }
}
