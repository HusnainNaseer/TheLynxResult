<?php

namespace App\Services;

use App\Models\User;
use App\Support\ErpHttp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ErpAuthService
{
    public function login(User $user): array
    {
        $email = (string) config('services.erp.admin_email', 'company@example.com');
        $password = (string) config('services.erp.admin_password', '123456');

        $response = ErpHttp::request(15, '')->post(ErpHttp::url('login'), [
            'email' => $email,
            'password' => $password,
            // 'device_name' => config('app.name', 'Result Management System'),
        ]);
     

        if (!$response->successful()) {
            Log::warning('ERP admin token login failed for local user ' . $user->id . '. Status: ' . $response->status(), [
                'url' => ErpHttp::url('login'),
                'response' => str($response->body())->limit(500)->toString(),
            ]);

            return [
                'ok' => false,
                'message' => 'ERP admin token login failed. Please check the configured ERP company credentials.',
            ];
        }

        $payload = $response->json();
        $token = $this->extractToken($payload);

        if (!$token) {
            Log::warning('ERP admin token login response did not include a token for local user ' . $user->id, [
                'keys' => is_array($payload) ? array_keys($payload) : [],
            ]);

            return [
                'ok' => false,
                'message' => 'ERP admin login succeeded, but no API token was returned.',
            ];
        }

        if (!Schema::hasColumn('users', 'erp_access_token')) {
            return [
                'ok' => false,
                'message' => 'ERP token column is missing. Please run migrations.',
            ];
        }

        $user->forceFill([
            'erp_access_token' => $token,
            'erp_token_expires_at' => $this->extractExpiry($payload),
        ])->save();

        return ['ok' => true, 'message' => 'ERP token saved.'];
    }

    private function extractToken(array $payload): ?string
    {
        foreach ([
            'token',
            'access_token',
            'plainTextToken',
            'data.token',
            'data.access_token',
            'data.plainTextToken',
            'user.token',
            'user.access_token',
        ] as $key) {
            $token = data_get($payload, $key);

            if (is_string($token) && $token !== '') {
                return $token;
            }
        }

        return null;
    }

    private function extractExpiry(array $payload): ?Carbon
    {
        $expiresAt = data_get($payload, 'expires_at') ?? data_get($payload, 'data.expires_at');

        if ($expiresAt) {
            return Carbon::parse($expiresAt);
        }

        $expiresIn = data_get($payload, 'expires_in') ?? data_get($payload, 'data.expires_in');

        return is_numeric($expiresIn) ? now()->addSeconds((int) $expiresIn) : null;
    }
}
