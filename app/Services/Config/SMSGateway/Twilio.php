<?php

namespace App\Services\Config\SMSGateway;

use App\Contracts\SMSGateway;
use Illuminate\Validation\ValidationException;
use Twilio\Rest\Client;

class Twilio implements SMSGateway
{
    public function send(string $phoneNumber, string $message): bool
    {
        $client = new Client(config('config.sms.api_key'), config('config.sms.api_secret'));

        try {
            $response = $client->messages->create($phoneNumber, [
                'from' => config('config.sms.sender_id'),
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }

        return true;
    }
}
