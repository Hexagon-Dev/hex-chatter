<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public bool $timestamps = false;

    public $guarded = [];

    public function setSentAtAttribute(): void
    {
        $this->attributes['sent_at'] = now()->timestamp;
    }
}
