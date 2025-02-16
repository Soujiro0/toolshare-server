<?php
// api/auth/auth.php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    private static $secret_key = JWT_SECRET_KEY;
    private static $algorithm = 'HS256';

    public static function generateToken($user_id, $name, $role)
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // 1 hour expiration

        $payload = [
            'user_id' => $user_id,
            'name' => $name,
            'role' => $role,
            'iat' => $issuedAt,
            'exp' => $expire
        ];

        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getBearerToken()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
