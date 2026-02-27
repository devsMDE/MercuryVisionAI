<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';

$user = require_auth();
$uid = (string)$user['uid'];

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

$db->exec('CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uid TEXT NOT NULL,
    name TEXT NOT NULL,
    description TEXT DEFAULT "",
    created_at TEXT DEFAULT (datetime("now")),
    updated_at TEXT DEFAULT (datetime("now"))
)');

$db->exec('CREATE TABLE IF NOT EXISTS analyses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uid TEXT NOT NULL,
    project_id INTEGER NOT NULL,
    mode TEXT NOT NULL,
    payload TEXT NOT NULL,
    created_at TEXT DEFAULT (datetime("now"))
)');

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$action = (string)($_GET['action'] ?? '');
if ($action === '' && $method === 'POST') {
    $rawBody = read_json_body();
    $action = (string)($rawBody['action'] ?? '');
    $_POST['__json_body'] = json_encode($rawBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

if ($action === '') {
    $action = $method === 'GET' ? 'list_projects' : 'create_project';
}

if ($method === 'GET' && $action === 'list_projects') {
    $stmt = $db->prepare('SELECT id, name, description, created_at, updated_at FROM projects WHERE uid = ? ORDER BY updated_at DESC, created_at DESC');
    $stmt->execute([$uid]);
    $projects = $stmt->fetchAll();
    json_ok(['projects' => $projects]);
}

if ($method === 'GET' && $action === 'project_detail') {
    $projectId = (int)($_GET['project_id'] ?? 0);
    if ($projectId <= 0) {
        json_err('project_id is required', 400);
    }

    $projectStmt = $db->prepare('SELECT id, name, description, created_at, updated_at FROM projects WHERE id = ? AND uid = ?');
    $projectStmt->execute([$projectId, $uid]);
    $project = $projectStmt->fetch();
    if (!$project) {
        json_err('Project not found', 404);
    }

    $analysisStmt = $db->prepare('SELECT id, mode, payload, created_at FROM analyses WHERE project_id = ? AND uid = ? ORDER BY id DESC');
    $analysisStmt->execute([$projectId, $uid]);
    $rows = $analysisStmt->fetchAll();

    $analyses = [];
    foreach ($rows as $row) {
        $payload = json_decode((string)$row['payload'], true);
        if (!is_array($payload)) {
            $payload = [];
        }
        $analyses[] = [
            'id' => (int)$row['id'],
            'mode' => (string)$row['mode'],
            'created_at' => (string)$row['created_at'],
            'payload' => $payload,
        ];
    }

    json_ok([
        'project' => $project,
        'analyses' => $analyses,
    ]);
}

if ($method === 'POST' && $action === 'create_project') {
    $body = decode_post_json_body();
    $name = trim((string)($body['name'] ?? ''));
    $description = trim((string)($body['description'] ?? ''));
    if ($name === '') {
        json_err('Project name is required', 400);
    }

    $stmt = $db->prepare('INSERT INTO projects (uid, name, description, updated_at) VALUES (?, ?, ?, datetime("now"))');
    $stmt->execute([$uid, $name, $description]);
    $id = (int)$db->lastInsertId();

    json_ok([
        'project' => [
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ],
    ]);
}

if ($method === 'POST' && $action === 'save_analysis') {
    $body = decode_post_json_body();
    $projectId = (int)($body['project_id'] ?? 0);
    $mode = trim((string)($body['mode'] ?? ''));
    $payload = $body['payload'] ?? null;

    if ($projectId <= 0 || $mode === '' || !is_array($payload)) {
        json_err('project_id, mode and payload are required', 400);
    }

    $projectStmt = $db->prepare('SELECT id FROM projects WHERE id = ? AND uid = ?');
    $projectStmt->execute([$projectId, $uid]);
    if (!$projectStmt->fetch()) {
        json_err('Project not found', 404);
    }

    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson)) {
        json_err('Invalid payload', 400);
    }

    $insert = $db->prepare('INSERT INTO analyses (uid, project_id, mode, payload, created_at) VALUES (?, ?, ?, ?, datetime("now"))');
    $insert->execute([$uid, $projectId, $mode, $payloadJson]);
    $analysisId = (int)$db->lastInsertId();

    $db->prepare('UPDATE projects SET updated_at = datetime("now") WHERE id = ? AND uid = ?')->execute([$projectId, $uid]);

    json_ok(['analysis_id' => $analysisId]);
}

if ($method === 'POST' && $action === 'update_analysis') {
    $body = decode_post_json_body();
    $projectId = (int)($body['project_id'] ?? 0);
    $payload = $body['payload'] ?? null;

    if ($projectId <= 0 || !is_array($payload)) {
        json_err('project_id and payload are required', 400);
    }

    $projectStmt = $db->prepare('SELECT id FROM projects WHERE id = ? AND uid = ?');
    $projectStmt->execute([$projectId, $uid]);
    if (!$projectStmt->fetch()) {
        json_err('Project not found', 404);
    }

    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson)) {
        json_err('Invalid payload', 400);
    }

    // Update the most recent analysis for this project
    $analysisStmt = $db->prepare('SELECT id FROM analyses WHERE project_id = ? AND uid = ? ORDER BY id DESC LIMIT 1');
    $analysisStmt->execute([$projectId, $uid]);
    $analysisRow = $analysisStmt->fetch();

    if ($analysisRow) {
        $db->prepare('UPDATE analyses SET payload = ?, created_at = datetime("now") WHERE id = ?')
           ->execute([$payloadJson, $analysisRow['id']]);
        $db->prepare('UPDATE projects SET updated_at = datetime("now") WHERE id = ? AND uid = ?')->execute([$projectId, $uid]);
    }

    json_ok(['updated' => true]);
}

if ($method === 'POST' && $action === 'delete_project') {
    $body = decode_post_json_body();
    $projectId = (int)($body['project_id'] ?? 0);
    if ($projectId <= 0) {
        json_err('project_id is required', 400);
    }

    $db->prepare('DELETE FROM analyses WHERE project_id = ? AND uid = ?')->execute([$projectId, $uid]);
    $db->prepare('DELETE FROM projects WHERE id = ? AND uid = ?')->execute([$projectId, $uid]);

    json_ok([]);
}

json_err('Unknown action', 400);

function decode_post_json_body(): array
{
    if (isset($_POST['__json_body'])) {
        $cached = json_decode((string)$_POST['__json_body'], true);
        if (is_array($cached)) {
            return $cached;
        }
    }
    return read_json_body();
}
