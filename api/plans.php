<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
    json_err('Method not allowed', 405);
}

$user = require_auth();

$plans = [
    'Free' => [
        'limit' => 75,
        'aiDepth' => 'summary',
        'modes' => ['compare', 'water', 'forest'],
        'exports' => ['csv'],
    ],
    'Lite' => [
        'limit' => 15000,
        'aiDepth' => 'guided',
        'modes' => ['compare', 'water', 'forest', 'agriculture'],
        'exports' => ['csv'],
    ],
    'Standard' => [
        'limit' => 60000,
        'aiDepth' => 'full',
        'modes' => ['compare', 'water', 'forest', 'agriculture', 'urban_expansion'],
        'exports' => ['csv', 'pdf'],
    ],
    'Pro' => [
        'limit' => 500000,
        'aiDepth' => 'expert',
        'modes' => ['compare', 'water', 'forest', 'agriculture', 'urban_expansion', 'infrastructure', 'disaster_impact'],
        'exports' => ['csv', 'pdf'],
    ],
];

$sessionPlan = (string)($user['plan'] ?? 'Free');
$plan = array_key_exists($sessionPlan, $plans) ? $sessionPlan : 'Free';
$config = $plans[$plan];
$usedThisMonth = (int)($_SESSION['used_this_month'] ?? 0);

json_ok([
    'plan' => $plan,
    'limits' => [
        'monthly' => $config['limit'],
    ],
    'allowedModes' => $config['modes'],
    'exportAbilities' => [
        'csv' => in_array('csv', $config['exports'], true),
        'pdf' => in_array('pdf', $config['exports'], true),
    ],
    'aiDepth' => $config['aiDepth'],
    'usage' => [
        'used_this_month' => $usedThisMonth,
    ],
    'plans' => $plans,
]);
