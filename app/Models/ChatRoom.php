<?php

namespace App\Models;

use App\Enums\ChatRoomType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_type',
        'last_message_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'room_type' => ChatRoomType::class,
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the participants relationship.
     */
    public function participants()
    {
        return $this->belongsToMany(\App\Models\User::class, 'chat_participants');
    }

    /**
     * Get the messages relationship.
     */
    public function messages()
    {
        return $this->hasMany(\App\Models\ChatMessage::class);
    }
}
