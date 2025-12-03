<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dispute_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dispute_id',
        'sender_id',
        'message',
        'attachment_url',
    ];

    /**
     * Get the dispute relationship.
     */
    public function dispute()
    {
        return $this->belongsTo(\App\Models\Dispute::class);
    }

    /**
     * Get the sender relationship.
     */
    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }
}
