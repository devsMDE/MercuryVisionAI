<?php
declare(strict_types=1);

function load_env_file(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if (!is_array($lines)) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim((string)$line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }

        $eqPos = strpos($line, '=');
        if ($eqPos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $eqPos));
        if ($key === '' || preg_match('/[^A-Za-z0-9_]/', $key)) {
            continue;
        }

        $rawValue = trim(substr($line, $eqPos + 1));
        $value = parse_env_value($rawValue);

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function parse_env_value(string $value): string
{
    if ($value === '') {
        return '';
    }

    $quote = $value[0];
    if (($quote === '"' || $quote === "'") && substr($value, -1) === $quote) {
        $inner = substr($value, 1, -1);
        if ($quote === '"') {
            $inner = str_replace(['\\n', '\\r', '\\t', '\\"', '\\\\'], ["\n", "\r", "\t", '"', '\\'], $inner);
        }
        return $inner;
    }

    $hashPos = strpos($value, ' #');
    if ($hashPos !== false) {
        $value = substr($value, 0, $hashPos);
    }

    return trim($value);
}

function env(string $key, $default = null)
{
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    return $default;
}

function json_ok($data = []): never
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);

    $payload = ['success' => true];
    if (is_array($data)) {
        $payload = array_merge($payload, $data);
    } else {
        $payload['data'] = $data;
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_err(string $message, int $status = 400): never
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($status);
    echo json_encode([
        'success' => false,
        'error' => $message,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        json_err('Invalid JSON body', 400);
    }

    return $data;
}

function allow_cors(): void
{
    // FBI-Level Security Headers
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

    $origin = (string)($_SERVER['HTTP_ORIGIN'] ?? '');
    if ($origin === '' && !empty($_SERVER['HTTP_HOST'])) {
        $origin = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    }
    if ($origin === '') $origin = '*';
    header('Vary: Origin');
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

load_env_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');
load_env_file(__DIR__ . DIRECTORY_SEPARATOR . '.env');
