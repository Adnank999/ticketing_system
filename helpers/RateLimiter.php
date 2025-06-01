<?php
namespace helpers;

class RateLimiter
{
    public static function limit($key, $limit = 5, $seconds = 60)
    {
        $cacheFile = __DIR__ . '/../storage/cache/rate_limit_' . md5($key);
        
        if (!file_exists(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0777, true);
        }

        $data = [
            'count' => 0,
            'expires_at' => time() + $seconds
        ];

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);

            if (time() > $data['expires_at']) {
                $data = ['count' => 0, 'expires_at' => time() + $seconds];
            }
        }

        if ($data['count'] >= $limit) {
            http_response_code(429);
            echo json_encode(['error' => 'Rate limit exceeded. Please try again later.']);
            exit;
        }

        $data['count']++;
        file_put_contents($cacheFile, json_encode($data));
    }
}
