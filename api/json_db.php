<?php
/**
 * api/json_db.php — Lightweight JSON fallback for PDO SQLite.
 * This ensures the app works even if the PHP SQLite extensions are missing.
 */
declare(strict_types=1);

class JsonPdo {
    private string $path;
    private array $data;

    public function __construct(string $dbPath) {
        $this->path = str_replace('.db', '.json', $dbPath);
        $this->data = file_exists($this->path) 
            ? (json_decode(file_get_contents($this->path), true) ?: $this->getDefaultData())
            : $this->getDefaultData();
    }

    private function getDefaultData(): array {
        return [
            'projects' => [],
            'analyses' => []
        ];
    }

    public function save(): void {
        $dir = dirname($this->path);
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        file_put_contents($this->path, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function exec(string $sql): int { return 0; }

    public function prepare(string $sql): JsonPdoStatement {
        return new JsonPdoStatement($this, $sql);
    }

    public function lastInsertId(): string {
        return (string)($_SESSION['last_json_id'] ?? '0');
    }

    public function &getData(): array {
        return $this->data;
    }
}

class JsonPdoStatement {
    private JsonPdo $db;
    private string $sql;
    private array $results = [];

    public function __construct(JsonPdo $db, string $sql) {
        $this->db = $db;
        $this->sql = trim($sql);
    }

    public function execute(array $params = []): bool {
        $data = &$this->db->getData();
        $sql = $this->sql;

        if (stripos($sql, 'INSERT INTO projects') !== false) {
            $id = count($data['projects']) + 1;
            $data['projects'][] = [
                'id' => $id,
                'uid' => $params[0],
                'name' => $params[1],
                'description' => $params[2],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->db->save();
            $_SESSION['last_json_id'] = $id;
            return true;
        }

        if (stripos($sql, 'INSERT INTO analyses') !== false) {
            $id = count($data['analyses']) + 1;
            $data['analyses'][] = [
                'id' => $id,
                'uid' => $params[0],
                'project_id' => (int)$params[1],
                'mode' => $params[2],
                'payload' => $params[3],
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->save();
            $_SESSION['last_json_id'] = $id;
            return true;
        }

        if (stripos($sql, 'SELECT id, name, description, created_at, updated_at FROM projects WHERE uid = ?') !== false) {
            $uid = $params[0];
            $res = [];
            foreach ($data['projects'] as $p) {
                if ($p['uid'] === $uid) $res[] = $p;
            }
            usort($res, fn($a, $b) => strcmp($b['updated_at'], $a['updated_at']));
            $this->results = $res;
            return true;
        }

        if (stripos($sql, 'SELECT id, name, description, created_at, updated_at FROM projects WHERE id = ? AND uid = ?') !== false) {
            $id = (int)$params[0];
            $uid = $params[1];
            foreach ($data['projects'] as $p) {
                if ($p['id'] === $id && $p['uid'] === $uid) {
                    $this->results = [$p];
                    return true;
                }
            }
            $this->results = [];
            return true;
        }

        if (stripos($sql, 'SELECT id, mode, payload, created_at FROM analyses WHERE project_id = ? AND uid = ?') !== false) {
            $pid = (int)$params[0];
            $uid = $params[1];
            $res = [];
            foreach ($data['analyses'] as $a) {
                if ($a['project_id'] === $pid && $a['uid'] === $uid) $res[] = $a;
            }
            usort($res, fn($a, $b) => $b['id'] - $a['id']);
            $this->results = $res;
            return true;
        }

        if (stripos($sql, 'UPDATE projects SET updated_at = datetime("now") WHERE id = ? AND uid = ?') !== false) {
            $id = (int)$params[0];
            $uid = $params[1];
            foreach ($data['projects'] as &$p) {
                if ($p['id'] === $id && $p['uid'] === $uid) {
                    $p['updated_at'] = date('Y-m-d H:i:s');
                }
            }
            $this->db->save();
            return true;
        }

        return true;
    }

    public function fetch() {
        return $this->results[0] ?? false;
    }

    public function fetchAll() {
        return $this->results;
    }
}
