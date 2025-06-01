<?php

namespace App\Middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use helpers\TokenStore;

class AuthAgentMiddleware
{
    public static function handle($roles = [])
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'You are not authorized']);
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




        if (!$user || (!empty($roles) && !in_array($decodedToken->role, $roles))) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized Access Denied']);
            exit;
        }

        $_REQUEST['auth_user'] = $user;
    }
}
