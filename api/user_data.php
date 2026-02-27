<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/user_store.php';

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
    json_err('Method not allowed', 405);
}

$user = require_auth();
$record = get_user_by_uid((string)$user['uid']);
$plan = normalize_plan((string)($record['plan'] ?? $user['plan'] ?? 'Free'));
$planConfig = plan_config($plan);
$used = (int)($record['credits_used'] ?? 0);

$_SESSION['plan'] = ucfirst($plan);

json_ok([
    'plan' => ucfirst($plan),
    'usage' => [
        'used' => $used,
        'limit' => $planConfig['limit'] ?? 500,
    ],
    'upload' => [
        'limit_mb' => $planConfig['upload_mb'],
    ],
]);

function normalize_plan(string $plan): string
{
    $normalized = strtolower(trim($plan));
    return match ($normalized) {
        'lite' => 'lite',
        'standard' => 'standard',
        'pro' => 'pro',
        'enterprise' => 'enterprise',
        default => 'free',
    };
}

function plan_config(string $plan): array
{
    return match ($plan) {
        'lite' => [
            'limit' => 15000,
            'upload_mb' => 5,
        ],
        'standard' => [
            'limit' => 60000,
            'upload_mb' => 10,
        ],
        'pro' => [
            'limit' => 500000,
            'upload_mb' => 10,
        ],
        'enterprise' => [
            'limit' => null,
            'upload_mb' => 20,
        ],
        default => [
            'limit' => 500,
            'upload_mb' => 2,
        ],
    };
}
