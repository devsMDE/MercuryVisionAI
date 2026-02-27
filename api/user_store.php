<?php
declare(strict_types=1);

function user_store_path(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app.json';
}

function normalize_user_plan(string $plan): string
{
    return match (strtolower(trim($plan))) {
        'lite' => 'lite',
        'standard' => 'standard',
        'pro' => 'pro',
        'enterprise', 'unlimited' => 'enterprise',
        default => 'free',
    };
}

function title_plan(string $plan): string
{
    return match (normalize_user_plan($plan)) {
        'lite' => 'Lite',
        'standard' => 'Standard',
        'pro' => 'Pro',
        'enterprise' => 'Enterprise',
        default => 'Free',
    };
}

function load_user_store(): array
{
    $path = user_store_path();
    if (!is_file($path)) {
        return ['users' => [], 'projects' => [], 'analyses' => [], 'messages' => []];
    }

    $raw = file_get_contents($path);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if (!is_array($decoded)) {
        return ['users' => [], 'projects' => [], 'analyses' => [], 'messages' => []];
    }

    if (!isset($decoded['users']) || !is_array($decoded['users'])) {
        $decoded['users'] = [];
    }

    $decoded['users'] = compact_users($decoded['users']);
    return $decoded;
}

function save_user_store(array $store): void
{
    $path = user_store_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($path, json_encode($store, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

function get_user_by_uid(string $uid): ?array
{
    $store = load_user_store();
    $best = null;
    foreach ($store['users'] as $u) {
        if (!is_array($u) || (string)($u['uid'] ?? '') !== $uid) {
            continue;
        }
        $candidate = $u;
        $candidate['plan'] = normalize_user_plan((string)($candidate['plan'] ?? 'free'));
        $best = $candidate;
    }
    return $best;
}

function upsert_user_record(array $user): array
{
    $uid = trim((string)($user['uid'] ?? ''));
    if ($uid === '') {
        return [];
    }

    $store = load_user_store();
    $now = date('Y-m-d H:i:s');
    $found = false;

    for ($i = count($store['users']) - 1; $i >= 0; $i--) {
        if (!is_array($store['users'][$i])) {
            continue;
        }
        if ((string)($store['users'][$i]['uid'] ?? '') !== $uid) {
            continue;
        }

        $record = $store['users'][$i];
        $record['uid'] = $uid;
        $record['email'] = (string)($user['email'] ?? ($record['email'] ?? ''));
        $record['username'] = (string)($user['username'] ?? ($record['username'] ?? 'User'));
        $record['plan'] = normalize_user_plan((string)($user['plan'] ?? ($record['plan'] ?? 'free')));
        if (!isset($record['credits_used'])) {
            $record['credits_used'] = 0;
        }
        if (!isset($record['credits_total'])) {
            $record['credits_total'] = 5000;
        }
        $record['updated_at'] = $now;
        $store['users'][$i] = $record;
        $found = true;
        break;
    }

    if (!$found) {
        $store['users'][] = [
            'uid' => $uid,
            'email' => (string)($user['email'] ?? ''),
            'username' => (string)($user['username'] ?? 'User'),
            'plan' => normalize_user_plan((string)($user['plan'] ?? 'free')),
            'credits_used' => 0,
            'credits_total' => 5000,
            'requests_today' => 0,
            'requests_month' => 0,
            'last_reset_day' => date('Y-m-d'),
            'last_reset_month' => date('Y-m'),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    save_user_store($store);
    $saved = get_user_by_uid($uid);
    return is_array($saved) ? $saved : [];
}

function list_users_latest(): array
{
    $store = load_user_store();
    $users = compact_users($store['users']);
    $store['users'] = $users;
    save_user_store($store);

    usort($users, static function (array $a, array $b): int {
        return strcmp((string)($b['updated_at'] ?? $b['created_at'] ?? ''), (string)($a['updated_at'] ?? $a['created_at'] ?? ''));
    });
    return $users;
}

function increment_user_credits(string $uid, int $amount): int
{
    $store = load_user_store();
    $now = date('Y-m-d H:i:s');
    $newTotal = 0;

    for ($i = count($store['users']) - 1; $i >= 0; $i--) {
        if (!is_array($store['users'][$i])) {
            continue;
        }
        if ((string)($store['users'][$i]['uid'] ?? '') !== $uid) {
            continue;
        }

        $current = (int)($store['users'][$i]['credits_used'] ?? 0);
        $newTotal = $current + $amount;
        $store['users'][$i]['credits_used'] = $newTotal;
        $store['users'][$i]['updated_at'] = $now;
        
        save_user_store($store);
        return $newTotal;
    }

    return $newTotal;
}

function update_user_plan(string $uid, string $plan): ?array
{
    $store = load_user_store();
    $planNorm = normalize_user_plan($plan);
    $now = date('Y-m-d H:i:s');

    for ($i = count($store['users']) - 1; $i >= 0; $i--) {
        if (!is_array($store['users'][$i])) {
            continue;
        }
        if ((string)($store['users'][$i]['uid'] ?? '') !== $uid) {
            continue;
        }

        $store['users'][$i]['plan'] = $planNorm;
        $store['users'][$i]['updated_at'] = $now;
        if ($planNorm === 'enterprise') {
            $store['users'][$i]['credits_total'] = 0;
        }
        save_user_store($store);
        return $store['users'][$i];
    }

    return null;
}

function compact_users(array $users): array
{
    $byUid = [];
    foreach ($users as $u) {
        if (!is_array($u)) {
            continue;
        }
        $uid = trim((string)($u['uid'] ?? ''));
        if ($uid === '') {
            continue;
        }
        $u['plan'] = normalize_user_plan((string)($u['plan'] ?? 'free'));
        $byUid[$uid] = choose_newer_user($byUid[$uid] ?? null, $u);
    }

    $byEmail = [];
    foreach ($byUid as $u) {
        $email = strtolower(trim((string)($u['email'] ?? '')));
        $key = $email !== '' ? 'email:' . $email : 'uid:' . (string)$u['uid'];
        $byEmail[$key] = choose_newer_user($byEmail[$key] ?? null, $u);
    }

    return array_values($byEmail);
}

function choose_newer_user(?array $current, array $candidate): array
{
    if ($current === null) {
        return $candidate;
    }

    $currentTs = strtotime((string)($current['updated_at'] ?? $current['created_at'] ?? '1970-01-01 00:00:00')) ?: 0;
    $candidateTs = strtotime((string)($candidate['updated_at'] ?? $candidate['created_at'] ?? '1970-01-01 00:00:00')) ?: 0;
    return $candidateTs >= $currentTs ? $candidate : $current;
}
