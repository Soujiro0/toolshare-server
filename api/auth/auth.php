<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Authentication class for handling JWT-based authentication.
 */
class Auth
{
    // Secret key for JWT signing (defined in config)
    private static $secret_key = JWT_SECRET_KEY;

    // Algorithm used for JWT encoding and decoding
    private static $algorithm = 'HS256';

    /**
     * Generates a JWT token for authenticated users.
     *
     * @param int $user_id The user's unique ID.
     * @param string $name The user's name.
     * @param string $role The user's role (e.g., 'admin', 'faculty').
     * @return string The generated JWT token.
     */
    public static function generateToken($user_id, $name, $role)
    {
        $issuedAt = time();  // Token issue time
        $expire = $issuedAt + 3600; // Token expires in 1 hour

        $payload = [
            'user_id' => $user_id,
            'name' => $name,
            'role' => $role,
            'iat' => $issuedAt, // Issued at
            'exp' => $expire    // Expiration time
        ];

        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    /**
     * Validates and decodes a JWT token.
     *
     * @param string $token The JWT token to validate.
     * @return object|false Decoded token payload if valid, false if invalid.
     */
    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Extracts the Bearer token from the Authorization header.
     *
     * @return string|null The extracted token, or null if not found.
     */
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
