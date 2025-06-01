<?php

namespace App\Controllers;


use helpers\TokenStore;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    public static function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $user = new User();


        $existingUser = $user->findByEmail($data['email']);
        if ($existingUser) {
            http_response_code(409);
            echo json_encode(['error' => 'User with this email already exists']);
            return;
        }


        $user->createUser($data);

        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully']);
    }


    public static function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        $user = (new User())->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password']);
            return;
        }

        

        $payload = [              
            'sub' => $user['id'],              
            'email' => $user['email'],          
            'role' => $user['role'],          
            'iat' => time(),                    
            'exp' => time() + 3600              
        ];

        $jwtSecret = $_ENV['JWT_SECRET'] ?? null;
        $token = JWT::encode($payload, $jwtSecret, 'HS256');
        TokenStore::storeToken($token, $user);

        http_response_code(200);
        echo json_encode([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ]
        ]);
    }



    public static function logout()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        TokenStore::deleteToken($token);
        echo json_encode(['message' => 'Logged out']);
    }
}
