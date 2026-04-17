<?php
/**
 * Server-side token verification.
 *
 * @package LazyCaptcha
 */

if (!defined('ABSPATH')) {
    exit;
}

class LazyCaptcha_Verifier
{
    public function verify(string $token, ?string $remote_ip = null): array
    {
        if ($token === '') {
            return ['success' => false, 'error' => 'missing_token'];
        }

        $secret = trim((string) get_option('lazycaptcha_secret_key', ''));
        if ($secret === '') {
            return ['success' => false, 'error' => 'misconfigured'];
        }

        $base = rtrim((string) get_option('lazycaptcha_base_url', 'https://lazycaptcha.com'), '/');
        $url  = $base . '/api/captcha/v1/verify';

        $body = [
            'secret'    => $secret,
            'token'     => $token,
            'remote_ip' => $remote_ip ?? $this->client_ip(),
        ];

        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'body'    => wp_json_encode($body),
            'timeout' => 5,
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'error' => 'request_failed', 'detail' => $response->get_error_message()];
        }

        $decoded = json_decode((string) wp_remote_retrieve_body($response), true);
        return is_array($decoded) ? $decoded : ['success' => false, 'error' => 'invalid_response'];
    }

    public function check(string $token, ?string $remote_ip = null): bool
    {
        return (bool) ($this->verify($token, $remote_ip)['success'] ?? false);
    }

    private function client_ip(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }
        return sanitize_text_field($ip);
    }
}
