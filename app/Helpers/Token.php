<?php


namespace App\Helpers;

use Carbon\Carbon;

class Token
{
    public static function getEncodedEmailToken($email, $userId)
    {
        $limit_time = Carbon::now()->addMinutes(15);
        return base64_encode("$email|$limit_time|$userId");
    }

    public static function getEncodedEmailCode($email, $userId, $code)
    {
        $limit_time = Carbon::now()->addMinutes(15);
        return base64_encode("$code|$email|$limit_time|$userId");
    }

    public static function getEmailByEncodedToken($encoded_token)
    {
        return explode('|', self::decodeToken($encoded_token))[0];
    }

    public static function getTokenLifeByEncodedToken($encoded_token)
    {
        return explode('|', self::decodeToken($encoded_token))[1];
    }

    public static function getUserIdByEncodedToken($encoded_token)
    {
        return explode('|', self::decodeToken($encoded_token))[2];
    }

    private static function decodeToken($token)
    {
        return base64_decode($token);
    }
}
