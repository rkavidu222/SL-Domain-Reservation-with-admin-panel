<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class OtpHelper
{
    public static function sendOtpSms(string $mobile, string $otp): bool
    {
        $data = [
            'api_token' => env('SMS_API_TOKEN', 'your_default_token'),
            'recipient' => self::normalizeSriLankanMobile($mobile),
            'sender_id' => 'SLHosting',
            'type' => 'plain',
            'message' => "Your OTP code is: {$otp}",
        ];

        $url = 'https://sms.serverclub.lk/api/http/sms/send';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        Log::info('OTP sent', ['mobile' => $mobile, 'otp' => $otp, 'response' => $response]);

        return $response ? true : false;
    }

    public static function normalizeSriLankanMobile(string $number): string

    {
        $number = trim($number);

        if (str_starts_with($number, '+')) {
            $number = substr($number, 1);
        }

        if (str_starts_with($number, '0')) {
            $number = '94' . substr($number, 1);
        }

        if (!str_starts_with($number, '94')) {
            $number = '94' . $number;
        }

        return $number;
    }
}
