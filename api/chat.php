<?php
/* api/chat.php — Vision AI chat endpoint */
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/verify.php';

function getDb() {
    $dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app.db';
    $storageDir = dirname($dbFile);
    if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
        json_err('Storage directory unavailable', 500);
    }

    try {
        if (!class_exists('PDO') || !in_array('sqlite', PDO::getAvailableDrivers())) {
            require_once __DIR__ . '/json_db.php';
            $db = new JsonPdo($dbFile);
        } else {
            $db = new PDO('sqlite:' . $dbFile, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
    } catch (Throwable $e) {
        json_err('Database connection failed: ' . $e->getMessage(), 500);
    }

    $db->exec('CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        uid TEXT NOT NULL,
        role TEXT NOT NULL,
        content TEXT NOT NULL,
        analysis_id INTEGER,
        created_at TEXT DEFAULT (datetime("now"))
    )');

    return $db;
}

allow_cors();

$db = getDb();

/* ── Auth ── */
$token = extractBearerToken();
if (!$token) json_err('Missing Authorization header', 401);
$user = verifyFirebaseToken($token);
$uid = $user['uid'];

$method = $_SERVER['REQUEST_METHOD'];

/* ── GET: retrieve chat history ── */
if ($method === 'GET') {
    $analysisId = isset($_GET['analysis_id']) ? (int)$_GET['analysis_id'] : null;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 50;

    if ($analysisId) {
        $stmt = $db->prepare('SELECT * FROM messages WHERE uid = ? AND analysis_id = ? ORDER BY created_at ASC LIMIT ?');
        $stmt->execute([$uid, $analysisId, $limit]);
    } else {
        $stmt = $db->prepare('SELECT * FROM messages WHERE uid = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$uid, $limit]);
    }

    $rows = $stmt->fetchAll();
    json_ok(['ok' => true, 'messages' => $rows]);
}

/* ── POST: send message & get AI response ── */
if ($method === 'POST') {
    $body = read_json_body();
    $content = trim($body['message'] ?? '');
    if (!$content) json_err('Message content is required');

    $analysisId = isset($body['analysis_id']) ? (int)$body['analysis_id'] : null;
    $mode = trim($body['mode'] ?? 'general');
    $context = trim($body['context'] ?? '');

    // Store user message
    $stmt = $db->prepare('INSERT INTO messages (uid, role, content, analysis_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$uid, 'user', $content, $analysisId]);

    // Generate AI response (simulated — replace with real AI API)
    $aiResponse = generateAIResponse($content, $mode, $context);

    // Store AI response
    $stmt = $db->prepare('INSERT INTO messages (uid, role, content, analysis_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$uid, 'ai', $aiResponse, $analysisId]);

    json_ok([
        'ok' => true,
        'reply' => $aiResponse,
        'mode' => $mode
    ]);
}

json_err('Method not allowed', 405);

/* ── AI Response Generator (placeholder) ── */
function generateAIResponse(string $message, string $mode, string $context): string {
    $lc = strtolower($message);
    $modeLabel = ucfirst($mode);

    // Context-aware responses
    $responses = [
        'water' => [
            "Water dynamics analysis indicates significant hydrological changes in this region. The turbidity levels and shoreline recession patterns suggest increased sediment transport, possibly due to upstream land use changes.",
            "Based on the spectral analysis, water coverage has decreased by approximately 12%. I recommend monitoring the NDWI (Normalized Difference Water Index) trends over the next 3 months.",
            "The dissolved organic matter detected in the spectral signature aligns with seasonal agricultural runoff. Consider implementing buffer zone vegetation to mitigate this."
        ],
        'forest' => [
            "Forest canopy analysis shows a 5% reduction in tree cover. The NDVI values suggest the remaining vegetation is relatively healthy at 0.75, but the deforestation rate needs attention.",
            "Biomass estimation from the spectral data indicates approximately 120 tonnes per hectare. This is consistent with secondary growth forest, suggesting previous disturbance events.",
            "The fire risk assessment is currently low due to adequate soil moisture levels. However, I recommend increased monitoring during the dry season."
        ],
        'urban' => [
            "Urban expansion analysis detected 12 new structures in the change detection comparison. The majority are concentrated in the eastern development corridor.",
            "The confidence level of 98% indicates reliable change detection. The ChangeFormer model successfully distinguished between actual structural changes and seasonal vegetation differences.",
            "I recommend overlaying this analysis with zoning regulations to identify any unauthorized construction or land use violations."
        ],
        'agriculture' => [
            "Crop health analysis shows an average NDVI of 0.68, indicating moderate vegetation vigor. The irrigated areas cover approximately 73% of the surveyed agricultural land.",
            "Yield forecast models predict an 8% increase above baseline, driven by improved irrigation coverage. Three stress zones were identified that may benefit from targeted intervention.",
            "Consider implementing precision agriculture techniques in the flagged stress zones. Targeted fertilizer application could improve the NDVI by 0.1-0.15 in those areas."
        ],
        'disaster' => [
            "Disaster impact assessment has identified a 2.4 km² affected area with high severity. Seven structures show damage indicators in the satellite imagery comparison.",
            "The model confidence of 94% supports reliable damage assessment. Priority areas for ground verification include the northwestern cluster of structural damage.",
            "I recommend immediate deployment of damage assessment teams to the high-confidence zones. The satellite data can guide efficient routing for field verification."
        ]
    ];

    $pool = $responses[$mode] ?? $responses['urban'];

    // Check for specific questions
    if (str_contains($lc, 'help') || str_contains($lc, 'how')) {
        return "I can help you understand the analysis results. You can ask me about specific metrics, change detection methodology, or ecological recommendations for the $modeLabel analysis mode.";
    }

    if (str_contains($lc, 'export') || str_contains($lc, 'download')) {
        return "You can export your analysis results using the Export button in the top bar. PDF exports include the full report with images and metrics, while CSV exports provide tabular data for further analysis.";
    }

    return $pool[array_rand($pool)];
}
