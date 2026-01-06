<?php

namespace Midtrans;

class Config
{
    public static string $serverKey = '';
    public static string $clientKey = '';
    public static bool $isProduction = false;
    public static bool $isSanitized = true;
    public static bool $is3ds = true;
}

class Snap
{
    private static function baseUrl(): string
    {
        return Config::$isProduction ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
    }

    public static function getSnapToken(array $params): ?array
    {
        $endpoint = self::baseUrl() . '/snap/v1/transactions';
        $body = json_encode($params);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, Config::$serverKey . ':');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 200 && $status < 300 && $response) {
            $data = json_decode($response, true);
            if (isset($data['token'])) {
                return $data;
            }
        }

        return null;
    }
}

// Initialize configuration from app config if helpers are loaded
if (function_exists('config')) {
    Config::$serverKey = config('midtrans.server_key', Config::$serverKey);
    Config::$clientKey = config('midtrans.client_key', Config::$clientKey);
    Config::$isProduction = config('midtrans.is_production', Config::$isProduction);
    Config::$isSanitized = true;
    Config::$is3ds = true;
}
