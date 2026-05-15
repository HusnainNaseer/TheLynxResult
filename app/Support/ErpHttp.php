<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ErpHttp
{
    public static function request(int $timeout = 10, ?string $token = null): PendingRequest
    {
        $verifySsl = filter_var(
            config('services.erp.verify_ssl', true),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        $request = Http::timeout($timeout)
            ->connectTimeout($timeout)
            ->withOptions([
                'verify' => $verifySsl ?? true,
            ]);

        $token ??= Auth::user()?->erp_access_token;

        return $token ? $request->withToken($token) : $request;
    }

    public static function get(string $path, int $timeout = 10): Response
    {
        return self::authenticatedRequest($timeout)->get(self::url($path));
    }

    public static function url(string $path): string
    {
        return rtrim((string) config('services.erp.api_url'), '/') . '/' . ltrim($path, '/');
    }

    private static function authenticatedRequest(int $timeout = 10): PendingRequest
    {
        $user = Auth::user();
        $token = $user?->hasAnyRole(['Admin', 'Coordinator']) ? $user->erp_access_token : null;

        if (!$token) {
            throw new RuntimeException('ERP API token is missing. Please login again.');
        }

        if ($user->erp_token_expires_at && $user->erp_token_expires_at->isPast()) {
            throw new RuntimeException('ERP API token has expired. Please login again.');
        }

        return self::request($timeout, $token);
    }
}
