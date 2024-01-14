<?php

namespace App\Models;

use App\Structures\Enums\ChatTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property ChatTypesEnum type
 * @property User[] users
 * @property string created_at
 * @property string updated_at
 * @property Message lastMessage
 * @property Message[] messages
 */
class Chat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => ChatTypesEnum::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
