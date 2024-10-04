<?php

namespace App\Actions\Config;

use App\Services\Config\SMSGateway\Gateway;
use Illuminate\Http\Request;

class TestSMS
{
    public function execute(Request $request)
    {
        $gateway = Gateway::init();

        $gateway->send(phoneNumber: config('config.sms.test_number'), message: 'Thanks for registration. Your OTP is 123456');
    }
}
