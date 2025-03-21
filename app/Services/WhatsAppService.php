<?php

namespace App\Services;

use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class WhatsAppService
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

    public function sendMessage(string $to, string $message)
    {
        try {
            $this->client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:{$this->fromNumber}",
                    'body' => $message
                ]
            );
            
            return true;
        } catch (Exception $e) {
            Log::error('Error enviando WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}
