<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatabaseNotification extends BaseDatabaseNotification
{
    public function sentNotifications(): HasMany
    {
        return $this->hasMany(SentNotification::class, 'notification_id');
    }

    public function markAsRead(): void
    {
        parent::markAsRead();
        
        // Puedes agregar lógica adicional aquí si es necesario
    }
}
