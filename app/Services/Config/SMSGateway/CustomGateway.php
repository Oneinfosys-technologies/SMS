<?php

namespace App\Services\Config\SMSGateway;

use App\Contracts\SMSGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CustomGateway implements SMSGateway
{
    protected $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function send(string $phoneNumber, string $message): bool
    {
        $response = Http::get($this->url, [
            'to' => $phoneNumber,
            'message' => $message,
            'sender' => config('config.sms.sender_id'),
        ]);

        if ($response->successful()) {
            return true;
        }

        throw ValidationException::withMessages([
            'message' => $response->body(),
        ]);
    }
}
