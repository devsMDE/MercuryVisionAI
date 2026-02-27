<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    json_err('Method not allowed', 405);
}

$user = require_auth();
$sessionPlan = normalize_plan((string)($user['plan'] ?? 'Free'));

$body = read_json_body();
$action = (string)($body['action'] ?? 'initial_report');
$customApiKey = (string)($body['customApiKey'] ?? '');
$aiModel = (string)($body['aiModel'] ?? 'openai');
$uiLang = (string)($body['language'] ?? 'en');

$mode = normalize_mode((string)($body['mode'] ?? ''));
$changePercent = $body['changePercent'] ?? ($body['metrics']['system_stats']['change_percent'] ?? null);
$hotspots = $body['hotspots'] ?? ($body['metrics']['system_stats']['hotspots'] ?? null);
$metrics = $body['metrics'] ?? null;
$imageRefs = $body['imageRefs'] ?? [];

if ($mode === '' || !is_numeric($changePercent) || !is_array($hotspots) || !is_array($metrics)) {
    json_err('Invalid payload', 400);
}

if (!is_array($imageRefs)) {
    $imageRefs = [];
}

$cost = 15;
$planConfig = plan_config($sessionPlan);
$record = get_user_by_uid((string)$user['uid']);
$used = (int)($record['credits_used'] ?? 0);
if ($planConfig['limit'] !== null && ($used + $cost) > $planConfig['limit']) {
    json_err('Monthly usage limit reached. Please upgrade your plan.', 429);
}

$key = trim((string)env('OPENAI_API_KEY', ''));
$isLocalFallback = false;
if ($customApiKey !== '') {
    $keys = array_filter(array_map('trim', explode(',', $customApiKey)));
    if (!empty($keys)) {
        $key = $keys[array_rand($keys)];
    }
}
if ($key === '') {
    $isLocalFallback = true;
}

$schema = [
    'type' => 'object',
    'additionalProperties' => false,
    'required' => ['summary', 'problems', 'solutionsByProblem'],
    'properties' => [
        'summary' => ['type' => 'string'],
        'problems' => [
            'type' => 'array',
            'items' => [
                'type' => 'object',
                'additionalProperties' => false,
                'required' => ['id', 'title', 'severity', 'description'],
                'properties' => [
                    'id' => ['type' => 'string'],
                    'title' => ['type' => 'string'],
                    'severity' => ['type' => 'string', 'enum' => ['low', 'medium', 'high']],
                    'description' => ['type' => 'string'],
                ],
            ],
        ],
        'solutionsByProblem' => [
            'type' => 'object',
            'additionalProperties' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['title', 'description', 'pros', 'cons'],
                    'properties' => [
                        'title' => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                        'pros' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ],
                        'cons' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ],
        'confidence' => ['type' => 'string'],
        'assumptions' => [
            'type' => 'array',
            'items' => ['type' => 'string'],
        ],
    ],
];

$promptPayload = [
    'mode' => $mode,
    'plan' => $sessionPlan,
    'changePercent' => (float)$changePercent,
    'hotspots' => $hotspots,
    'metrics' => $metrics,
    'imageRefs' => $imageRefs,
];

if ($action === 'chat_message') {
    $sysPrompt = [
        'role' => 'system',
        'content' => 'You are a highly capable geospatial AI assistant inside MercuryVision Studio. Provide strictly concise, helpful answers regarding the environmental analysis. CRITICAL INSTRUCTION: You MUST reply in this language: ' . strtoupper($uiLang)
    ];
    $chatMessages = is_array($body['messages'] ?? null) ? $body['messages'] : [];
    
    $request = [
        'model' => (string)env('OPENAI_MODEL', 'gpt-4o-mini'),
        'temperature' => 0.7,
        'messages' => array_merge([$sysPrompt], $chatMessages),
    ];
} else {
    $request = [
        'model' => (string)env('OPENAI_MODEL', 'gpt-4o-mini'),
        'temperature' => 0.2,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a geospatial analyst. Produce evidence-grounded JSON only. Do not use markdown. CRITICAL INSTRUCTION: Generate all text fields (summary, problem titles/descriptions, solution strings, etc.) in this language: ' . strtoupper($uiLang),
            ],
            [
                'role' => 'user',
                'content' => "Analyze these change-detection results and return decision support.\n\n" .
                    "Rules:\n" .
                    "- Link each solution to a problem id.\n" .
                    "- Keep to the given numeric evidence.\n" .
                    "- If confidence is low, say why.\n\n" .
                    "Input:\n" . json_encode($promptPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
        ],
        'response_format' => [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'mercuryvision_ai_solution',
                'strict' => true,
                'schema' => $schema,
            ],
        ],
    ];
}

$result = null;
if (!$isLocalFallback) {
    if ($aiModel === 'gemini') {
        $systemPrompt = "";
        $geminiContents = [];
        $lastRole = null;
        
        foreach ($request['messages'] as $msg) {
            if ($msg['role'] === 'system') {
                $systemPrompt .= $msg['content'] . "\n";
            } else {
                $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                if ($lastRole === $role && !empty($geminiContents)) {
                    $geminiContents[count($geminiContents)-1]['parts'][0]['text'] .= "\n\n" . $msg['content'];
                } else {
                    $geminiContents[] = [
                        'role' => $role,
                        'parts' => [['text' => $msg['content']]]
                    ];
                    $lastRole = $role;
                }
            }
        }
        if ($systemPrompt !== '' && !empty($geminiContents)) {
            $geminiContents[0]['parts'][0]['text'] = $systemPrompt . "\n\n" . $geminiContents[0]['parts'][0]['text'];
        }
        
        $geminiPayload = [
            'contents' => $geminiContents,
            'generationConfig' => [
                'temperature' => $request['temperature'] ?? 0.7,
            ]
        ];
        
        if ($action !== 'chat_message') {
            $geminiPayload['generationConfig']['responseMimeType'] = 'application/json';
        }

        if (!function_exists('curl_init')) {
            $isLocalFallback = true;
        } else {
            $ch = @curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $key);
            if ($ch === false) {
                $isLocalFallback = true;
            } else {
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_POSTFIELDS => json_encode($geminiPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);

                $responseRaw = curl_exec($ch);
                $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                @curl_close($ch);

                if ($responseRaw === false || $curlError !== '') {
                    $isLocalFallback = true;
                } else {
                    $response = json_decode((string)$responseRaw, true);
                    if (!is_array($response) || $httpCode < 200 || $httpCode >= 300) {
                        $isLocalFallback = true;
                    } else {
                        $content = (string)($response['candidates'][0]['content']['parts'][0]['text'] ?? '');
                        $content = preg_replace('/^```json\s*/i', '', $content);
                        $content = preg_replace('/```$/', '', trim($content));
                        
                        if ($action === 'chat_message') {
                            $result = ['reply' => $content];
                        } else {
                            $decoded = json_decode($content, true);
                            if (is_array($decoded)) {
                                $result = $decoded;
                            } else {
                                $isLocalFallback = true;
                            }
                        }
                    }
                }
            }
        }
    } else {
        if (!function_exists('curl_init')) {
            $isLocalFallback = true;
        } else {
            $ch = @curl_init('https://api.openai.com/v1/chat/completions');
            if ($ch === false) {
                $isLocalFallback = true;
            } else {
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $key,
                        'Content-Type: application/json',
                    ],
                    CURLOPT_POSTFIELDS => json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);

                $responseRaw = curl_exec($ch);
                $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                @curl_close($ch);

                if ($responseRaw === false || $curlError !== '') {
                    $isLocalFallback = true;
                } else {
                    $response = json_decode((string)$responseRaw, true);
                    if (!is_array($response) || $httpCode < 200 || $httpCode >= 300) {
                        $isLocalFallback = true;
                    } else {
                        $content = (string)($response['choices'][0]['message']['content'] ?? '');
                        if ($action === 'chat_message') {
                            $result = ['reply' => $content];
                        } else {
                            $decoded = json_decode($content, true);
                            if (is_array($decoded)) {
                                $result = $decoded;
                            } else {
                                $isLocalFallback = true;
                            }
                        }
                    }
                }
            }
        }
    }
}

if ($action === 'chat_message') {
    if ($isLocalFallback) {
        $result = ['reply' => "I am operating in fallback mode because the AI service is unavailable. I recommend analyzing the results manually based on the output metrics."];
    }
    
    $finalCost = $isLocalFallback ? 0 : 5; // chat costs less
    $newUsed = $finalCost > 0 ? increment_user_credits((string)$user['uid'], $finalCost) : $used;

    $out = [
        'reply' => $result['reply'] ?? '',
        'usage' => [
            'used' => $newUsed,
            'limit' => $planConfig['limit'] ?? 500,
            'cost' => $finalCost
        ]
    ];
    if ($isLocalFallback) $out['fallback'] = true;
    json_ok($out);
}

// Below is purely for initial_report
if ($isLocalFallback) {
    $result = build_local_ai_response($mode, (float)$changePercent, $hotspots);
}

$normalized = [
    'summary' => (string)($result['summary'] ?? ''),
    'problems' => normalize_problems($result['problems'] ?? []),
    'solutionsByProblem' => normalize_solutions_by_problem($result['solutionsByProblem'] ?? []),
];

if ($sessionPlan === 'enterprise') {
    $normalized['confidence'] = (string)($result['confidence'] ?? '');
    $normalized['assumptions'] = normalize_string_list($result['assumptions'] ?? []);
}

$gated = apply_plan_gating($normalized, $sessionPlan);

// Deduct credits only on success
$finalCost = $isLocalFallback ? 0 : $cost;
$newUsed = $finalCost > 0 ? increment_user_credits((string)$user['uid'], $finalCost) : $used;

$gated['usage'] = [
    'used' => $newUsed,
    'limit' => $planConfig['limit'] ?? 500,
    'cost' => $finalCost
];

if ($isLocalFallback) {
    $gated['fallback'] = true;
}

json_ok($gated);

function plan_config(string $plan): array
{
    return match ($plan) {
        'lite' => [
            'limit' => 15000,
            'modes' => ['water', 'forest', 'agriculture'],
        ],
        'standard' => [
            'limit' => 60000,
            'modes' => ['compare', 'water', 'forest', 'agriculture', 'urban_expansion'],
        ],
        'pro' => [
            'limit' => 500000,
            'modes' => ['compare', 'water', 'forest', 'agriculture', 'urban_expansion', 'infrastructure', 'disaster_impact'],
        ],
        'enterprise' => [
            'limit' => null,
            'modes' => ['compare', 'water', 'forest', 'agriculture', 'urban_expansion', 'infrastructure', 'disaster_impact'],
        ],
        default => [
            'limit' => 75,
            'modes' => ['compare', 'water', 'forest'],
        ],
    };
}

function normalize_plan(string $plan): string
{
    return match (strtolower(trim($plan))) {
        'lite' => 'lite',
        'standard' => 'standard',
        'pro' => 'pro',
        'enterprise' => 'enterprise',
        default => 'free',
    };
}

function normalize_mode(string $mode): string
{
    $mode = strtolower(trim($mode));
    return match ($mode) {
        'urban', 'urban expansion' => 'urban_expansion',
        default => $mode,
    };
}

function normalize_problems($value): array
{
    if (!is_array($value)) {
        return [];
    }

    $out = [];
    foreach ($value as $item) {
        if (!is_array($item)) {
            continue;
        }

        $id = trim((string)($item['id'] ?? ''));
        if ($id === '') {
            $id = 'problem_' . (string)(count($out) + 1);
        }

        $severity = strtolower((string)($item['severity'] ?? 'medium'));
        if (!in_array($severity, ['low', 'medium', 'high'], true)) {
            $severity = 'medium';
        }

        $out[] = [
            'id' => $id,
            'title' => (string)($item['title'] ?? ''),
            'severity' => $severity,
            'description' => (string)($item['description'] ?? ''),
        ];
    }

    return $out;
}

function normalize_solutions_by_problem($value): array
{
    if (!is_array($value)) {
        return [];
    }

    $out = [];
    foreach ($value as $problemId => $solutions) {
        if (!is_array($solutions)) {
            continue;
        }

        $cleanSolutions = [];
        foreach ($solutions as $solution) {
            if (!is_array($solution)) {
                continue;
            }
            $cleanSolutions[] = [
                'title' => (string)($solution['title'] ?? ''),
                'description' => (string)($solution['description'] ?? ''),
                'pros' => normalize_string_list($solution['pros'] ?? []),
                'cons' => normalize_string_list($solution['cons'] ?? []),
            ];
        }
        $out[(string)$problemId] = $cleanSolutions;
    }

    return $out;
}

function normalize_string_list($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $out = [];
    foreach ($value as $item) {
        $out[] = (string)$item;
    }
    return $out;
}

function apply_plan_gating(array $data, string $plan): array
{
    if ($plan === 'pro' || $plan === 'enterprise') {
        return $data;
    }


    if ($plan === 'free') {
        return [
            'summary' => (string)($data['summary'] ?? ''),
            'problems' => $data['problems'] ?? [],
            'solutionsByProblem' => [],
        ];
    }

    $problems = $data['problems'] ?? [];
    $source = is_array($data['solutionsByProblem'] ?? null) ? $data['solutionsByProblem'] : [];
    $trimmed = [];
    $budget = 2;

    foreach ($problems as $problem) {
        if ($budget <= 0) {
            break;
        }
        if (!is_array($problem)) {
            continue;
        }
        $id = (string)($problem['id'] ?? '');
        if ($id === '' || !isset($source[$id]) || !is_array($source[$id])) {
            continue;
        }

        $chunk = array_slice($source[$id], 0, $budget);
        if ($chunk !== []) {
            $trimmed[$id] = $chunk;
            $budget -= count($chunk);
        }
    }

    return [
        'summary' => (string)($data['summary'] ?? ''),
        'problems' => $problems,
        'solutionsByProblem' => $trimmed,
    ];
}

function build_local_ai_response(string $mode, float $changePercent, array $hotspots): array
{
    $severity = 'low';
    if ($changePercent >= 15.0) {
        $severity = 'high';
    } elseif ($changePercent >= 7.0) {
        $severity = 'medium';
    }

    $hotspotCount = count($hotspots);
    $problemId = 'p1';
    return [
        'summary' => "Local AI fallback: detected {$changePercent}% change in {$mode} mode with {$hotspotCount} hotspot areas.",
        'problems' => [
            [
                'id' => $problemId,
                'title' => 'Detected surface change',
                'severity' => $severity,
                'description' => 'Change intensity indicates potential land-use or environmental shift.',
            ],
        ],
        'solutionsByProblem' => [
            $problemId => [
                [
                    'title' => 'Run follow-up verification',
                    'description' => 'Validate changes against additional temporal imagery and field checks.',
                    'pros' => ['Improves confidence', 'Reduces false positives'],
                    'cons' => ['Needs more time', 'Requires extra data'],
                ],
                [
                    'title' => 'Set monitoring threshold alert',
                    'description' => 'Trigger review workflow when change exceeds predefined limits.',
                    'pros' => ['Fast operational response', 'Scales across regions'],
                    'cons' => ['Threshold tuning required', 'May generate noise initially'],
                ],
            ],
        ],
        'confidence' => 'medium',
        'assumptions' => [
            'Fallback used due to unavailable AI service',
            'Only uploaded pair and derived metrics were considered',
        ],
    ];
}
