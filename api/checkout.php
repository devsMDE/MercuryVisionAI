<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/user_store.php';

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    json_err('Method not allowed', 405);
}

$user = require_auth();
$uid = (string)$user['uid'];

$rawBody = read_json_body();
$plan = trim((string)($rawBody['plan'] ?? ''));

if ($plan === '') {
    json_err('Plan is required', 400);
}

// Update the persistent underlying user_store JSON (acting as the DB for this setup)
$updatedUser = update_user_plan($uid, $plan);

if ($updatedUser) {
    // Note: The Firebase token claim will still have the old plan unless updated independently,
    // but the session system overrides user plan through user_store.php priority, so it correctly sticks here
    $_SESSION['plan'] = ucfirst($updatedUser['plan']);
    json_ok([
        'message' => 'Plan updated successfully',
        'plan' => ucfirst($updatedUser['plan']),
        'user' => $updatedUser
    ]);
} else {
    json_err('User not found or plan update failed', 500);
}
