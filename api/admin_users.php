<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/user_store.php';

$viewer = require_auth();
if (!is_admin_user($viewer)) {
    json_err('Forbidden', 403);
}

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    $users = list_users_latest();
    $out = [];
    foreach ($users as $u) {
        $plan = normalize_user_plan((string)($u['plan'] ?? 'free'));
        $out[] = [
            'uid' => (string)($u['uid'] ?? ''),
            'email' => (string)($u['email'] ?? ''),
            'username' => (string)($u['username'] ?? 'User'),
            'plan' => title_plan($plan),
            'plan_key' => $plan,
            'unlimited' => $plan === 'enterprise',
            'created_at' => (string)($u['created_at'] ?? ''),
            'updated_at' => (string)($u['updated_at'] ?? ''),
        ];
    }

    json_ok([
        'stats' => [
            'total_users' => count($out),
            'unlimited_users' => count(array_filter($out, static fn(array $u): bool => (bool)$u['unlimited'])),
        ],
        'users' => $out,
        'plans' => ['Free', 'Lite', 'Standard', 'Pro', 'Enterprise'],
    ]);
}

if ($method === 'POST') {
    $body = read_json_body();
    $uid = trim((string)($body['uid'] ?? ''));
    if ($uid === '') {
        json_err('uid is required', 400);
    }

    $unlimited = isset($body['unlimited']) ? (bool)$body['unlimited'] : null;
    $planRaw = trim((string)($body['plan'] ?? ''));
    $plan = $planRaw !== '' ? normalize_user_plan($planRaw) : 'free';
    if ($unlimited === true) {
        $plan = 'enterprise';
    }

    $updated = update_user_plan($uid, $plan);
    if (!is_array($updated)) {
        json_err('User not found', 404);
    }

    if ((string)($_SESSION['uid'] ?? '') === $uid) {
        $_SESSION['plan'] = title_plan($plan);
        if ($plan === 'enterprise') {
            $_SESSION['used_this_month'] = 0;
        }
    }

    json_ok([
        'user' => [
            'uid' => $uid,
            'plan' => title_plan($plan),
            'plan_key' => $plan,
            'unlimited' => $plan === 'enterprise',
        ],
    ]);
}

json_err('Method not allowed', 405);

function is_admin_user(array $viewer): bool
{
    $email = strtolower(trim((string)($viewer['email'] ?? '')));
    $configured = trim((string)env('ADMIN_EMAILS', ''));
    if ($configured === '') {
        return true;
    }

    $allowed = array_filter(array_map(static fn(string $v): string => strtolower(trim($v)), explode(',', $configured)));
    return in_array($email, $allowed, true);
}
