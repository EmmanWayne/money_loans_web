<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentNotification extends Model
{
    protected $fillable = [
        'notification_id',
        'channel',
        'recipient',
        'message',
        'status',
        'error_message',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(DatabaseNotification::class, 'notification_id');
    }
}
