<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    json_err('Method not allowed', 405);
}

$user = require_auth();
$plan = normalize_plan((string)($user['plan'] ?? 'free'));

$body = read_json_body();
$format = strtolower((string)($body['format'] ?? ''));
$report = $body['report'] ?? null;

if (!in_array($format, ['csv', 'pdf'], true)) {
    json_err('format must be csv or pdf', 400);
}
if (!is_array($report)) {
    json_err('report is required', 400);
}

if ($format === 'pdf' && in_array($plan, ['free', 'lite'], true)) {
    json_err('PDF export is available on Pro and Enterprise plans', 403);
}

if ($format === 'csv') {
    output_csv_report($report);
}

output_html_report($report);

function output_csv_report(array $report): never
{
    $filename = 'mercuryvision_report_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    if ($out === false) {
        json_err('Failed to create CSV output', 500);
    }

    fputcsv($out, ['Section', 'Field', 'Value']);

    $mode = (string)($report['mode'] ?? '');
    fputcsv($out, ['Report', 'Mode', $mode]);

    $timestamps = $report['timestamps'] ?? [];
    if (is_array($timestamps)) {
        foreach ($timestamps as $key => $value) {
            fputcsv($out, ['Timestamps', (string)$key, is_scalar($value) ? (string)$value : json_encode($value)]);
        }
    }

    $summary = (string)($report['summary'] ?? '');
    fputcsv($out, ['Decision support', 'Summary', $summary]);

    $plan = (string)($report['plan'] ?? '');
    if ($plan !== '') {
        fputcsv($out, ['Report', 'Plan', $plan]);
    }

    $visuals = $report['visuals'] ?? [];
    if (is_array($visuals)) {
        foreach ($visuals as $key => $value) {
            fputcsv($out, ['Visuals', (string)$key, format_scalar($value)]);
        }
    }

    $metrics = $report['metrics'] ?? [];
    if (is_array($metrics)) {
        foreach ($metrics as $row) {
            if (!is_array($row)) {
                continue;
            }
            $label = (string)($row['metric'] ?? $row['name'] ?? 'Metric');
            $before = format_scalar($row['before'] ?? '');
            $after = format_scalar($row['after'] ?? '');
            $delta = format_scalar($row['deltaPct'] ?? $row['delta'] ?? '');
            fputcsv($out, ['Metrics', $label . ' | Before', $before]);
            fputcsv($out, ['Metrics', $label . ' | After', $after]);
            fputcsv($out, ['Metrics', $label . ' | Delta', $delta]);
        }
    }

    $solutions = $report['solutions'] ?? [];
    if (is_array($solutions)) {
        foreach ($solutions as $i => $solution) {
            if (!is_array($solution)) {
                continue;
            }
            $prefix = 'Solution ' . ((int)$i + 1);
            fputcsv($out, ['Solutions', $prefix . ' Title', (string)($solution['title'] ?? '')]);
            fputcsv($out, ['Solutions', $prefix . ' Description', (string)($solution['description'] ?? '')]);
            fputcsv($out, ['Solutions', $prefix . ' Confidence', (string)($solution['confidence'] ?? '')]);
        }
    }

    fclose($out);
    exit;
}

function output_html_report(array $report): never
{
    header('Content-Type: text/html; charset=utf-8');

    $mode = h((string)($report['mode'] ?? ''));
    $summary = nl2br(h((string)($report['summary'] ?? '')));
    $metricsRows = build_metrics_rows($report['metrics'] ?? []);
    $solutionsRows = build_solution_rows($report['solutions'] ?? []);
    $timestampsRows = build_timestamp_rows($report['timestamps'] ?? []);

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>MercuryVision Report</title>';
    echo '<style>
        body{font-family:Inter,Arial,sans-serif;background:#fff;color:#111;margin:0;padding:28px;line-height:1.45}
        h1,h2{margin:0 0 10px}
        h1{font-size:24px}
        h2{font-size:18px;margin-top:22px}
        .meta{margin-bottom:14px;color:#444}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{border:1px solid #ddd;padding:8px;text-align:left;font-size:13px;vertical-align:top}
        th{background:#f5f5f5}
        .summary{border:1px solid #ddd;border-radius:8px;padding:12px;background:#fafafa}
    </style></head><body>';
    echo '<h1>MercuryVision Analytics Report</h1>';
    echo '<div class="meta"><strong>Mode:</strong> ' . $mode . ' <strong style="margin-left:14px">Generated:</strong> ' . h(date('c')) . '</div>';

    echo '<h2>Summary</h2><div class="summary">' . $summary . '</div>';

    echo '<h2>Metrics</h2>';
    echo '<table><thead><tr><th>Metric</th><th>Before</th><th>After</th><th>Delta</th></tr></thead><tbody>' . $metricsRows . '</tbody></table>';

    echo '<h2>Solutions</h2>';
    echo '<table><thead><tr><th>Title</th><th>Description</th><th>Confidence</th></tr></thead><tbody>' . $solutionsRows . '</tbody></table>';

    echo '<h2>Timestamps</h2>';
    echo '<table><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>' . $timestampsRows . '</tbody></table>';

    echo '</body></html>';
    exit;
}

function build_metrics_rows($metrics): string
{
    if (!is_array($metrics) || $metrics === []) {
        return '<tr><td colspan="4">No metrics</td></tr>';
    }

    $rows = '';
    foreach ($metrics as $row) {
        if (!is_array($row)) {
            continue;
        }
        $rows .= '<tr>'
            . '<td>' . h((string)($row['metric'] ?? $row['name'] ?? 'Metric')) . '</td>'
            . '<td>' . h(format_scalar($row['before'] ?? '')) . '</td>'
            . '<td>' . h(format_scalar($row['after'] ?? '')) . '</td>'
            . '<td>' . h(format_scalar($row['deltaPct'] ?? $row['delta'] ?? '')) . '</td>'
            . '</tr>';
    }

    return $rows !== '' ? $rows : '<tr><td colspan="4">No metrics</td></tr>';
}

function build_solution_rows($solutions): string
{
    if (!is_array($solutions) || $solutions === []) {
        return '<tr><td colspan="3">No solutions</td></tr>';
    }

    $rows = '';
    foreach ($solutions as $solution) {
        if (!is_array($solution)) {
            continue;
        }
        $rows .= '<tr>'
            . '<td>' . h((string)($solution['title'] ?? '')) . '</td>'
            . '<td>' . h((string)($solution['description'] ?? '')) . '</td>'
            . '<td>' . h((string)($solution['confidence'] ?? '')) . '</td>'
            . '</tr>';
    }

    return $rows !== '' ? $rows : '<tr><td colspan="3">No solutions</td></tr>';
}

function build_timestamp_rows($timestamps): string
{
    if (!is_array($timestamps) || $timestamps === []) {
        return '<tr><td colspan="2">No timestamps</td></tr>';
    }

    $rows = '';
    foreach ($timestamps as $key => $value) {
        $rows .= '<tr><td>' . h((string)$key) . '</td><td>' . h(format_scalar($value)) . '</td></tr>';
    }

    return $rows;
}

function format_scalar($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return '';
    }
    if (is_scalar($value)) {
        return (string)$value;
    }
    return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function normalize_plan(string $plan): string
{
    return match (strtolower(trim($plan))) {
        'lite' => 'lite',
        'pro' => 'pro',
        'enterprise' => 'enterprise',
        default => 'free',
    };
}
