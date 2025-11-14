<?php

use Carbon\Carbon;
use App\Models\User;

if (!function_exists('format_success')) {
    /**
     * Format a successful API response body.
     */
    function format_success(string $message, array $data = []): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'errors' => null,
            'data' => $data,
        ];
    }
}

if (!function_exists('format_error')) {
    /**
     * Format an error API response body.
     */
    function format_error(string $message, array $errors = []): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ];
    }
}

if (!function_exists('base64url_encode')) {
    /**
     * Encode string to base64url (RFC 7515)
     */
    function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('jwt_encode')) {
    /**
     * Minimal JWT encoder using HS256 without external deps.
     *
     * @param array $payload
     * @param string $secret
     * @param string $alg Only HS256 supported
     * @return string JWT token
     */
    function jwt_encode(array $payload, string $secret, string $alg = 'HS256'): string
    {
        $header = ['alg' => $alg, 'typ' => 'JWT'];
        $segments = [];
        $segments[] = base64url_encode(json_encode($header));
        $segments[] = base64url_encode(json_encode($payload));
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $secret, true);
        $segments[] = base64url_encode($signature);
        return implode('.', $segments);
    }
}

if (!function_exists('jwt_default_payload')) {
    /**
     * Build default JWT payload for a user.
     */
    function jwt_default_payload(User $user, int $ttlSeconds = 7200): array
    {
        $now = time();
        $exp = $now + $ttlSeconds;
        return [
            'iss' => config('app.url'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => optional($user->getRoleNames())->first(),
            'url_image' => $user->photo_url,
        ];
    }
}

if (!function_exists('backChangeFormatDate')) {
    function backChangeFormatDate($date)
    {
        if (!$date) return null;
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        return $carbonDate->format('d/m/Y H:i:s');
    }
}