<?php
/* api/verify.php — Firebase ID token verification */
declare(strict_types=1);
require_once __DIR__ . '/config.php';
allow_cors();

function extractBearerToken(): ?string {
    $authHeader = (string)($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m)) {
        return trim((string)$m[1]);
    }
    return null;
}

/**
 * PRODUCTION: Use kreait/firebase-php via Composer:
 *   composer require kreait/firebase-php
 *
 * This is a STUB that extracts the UID from the JWT payload WITHOUT
 * cryptographic verification. It is INSECURE and exists only to make
 * the app runnable locally without Composer.
 *
 * ⚠️  DO NOT USE IN PRODUCTION WITHOUT REPLACING WITH REAL VERIFICATION.
 */

function verifyFirebaseToken(string $idToken): array {
    $saPath = getenv('FIREBASE_SERVICE_ACCOUNT_PATH');

    /* ── Attempt real verification if Composer autoload + kreait exists ── */
    $autoload = __DIR__ . '/vendor/autoload.php';
    if ($saPath && file_exists($autoload) && file_exists($saPath)) {
        require_once $autoload;
        try {
            $factory = (new \Kreait\Firebase\Factory())->withServiceAccount($saPath);
            $auth = $factory->createAuth();
            $verifiedToken = $auth->verifyIdToken($idToken);
            $claims = $verifiedToken->claims();
            return [
                'uid'   => $claims->get('sub'),
                'email' => $claims->get('email', ''),
            ];
        } catch (\Throwable $e) {
            json_err('Token verification failed: ' . $e->getMessage(), 401);
        }
    }

    /* ── STUB: decode JWT payload without verification ── */
    /* WARNING: This does NOT verify the signature. Local development only. */
    $parts = explode('.', $idToken);
    if (count($parts) !== 3) {
        json_err('Invalid token format', 401);
    }

    $segment = strtr($parts[1], '-_', '+/');
    $padding = strlen($segment) % 4;
    if ($padding > 0) {
        $segment .= str_repeat('=', 4 - $padding);
    }
    $decoded = base64_decode($segment, true);
    $payload = is_string($decoded) ? json_decode($decoded, true) : null;
    if (!$payload || empty($payload['sub'])) {
        json_err('Invalid token payload', 401);
    }

    /* Check expiry at minimum */
    if (isset($payload['exp']) && (int)$payload['exp'] < (time() - 120)) {
        json_err('Token expired', 401);
    }

    error_log('[SECURITY WARNING] Firebase token NOT cryptographically verified. Install kreait/firebase-php for production.');

    return [
        'uid'   => $payload['sub'],
        'email' => $payload['email'] ?? '',
    ];
}

/* ── Direct endpoint: POST /api/verify.php ── */
if (
    basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__) &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $token = extractBearerToken();
    if (!$token) json_err('Missing Authorization header', 401);
    $user = verifyFirebaseToken($token);
    json_ok(['ok' => true, 'uid' => $user['uid'], 'email' => $user['email']]);
}
