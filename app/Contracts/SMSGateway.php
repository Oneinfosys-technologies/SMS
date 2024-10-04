<?php

namespace App\Contracts;

interface SMSGateway
{
    public function send(string $phoneNumber, string $message): bool;
}
