<?php

namespace App\Notifications\Channels;

use App\Models\SentNotification;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->fromNumber = config('services.twilio.whatsapp_from');
    }

    public function send(object $notifiable, Notification $notification)
    {
        try {
            $message = $notification->toWhatsapp($notifiable);
            
            if (!$notifiable->whatsapp) {
                return;
            }

            $response = $this->client->messages->create(
                "whatsapp:+{$notifiable->whatsapp}",
                [
                    'from' => "whatsapp:{$this->fromNumber}",
                    'body' => $message
                ]
            );
            
            // Registrar el envío exitoso
            SentNotification::create([
                'notification_id' => $notification->id ?? null,
                'channel' => 'whatsapp',
                'recipient' => $notifiable->whatsapp,
                'message' => $message,
                'status' => 'sent',
            ]);

            Log::info('WhatsApp enviado exitosamente', [
                'to' => $notifiable->whatsapp,
                'message' => $message
            ]);
        } catch (Exception $e) {
            // Registrar el error
            SentNotification::create([
                'notification_id' => $notification->id ?? null,
                'channel' => 'whatsapp',
                'recipient' => $notifiable->whatsapp,
                'message' => $message ?? '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Error enviando WhatsApp: ' . $e->getMessage());
        }
    }
}
