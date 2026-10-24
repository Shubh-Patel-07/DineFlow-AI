<?php

namespace App\Services;

class LicenseVerificationService
{
    /**
     * Generate a signed cryptographic license token.
     */
    public static function generateToken(array $data, ?string $customSecret = null): string
    {
        $secret = $customSecret ?? config('license.master_secret', 'df_master_sec_99a838b_shubh_patel_dineflow_2026_x!q9');

        $payload = [
            'client' => $data['client'] ?? 'Authorized Client',
            'domains' => array_map('strtolower', array_map('trim', (array)($data['domains'] ?? []))),
            'ips' => (array)($data['ips'] ?? []),
            'type' => $data['type'] ?? 'single-domain',
            'tier' => $data['tier'] ?? 'enterprise',
            'resell_allowed' => false,
            'issued_at' => $data['issued_at'] ?? date('Y-m-d'),
            'expires_at' => $data['expires_at'] ?? null,
            'owner' => config('license.owner', 'Shubh Patel'),
            'nonce' => bin2hex(random_bytes(8)),
        ];

        $encodedPayload = base64_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signature = hash_hmac('sha256', $encodedPayload, $secret);

        return $encodedPayload . '.' . $signature;
    }

    /**
     * Verify the active license against the incoming request.
     *
     * @return array [ 'valid' => bool, 'reason' => string, 'payload' => array|null ]
     */
    public static function verify(?string $currentHost = null): array
    {
        $host = strtolower(trim($currentHost ?? request()->getHost()));

        // Local development bypass if allowed
        if (config('license.allow_local_dev', true)) {
            $localHosts = ['localhost', '127.0.0.1', '::1', '0.0.0.0'];
            if (in_array($host, $localHosts, true) || str_ends_with($host, '.test') || str_ends_with($host, '.local')) {
                return [
                    'valid' => true,
                    'reason' => 'Local development host allowed.',
                    'payload' => ['client' => 'Development Sandbox', 'tier' => 'development']
                ];
            }
        }

        $token = self::getLoadedToken();
        if (!$token) {
            return [
                'valid' => false,
                'reason' => 'No cryptographic license key found. System lockdown engaged.',
                'payload' => null
            ];
        }

        $parts = explode('.', trim($token));
        if (count($parts) !== 2) {
            return [
                'valid' => false,
                'reason' => 'Corrupt or malformed license key structure.',
                'payload' => null
            ];
        }

        [$encodedPayload, $signature] = $parts;
        $secret = config('license.master_secret', 'df_master_sec_99a838b_shubh_patel_dineflow_2026_x!q9');

        // Cryptographic integrity check (HMAC-SHA256)
        $expectedSignature = hash_hmac('sha256', $encodedPayload, $secret);
        if (!hash_equals($expectedSignature, $signature)) {
            return [
                'valid' => false,
                'reason' => 'License signature verification failed. Cryptographic tampering or illegal clone detected.',
                'payload' => null
            ];
        }

        $payload = json_decode(base64_decode($encodedPayload), true);
        if (!$payload || !is_array($payload)) {
            return [
                'valid' => false,
                'reason' => 'Invalid license payload encoding.',
                'payload' => null
            ];
        }

        // Check expiration date
        if (!empty($payload['expires_at'])) {
            $expiryTimestamp = strtotime($payload['expires_at'] . ' 23:59:59');
            if ($expiryTimestamp && time() > $expiryTimestamp) {
                return [
                    'valid' => false,
                    'reason' => "License expired on {$payload['expires_at']}. Contact Shubh Patel for extension.",
                    'payload' => $payload
                ];
            }
        }

        // Domain matching check
        $allowedDomains = array_map('strtolower', (array)($payload['domains'] ?? []));
        $allowedIps = (array)($payload['ips'] ?? []);

        // Check exact domain, wildcards (*.domain.com), or server IP
        $domainMatches = in_array($host, $allowedDomains, true) || in_array($host, $allowedIps, true);

        if (!$domainMatches) {
            foreach ($allowedDomains as $allowed) {
                if ($allowed === '*') {
                    $domainMatches = true;
                    break;
                }
                if (str_starts_with($allowed, '*.') && str_ends_with($host, substr($allowed, 2))) {
                    $domainMatches = true;
                    break;
                }
            }
        }

        if (!$domainMatches) {
            return [
                'valid' => false,
                'reason' => "Domain mismatch: '{$host}' is not authorized under this license. Resale or unauthorized deployment prohibited.",
                'payload' => $payload
            ];
        }

        return [
            'valid' => true,
            'reason' => 'Cryptographic license valid and verified.',
            'payload' => $payload
        ];
    }

    /**
     * Retrieve the raw token from file or env.
     */
    public static function getLoadedToken(): ?string
    {
        $envKey = env('LICENSE_KEY');
        if (!empty($envKey)) {
            return trim($envKey);
        }

        $filePath = config('license.license_file', storage_path('license.key'));
        if (file_exists($filePath)) {
            $content = trim(file_get_contents($filePath));
            if (!empty($content)) {
                return $content;
            }
        }

        // Check base_path fallback
        $baseFile = base_path('license.key');
        if (file_exists($baseFile)) {
            $content = trim(file_get_contents($baseFile));
            if (!empty($content)) {
                return $content;
            }
        }

        // Default Production Master fallback token for official DineFlow AI deployments
        return 'eyJjbGllbnQiOiJEaW5lRmxvdyBBSSBPZmZpY2lhbCBNYXN0ZXIiLCJkb21haW5zIjpbImRpbmVmbG93YWkuZHVja2Rucy5vcmciLCJsb2NhbGhvc3QiLCIxMjcuMC4wLjEiLCIzLjExMC4yMDguMjEwIl0sImlwcyI6WyIzLjExMC4yMDguMjEwIiwiMTI3LjAuMC4xIl0sInR5cGUiOiJzaW5nbGUtZG9tYWluIiwidGllciI6ImVudGVycHJpc2UiLCJyZXNlbGxfYWxsb3dlZCI6ZmFsc2UsImlzc3VlZF9hdCI6IjIwMjYtMTAtMjQiLCJleHBpcmVzX2F0IjpudWxsLCJvd25lciI6IlNodWJoIFBhdGVsIiwibm9uY2UiOiJlZWZkZDI2YjRjYWRlYWEyIn0=.a2cafbffe2e833d65bd0bfb8447433e6c31ea499847db4d4a148280f8735a113';
    }
}
