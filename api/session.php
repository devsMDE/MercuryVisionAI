<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/user_store.php';

allow_cors();

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function require_auth(): array
{
    if (empty($_SESSION['uid'])) {
        json_err('Unauthorized', 401);
    }

    return [
        'uid' => (string)$_SESSION['uid'],
        'plan' => (string)($_SESSION['plan'] ?? 'Free'),
        'email' => (string)($_SESSION['email'] ?? ''),
        'username' => (string)($_SESSION['username'] ?? ''),
        'photo_url' => (string)($_SESSION['photo_url'] ?? ''),
    ];
}

function do_logout(): never
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'] ?? '/',
            $params['domain'] ?? '',
            (bool)($params['secure'] ?? false),
            (bool)($params['httponly'] ?? true)
        );
    }

    session_destroy();
    json_ok([]);
}

function decode_firebase_token_payload(string $token): array
{
    // Fallback for mock dev tokens used in testing
    if ($token === 'mock_dev_token' || strpos($token, 'dev_') === 0) {
        return [
            'sub' => uniqid('dev_'),
            'email' => 'dev@example.com',
            'name' => 'Dev User'
        ];
    }

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        json_err('Invalid token format', 401);
    }

    $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
    if ($payloadJson === false) {
        json_err('Invalid token payload encoding', 401);
    }

    $payload = json_decode($payloadJson, true);
    if (!is_array($payload) || empty($payload['sub'])) {
        json_err('Invalid token payload', 401);
    }

    $exp = (int)($payload['exp'] ?? 0);
    if ($exp > 0 && $exp < (time() - 300)) {
        json_err('Token expired', 401);
    }

    return $payload;
}

function do_login(array $body): never
{
    $authHeader = (string)($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    $token = '';
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m)) {
        $token = trim((string)$m[1]);
    }
    if ($token === '') {
        $token = trim((string)($body['idToken'] ?? ''));
    }
    if ($token === '') {
        json_err('Missing token', 401);
    }

    $payload = decode_firebase_token_payload($token);
    $uid = (string)$payload['sub'];
    $email = (string)($payload['email'] ?? '');
    $username = trim((string)($body['username'] ?? ''));
    if ($username === '') {
        $username = (string)($payload['name'] ?? '');
    }
    if ($username === '') {
        $username = $email !== '' ? (string)strtok($email, '@') : 'User';
    }
    $photoUrl = trim((string)($body['photoUrl'] ?? ''));
    if ($photoUrl === '') {
        $photoUrl = trim((string)($payload['picture'] ?? ''));
    }

    $existing = get_user_by_uid($uid);
    $plan = is_array($existing) ? title_plan((string)($existing['plan'] ?? 'free')) : 'Free';

    $_SESSION['uid'] = $uid;
    $_SESSION['email'] = $email;
    $_SESSION['username'] = $username;
    $_SESSION['photo_url'] = $photoUrl;
    $_SESSION['plan'] = $plan;
    if (!isset($_SESSION['used_this_month'])) {
        $_SESSION['used_this_month'] = 0;
    }

    upsert_user_record([
        'uid' => $uid,
        'email' => $email,
        'username' => $username,
        'plan' => strtolower($plan),
    ]);

    session_write_close();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'user' => [
            'uid' => $uid,
            'email' => $email,
            'username' => $username,
            'plan' => (string)$_SESSION['plan'],
            'photo_url' => $photoUrl,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === 'session.php') {
    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

    if ($method === 'POST') {
        $body = [];
        $action = (string)($_GET['action'] ?? '');
        if ($action === '') {
            $action = (string)($_POST['action'] ?? '');
        }
        if (stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== false) {
            $body = read_json_body();
            if ($action === '') {
                $action = (string)($body['action'] ?? '');
            }
        }

        if ($action === 'login') {
            do_login($body);
        }

        if ($action === 'dev_login') {
            $email = (string)($body['email'] ?? 'dev@example.com');
            $username = trim((string)($body['username'] ?? ''));
            $requestedPlan = strtolower(trim((string)($body['plan'] ?? 'free')));
            $plan = match ($requestedPlan) {
                'lite' => 'Lite',
                'pro' => 'Pro',
                'enterprise' => 'Enterprise',
                default => 'Free',
            };
            if ($username === '') {
                $username = (string)strtok($email, '@');
            }
            $_SESSION['uid'] = 'dev_' . uniqid();
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username;
            $_SESSION['photo_url'] = '';
            $_SESSION['plan'] = $plan;
            upsert_user_record([
                'uid' => (string)$_SESSION['uid'],
                'email' => $email,
                'username' => $username,
                'plan' => strtolower($plan),
            ]);
            json_ok([
                'user' => [
                    'uid' => $_SESSION['uid'],
                    'email' => $email,
                    'username' => $username,
                    'plan' => $plan
                ]
            ]);
        }

        if ($action === 'logout') {
            do_logout();
        }

        json_err('Unknown action', 400);
    }

    if ($method === 'GET') {
        if (empty($_SESSION['uid'])) {
            json_ok([
                'authenticated' => false,
            ]);
        }

        json_ok([
            'authenticated' => true,
            'user' => [
                'uid' => (string)$_SESSION['uid'],
                'email' => (string)($_SESSION['email'] ?? ''),
                'username' => (string)($_SESSION['username'] ?? ''),
                'plan' => (string)($_SESSION['plan'] ?? 'Free'),
                'photo_url' => (string)($_SESSION['photo_url'] ?? ''),
            ],
        ]);
    }

    json_err('Method not allowed', 405);
}
