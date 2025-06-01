<?php
namespace App\Middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use helpers\TokenStore;

class AuthenticatedMiddleware
{
    public static function handle($roles = [])
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'You are not authorized.Please Login to continue']);
            exit;
        }

        $user = TokenStore::getUserByToken($token);

        $jwtSecret = $_ENV['JWT_SECRET'] ?? null;

        try {
            $decodedToken = JWT::decode($token, new Key($jwtSecret, 'HS256'));
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        if (!$user || ( !$decodedToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized Access Denied']);
            exit;
        }

        $_REQUEST['auth_user'] = $user;
    }
}
