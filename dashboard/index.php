<?php
// MercuryVision Studio - Dashboard
function parse_ini_bytes(string $raw): int
{
    $raw = trim($raw);
    if ($raw === '') {
        return 0;
    }
    if (is_numeric($raw)) {
        return (int)$raw;
    }
    $unit = strtolower(substr($raw, -1));
    $num = (float)substr($raw, 0, -1);
    return match ($unit) {
        'g' => (int)($num * 1024 * 1024 * 1024),
        'm' => (int)($num * 1024 * 1024),
        'k' => (int)($num * 1024),
        default => (int)$raw,
    };
}
$serverUploadMax = parse_ini_bytes((string)ini_get('upload_max_filesize'));
$serverPostMax = parse_ini_bytes((string)ini_get('post_max_size'));
$serverLimit = min(array_filter([$serverUploadMax, $serverPostMax, 10 * 1024 * 1024], static fn(int $v): bool => $v > 0));
if ($serverLimit <= 0) {
    $serverLimit = 2 * 1024 * 1024;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MercuryVision Studio</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* ═══════════════════════════════════════════════════════════════
           MercuryVision Studio — Premium Dark Theme
           Apple-inspired SaaS aesthetic with glassmorphism
           ═══════════════════════════════════════════════════════════════ */

        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #111118;
            --bg-card: rgba(255, 255, 255, 0.035);
            --bg-card-hover: rgba(255, 255, 255, 0.06);
            --bg-glass: rgba(18, 18, 28, 0.72);

            --gold: #3b82f6;
            --gold-light: #60a5fa;
            --gold-glow: rgba(59, 130, 246, 0.35);
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #818cf8 50%, #60a5fa 100%);

            --text-primary: #f0f0f5;
            --text-secondary: #9ca3af;
            --text-tertiary: #6b7280;

            --border: rgba(255, 255, 255, 0.07);
            --border-hover: rgba(255, 255, 255, 0.14);
            --border-active: rgba(59, 130, 246, 0.45);

            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.25);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.35);
            --shadow-lg: 0 16px 48px rgba(0, 0, 0, 0.45);
            --shadow-glow: 0 4px 20px var(--gold-glow);
            --shadow-glow-lg: 0 8px 40px var(--gold-glow);

            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.1);
            --success: #22c55e;
            --warning: #eab308;

            --font-family-en: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-family-ru: 'Inter', -apple-system, sans-serif;
            --font-family-kz: 'Inter', 'Roboto', sans-serif;
            --font-family: var(--font-family-en);
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-2xl: 24px;
        }

        [data-theme="light"] {
            --bg-primary: #f8f9fb;
            --bg-secondary: #eef0f4;
            --bg-card: #ffffff;
            --bg-card-hover: #f6f7f9;
            --bg-glass: rgba(255, 255, 255, 0.78);

            --gold: #2563eb;
            --gold-light: #3b82f6;
            --gold-glow: rgba(37, 99, 235, 0.25);

            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-tertiary: #94a3b8;

            --border: rgba(0, 0, 0, 0.06);
            --border-hover: rgba(0, 0, 0, 0.12);

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
        }

        /* ── Reset & Base ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: var(--font-family);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            transition: background 0.5s var(--ease), color 0.4s var(--ease);
            letter-spacing: -0.011em;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.025em;
            line-height: 1.25;
        }

        p {
            line-height: 1.65;
            letter-spacing: -0.006em;
        }

        [lang="ru"] body { --font-family: var(--font-family-ru); }
        [lang="kz"] body { --font-family: var(--font-family-kz); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-hover); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-tertiary); }

        /* ── Header ── */
        header {
            position: sticky; top: 0; z-index: 105;
            background: var(--bg-glass);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border-bottom: 1px solid var(--border);
            padding: 10px 24px;
            display: grid; grid-template-columns: 1fr auto 1fr;
            align-items: center;
            transition: background 0.4s var(--ease);
        }
        [data-theme="light"] header { background: var(--bg-glass); }

        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-primary); transition: opacity 0.2s; }
        .logo:hover { opacity: 0.85; }
        .logo-icon {
            width: 36px; height: 36px;
            background: var(--accent-gradient);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px; color: #fff;
            box-shadow: var(--shadow-glow);
            transition: transform 0.3s var(--ease-bounce);
        }
        .logo:hover .logo-icon { transform: scale(1.06) rotate(-2deg); }
        .logo-text { font-weight: 700; font-size: 16px; letter-spacing: -0.035em; }

        .usage-bar { display: flex; flex-direction: column; gap: 5px; align-items: center; }
        .usage-text { font-size: 12px; color: var(--text-tertiary); font-weight: 500; letter-spacing: 0.02em; }
        .progress-wrapper { width: 220px; height: 5px; background: var(--bg-card-hover); border-radius: 100px; overflow: hidden; border: 1px solid var(--border); }
        .progress-fill { height: 100%; background: var(--accent-gradient); width: 20%; border-radius: 100px; transition: width 0.6s var(--ease); }

        .header-actions { display: flex; justify-content: flex-end; position: relative; }
        .profile-btn {
            display: flex; align-items: center; gap: 10px;
            background: var(--bg-card); border: 1px solid var(--border);
            padding: 5px 14px 5px 5px; border-radius: 100px;
            cursor: pointer; color: var(--text-primary);
            transition: all 0.3s var(--ease);
            box-shadow: var(--shadow-sm);
        }
        .profile-btn:hover { background: var(--bg-card-hover); border-color: var(--border-hover); transform: translateY(-1px); box-shadow: var(--shadow-md); }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--accent-gradient);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
            box-shadow: var(--shadow-sm);
        }
        .profile-info { display: flex; flex-direction: column; align-items: flex-start; }
        .profile-name { font-size: 13px; font-weight: 600; }
        .profile-plan { font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.6px; }

        /* ── Profile Dropdown ── */
        .dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 240px;
            background: var(--bg-glass);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 8px; display: none; flex-direction: column; gap: 2px; z-index: 100;
        }
        .dropdown-menu.show { display: flex; animation: slideDown 0.35s var(--ease); }
        @keyframes slideDown {
            0% { opacity: 0; transform: translateY(-8px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .dropdown-item {
            padding: 10px 12px; border-radius: var(--radius-sm);
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; font-weight: 500; color: var(--text-primary);
            cursor: pointer; transition: all 0.2s var(--ease);
            text-decoration: none; border: none; background: transparent;
            width: 100%; text-align: left;
        }
        .dropdown-item:hover:not(.no-hover) { background: var(--bg-card-hover); transform: translateX(2px); color: var(--gold); }

        .theme-segmented { display: flex; background: var(--bg-card); border-radius: var(--radius-sm); padding: 3px; margin: 4px; border: 1px solid var(--border); }
        .theme-btn {
            flex: 1; display: flex; justify-content: center; align-items: center;
            padding: 6px 0; border: none; background: transparent;
            color: var(--text-tertiary); border-radius: 6px; cursor: pointer;
            transition: all 0.25s var(--ease);
        }
        .theme-btn:hover { color: var(--text-secondary); }
        .theme-btn.active { background: var(--bg-primary); color: var(--text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border); }

        .dropdown-divider { height: 1px; background: var(--border); margin: 4px; }
        .btn-upgrade-inline { background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(129, 140, 248, 0.05) 100%); color: var(--gold); font-weight: 600; border: 1px solid var(--border); justify-content: space-between; }
        .btn-upgrade-inline:hover { background: rgba(59, 130, 246, 0.15); border-color: var(--border-active); }
        .text-danger { color: var(--danger) !important; }
        .text-danger:hover { background: var(--danger-bg) !important; }

        /* ── App Layout ── */
        .app-layout { display: flex; flex: 1; overflow: hidden; height: calc(100vh - 62px); }
        .sidebar {
            width: 256px; background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            padding: 20px 14px; display: flex; flex-direction: column; flex-shrink: 0; z-index: 40;
            transition: background 0.4s var(--ease);
        }
        .sidebar-nav { display: flex; flex-direction: column; gap: 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: var(--radius-md);
            background: transparent; border: 1px solid transparent;
            color: var(--text-secondary); cursor: pointer;
            transition: all 0.25s var(--ease);
            font-size: 14px; font-weight: 600; text-align: left;
        }
        .nav-item:hover { background: var(--bg-card-hover); color: var(--text-primary); border-color: var(--border); }
        .nav-item.active { background: rgba(59, 130, 246, 0.1); color: var(--gold); border-color: rgba(59, 130, 246, 0.2); }

        .sidebar-history { margin-top: 20px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 2px; border-top: 1px solid var(--border); padding-top: 14px; min-height: 0; }
        .sidebar-heading { font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; padding: 0 8px; }
        .history-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px; border-radius: var(--radius-sm);
            background: transparent; border: none;
            color: var(--text-secondary); cursor: pointer;
            transition: all 0.2s var(--ease);
            font-size: 13px; text-align: left;
            overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
        }
        .history-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .history-item.active { background: rgba(59, 130, 246, 0.1); color: var(--gold); }

        .analysis-chat-item { display: flex; align-items: center; gap: 8px; padding: 6px 12px 6px 32px; font-size: 12px; color: var(--text-secondary); background: transparent; border: none; cursor: pointer; text-align: left; transition: color 0.2s; }
        .analysis-chat-item:hover { color: var(--text-primary); }
        .analysis-chat-item i { margin-right: 4px; }

        .app-content { flex: 1; overflow-y: auto; overflow-x: hidden; position: relative; background: var(--bg-primary); }
        .view-section { display: none; animation: fadeIn 0.5s var(--ease); }
        .view-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* ── AI Right Sidebar ── */
        .right-sidebar {
            position: fixed; top: 62px; right: -420px; width: 420px; bottom: 0;
            background: var(--bg-glass);
            backdrop-filter: blur(24px) saturate(1.4);
            -webkit-backdrop-filter: blur(24px) saturate(1.4);
            border-left: 1px solid var(--border);
            box-shadow: -8px 0 40px rgba(0,0,0,0.3);
            z-index: 110; display: flex; flex-direction: column;
            padding: 24px 20px;
            transition: right 0.4s var(--ease);
            overflow-y: auto;
        }
        .right-sidebar.open { right: 0; }
        .btn-close-sidebar { position: absolute; top: 18px; right: 18px; background: var(--bg-card); border: 1px solid var(--border); color: var(--text-secondary); cursor: pointer; border-radius: var(--radius-sm); padding: 6px; transition: all 0.2s; }
        .btn-close-sidebar:hover { color: var(--text-primary); background: var(--bg-card-hover); border-color: var(--border-hover); }

        .floating-ai-btn {
            position: fixed; bottom: 24px; right: 24px;
            width: 58px; height: 58px; border-radius: 50%;
            background: var(--accent-gradient);
            color: #fff; box-shadow: var(--shadow-glow-lg);
            display: none; align-items: center; justify-content: center;
            cursor: pointer; border: none; z-index: 106;
            transition: all 0.3s var(--ease-bounce);
        }
        .floating-ai-btn:hover { transform: scale(1.1) translateY(-2px); box-shadow: 0 12px 48px var(--gold-glow); }
        .floating-ai-btn i { animation: floatPulse 2.5s ease-in-out infinite; }
        @keyframes floatPulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.8; transform: scale(0.92); } }
        [data-theme="light"] .floating-ai-btn { color: #fff; }

        .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }

        .main-container { max-width: 1280px; margin: 0 auto; padding: 40px 28px; width: 100%; }
        .section-title {
            font-size: 18px; font-weight: 700; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            letter-spacing: -0.03em;
            line-height: 1.3;
        }

        /* ── Projects Panel ── */
        .projects-header-title {
            font-size: 22px; font-weight: 800; letter-spacing: -0.035em;
            line-height: 1.2; margin-bottom: 4px;
        }
        .projects-subtitle {
            color: var(--text-tertiary); font-size: 13.5px; font-weight: 450;
            line-height: 1.5; letter-spacing: -0.005em;
        }
        .projects-stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 20px 16px;
            text-align: center; transition: all 0.3s var(--ease);
            box-shadow: var(--shadow-sm);
        }
        .projects-stat-card:hover {
            transform: translateY(-2px); box-shadow: var(--shadow-md);
            border-color: var(--border-hover);
        }
        .projects-stat-value {
            font-size: 28px; font-weight: 800; letter-spacing: -0.04em;
            line-height: 1.1;
        }
        .projects-stat-label {
            font-size: 11px; color: var(--text-tertiary); font-weight: 650;
            margin-top: 6px; text-transform: uppercase; letter-spacing: 0.06em;
        }
        .projects-search-bar {
            display: flex; gap: 10px; margin-bottom: 20px;
            flex-wrap: wrap; align-items: center;
        }
        .projects-search-wrap {
            position: relative; flex: 1; min-width: 200px;
        }
        .projects-search-wrap i {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%); color: var(--text-tertiary);
            pointer-events: none;
        }
        .projects-search-input {
            width: 100%; padding: 10px 14px 10px 40px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text-primary);
            font-family: inherit; font-size: 13px; font-weight: 500;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .projects-search-input:focus {
            outline: none; border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .projects-search-input::placeholder {
            color: var(--text-tertiary); font-weight: 450;
        }
        .projects-filter-group {
            display: flex; gap: 5px; background: var(--bg-card);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            padding: 3px;
        }
        .projects-filter-btn {
            padding: 6px 14px; border: none; background: transparent;
            color: var(--text-tertiary); border-radius: 6px; cursor: pointer;
            font-size: 12px; font-weight: 600; transition: all 0.2s var(--ease);
            letter-spacing: 0.01em;
        }
        .projects-filter-btn:hover { color: var(--text-secondary); }
        .projects-filter-btn.active {
            background: var(--bg-primary); color: var(--text-primary);
            box-shadow: var(--shadow-sm); border: 1px solid var(--border);
        }
        .projects-mode-select {
            padding: 8px 12px; background: var(--bg-card);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            color: var(--text-primary); font-family: inherit;
            font-size: 12px; font-weight: 500; max-width: 160px;
            transition: border-color 0.2s; color-scheme: dark;
        }
        .projects-mode-select:focus { outline: none; border-color: var(--gold); }
        [data-theme="light"] .projects-mode-select { color-scheme: light; }
        .projects-mode-select option { background: #16161f; color: #f0f0f5; }
        [data-theme="light"] .projects-mode-select option { background: #fff; color: #0f172a; }
        .project-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md); overflow: hidden;
            display: flex; flex-direction: column;
            transition: all 0.3s var(--ease); box-shadow: var(--shadow-sm);
        }
        .project-card:hover {
            transform: translateY(-3px); box-shadow: var(--shadow-md);
            border-color: var(--border-hover);
        }
        .project-card-header {
            padding: 14px 16px 10px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .project-card-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .project-card-mode {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .project-card-date {
            font-size: 10.5px; color: var(--text-tertiary);
            font-weight: 450; letter-spacing: 0.01em;
        }
        .project-card-body {
            padding: 14px 16px; flex: 1;
        }
        .project-card-name {
            font-size: 14px; font-weight: 650; color: var(--text-primary);
            margin-bottom: 4px; line-height: 1.35; letter-spacing: -0.015em;
        }
        .project-card-meta {
            font-size: 12px; color: var(--text-tertiary);
            font-weight: 450; letter-spacing: 0.005em;
        }
        .project-card-footer {
            padding: 10px 16px; border-top: 1px solid var(--border);
            display: flex; gap: 8px;
        }
        .project-card-btn {
            flex: 1; padding: 7px 10px; background: var(--bg-card);
            border: 1px solid var(--border); color: var(--text-primary);
            border-radius: 6px; font-size: 12px; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center;
            justify-content: center; gap: 5px;
            transition: all 0.2s var(--ease);
        }
        .project-card-btn:hover {
            background: var(--bg-card-hover); border-color: var(--border-hover);
            transform: translateY(-1px);
        }
        .project-card-btn-ai {
            padding: 7px 10px; background: none; border: 1px solid var(--border);
            color: var(--gold); border-radius: 6px; font-size: 12px;
            cursor: pointer; display: inline-flex; align-items: center;
            justify-content: center; transition: all 0.2s var(--ease);
        }
        .project-card-btn-ai:hover {
            background: rgba(59, 130, 246, 0.08); border-color: var(--gold);
        }
        .project-delete-btn {
            background: none; border: none; cursor: pointer; padding: 4px;
            color: var(--text-tertiary); border-radius: 6px;
            transition: color 0.2s;
        }
        .project-delete-btn:hover { color: var(--danger); }
        .projects-empty {
            grid-column: 1 / -1; text-align: center; padding: 80px 24px;
            color: var(--text-tertiary); background: var(--bg-card);
            border-radius: var(--radius-lg); border: 1px dashed var(--border);
        }
        .projects-empty-icon {
            width: 68px; height: 68px; border-radius: 50%;
            background: var(--bg-secondary); display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .projects-empty h3 {
            font-size: 17px; font-weight: 700; margin-bottom: 8px;
            color: var(--text-secondary);
        }
        .projects-empty p {
            font-size: 13px; max-width: 300px;
            margin: 0 auto 20px; line-height: 1.6;
        }

        /* ── Analysis Modes ── */
        .modes-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; margin-bottom: 8px; }
        .mode-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 14px; cursor: pointer;
            transition: all 0.3s var(--ease);
            display: flex; align-items: flex-start; gap: 12px; position: relative;
        }
        .mode-card:hover:not(.locked) { border-color: var(--border-hover); background: var(--bg-card-hover); transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .mode-card.active { border-color: var(--gold); background: rgba(59, 130, 246, 0.06); box-shadow: 0 0 0 1px var(--gold), var(--shadow-glow); }

        .mode-icon {
            width: 38px; height: 38px;
            background: var(--bg-secondary); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border); color: var(--text-secondary);
            flex-shrink: 0; transition: all 0.3s var(--ease);
        }
        .mode-card.active .mode-icon { color: var(--gold); border-color: var(--gold); background: rgba(59, 130, 246, 0.08); }

        .mode-info { flex: 1; }
        .mode-title { font-size: 14px; font-weight: 600; margin-bottom: 3px; }
        .mode-desc { font-size: 12px; color: var(--text-tertiary); line-height: 1.4; }

        .mode-card.locked { opacity: 0.5; cursor: pointer; }
        .locked-badge {
            position: absolute; top: -8px; right: 10px;
            background: var(--bg-secondary); border: 1px solid var(--border);
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 10px; display: flex; align-items: center; gap: 3px;
            color: var(--text-tertiary); text-transform: uppercase;
        }

        #mode-error { color: var(--gold); font-size: 13px; margin-bottom: 24px; display: none; font-weight: 500; background: rgba(59, 130, 246, 0.06); padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid rgba(59, 130, 246, 0.2); }

        .model-selector {
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 14px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        .model-selector:hover { border-color: var(--border-hover); box-shadow: var(--shadow-sm); }
        .model-selector label { font-size: 12.5px; color: var(--text-secondary); font-weight: 650; min-width: 140px; letter-spacing: -0.01em; }
        .model-select {
            flex: 1; background: var(--bg-primary); color: var(--text-primary);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            padding: 9px 14px; font-size: 13px; font-family: inherit;
            font-weight: 500; letter-spacing: -0.005em;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: pointer; appearance: none; -webkit-appearance: none;
            color-scheme: dark;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 32px;
        }
        .model-select:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        [data-theme="light"] .model-select { color-scheme: light; background: #fff; }
        .model-select option { background: #16161f; color: #f0f0f5; }
        [data-theme="light"] .model-select option { background: #fff; color: #0f172a; }

        /* ── Settings Provider Cards ── */
        .provider-card {
            border: 2px solid var(--border); background: var(--bg-card);
            border-radius: var(--radius-md); padding: 14px 12px;
            display: flex; flex-direction: column; align-items: center;
            gap: 6px; transition: all 0.25s var(--ease); cursor: pointer;
            position: relative; overflow: hidden;
        }
        .provider-card::before {
            content: ''; position: absolute; inset: 0;
            background: var(--accent-gradient); opacity: 0;
            transition: opacity 0.3s var(--ease);
        }
        .provider-card:hover { border-color: var(--border-hover); transform: translateY(-1px); box-shadow: var(--shadow-sm); }
        .provider-card.selected {
            border-color: var(--gold); background: rgba(59, 130, 246, 0.06);
            box-shadow: 0 0 0 1px var(--gold), var(--shadow-glow);
        }
        .provider-card.selected::after {
            content: '✓'; position: absolute; top: 6px; right: 8px;
            font-size: 12px; font-weight: 800; color: var(--gold);
        }
        .provider-card-emoji { font-size: 22px; position: relative; z-index: 1; }
        .provider-card-name { font-size: 12.5px; font-weight: 700; color: var(--text-primary); position: relative; z-index: 1; letter-spacing: -0.01em; }
        .provider-card-model { font-size: 10.5px; color: var(--text-tertiary); position: relative; z-index: 1; font-weight: 450; }
        .provider-card-badge {
            font-size: 9px; font-weight: 700; padding: 2px 7px;
            border-radius: 4px; position: relative; z-index: 1;
            letter-spacing: 0.03em;
        }

        /* ── Upload Zones ── */
        .upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
        .dropzone {
            border: 2px dashed var(--border); border-radius: var(--radius-xl);
            background: var(--bg-card); height: 260px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s var(--ease);
            position: relative; overflow: hidden;
            text-align: center; padding: 24px;
        }
        .dropzone:hover { border-color: var(--gold); background: var(--bg-card-hover); }
        .dropzone:hover .dropzone-icon { color: var(--gold); transform: translateY(-2px); }
        .dropzone.dragover { border-color: var(--gold); background: rgba(59, 130, 246, 0.06); box-shadow: inset 0 0 30px rgba(59, 130, 246, 0.05); }
        .dropzone.loading { cursor: wait; pointer-events: none; }

        .dz-input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 20; }
        .dropzone-content { position: relative; z-index: 10; pointer-events: none; }
        .dropzone-icon { margin-bottom: 14px; color: var(--text-tertiary); transition: all 0.3s var(--ease); }
        .dropzone-title { font-size: 15px; font-weight: 600; margin-bottom: 6px; }
        .dropzone-hint { font-size: 12px; color: var(--text-tertiary); line-height: 1.5; }

        .dropzone-loader {
            position: absolute; inset: 0; background: var(--bg-secondary);
            display: none; align-items: center; justify-content: center; z-index: 40;
            border-radius: calc(var(--radius-xl) - 2px);
        }
        .dropzone.loading .dropzone-loader { display: flex; }

        .preview-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 15; display: none; }
        .remove-img-btn {
            position: absolute; top: 10px; right: 10px; z-index: 30;
            width: 30px; height: 30px;
            background: rgba(0,0,0,0.55); backdrop-filter: blur(6px);
            color: #fff; border: none; border-radius: var(--radius-sm);
            cursor: pointer; display: none; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .remove-img-btn:hover { background: var(--danger); transform: scale(1.08); }

        /* ── Action Bar ── */
        .action-bar { display: flex; align-items: center; justify-content: center; margin-bottom: 36px; position: relative; }
        .btn-analyze {
            padding: 14px 44px;
            background: var(--accent-gradient);
            color: #fff; border: none; border-radius: var(--radius-md);
            font-size: 15px; font-weight: 700;
            cursor: pointer; display: inline-flex; align-items: center; gap: 10px;
            transition: all 0.35s var(--ease);
            box-shadow: var(--shadow-glow);
            letter-spacing: -0.01em;
        }
        [data-theme="light"] .btn-analyze { color: #fff; }
        .btn-analyze:hover:not(:disabled) { transform: translateY(-3px); filter: brightness(1.08); box-shadow: var(--shadow-glow-lg); }
        .btn-analyze:active:not(:disabled) { transform: translateY(-1px); }
        .btn-analyze:disabled {
            opacity: 0.4; cursor: not-allowed; transform: none;
            box-shadow: none; background: var(--bg-card); color: var(--text-tertiary);
            border: 1px solid var(--border);
        }
        .inline-error { color: var(--danger); font-size: 13px; position: absolute; margin-top: 60px; display: none; font-weight: 500; }

        /* ── Results Area ── */
        #results-area {
            display: none; opacity: 0;
            transition: opacity 0.6s var(--ease);
            border-top: 1px solid var(--border);
            padding-top: 36px;
        }

        .results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .export-actions { display: flex; gap: 10px; }
        .btn-outline {
            padding: 8px 14px; background: var(--bg-card);
            border: 1px solid var(--border); color: var(--text-primary);
            border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.25s var(--ease);
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-outline:hover { background: var(--bg-card-hover); border-color: var(--border-hover); transform: translateY(-1px); box-shadow: var(--shadow-sm); }
        .btn-outline:active { transform: translateY(0); }

        /* ── Visual Slider ── */
        .visuals-container { display: flex; flex-direction: column; gap: 20px; margin-bottom: 32px; }
        @media (min-width: 900px) {
            .visuals-container { flex-direction: row; }
            .visuals-slider-wrap { flex: 2; }
            .visuals-output-wrap { flex: 1; }
        }

        .image-slider {
            position: relative; width: 100%; aspect-ratio: 16/9;
            overflow: hidden; border-radius: var(--radius-lg);
            border: 1px solid var(--border); background: #000;
            box-shadow: var(--shadow-md); user-select: none;
        }
        .image-slider img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        .slider-after-container { position: absolute; top: 0; left: 0; width: 50%; height: 100%; overflow: hidden; border-right: 2px solid var(--gold); }
        .slider-after-container img { width: 200%; max-width: none; }
        .slider-handle {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 34px; height: 34px;
            background: var(--accent-gradient); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.5), var(--shadow-glow);
            cursor: ew-resize; z-index: 10;
            transition: transform 0.15s;
        }
        .slider-handle:hover { transform: translate(-50%, -50%) scale(1.12); }
        .slider-tag {
            position: absolute; top: 10px;
            background: rgba(0,0,0,0.55); backdrop-filter: blur(8px);
            padding: 3px 10px; border-radius: 6px;
            font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9);
            z-index: 5; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .tag-before { right: 10px; }
        .tag-after { left: 10px; }

        .visual-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); overflow: hidden;
            display: flex; flex-direction: column; height: 100%;
            transition: box-shadow 0.3s var(--ease);
        }
        .visual-header {
            padding: 10px 14px; border-bottom: 1px solid var(--border);
            font-size: 12px; font-weight: 600;
            display: flex; align-items: center; gap: 6px;
            background: var(--bg-secondary); flex-shrink: 0;
            text-transform: uppercase; letter-spacing: 0.3px; color: var(--text-secondary);
        }
        .visual-img-container { flex: 1; position: relative; background: #000; min-height: 200px; }
        .visual-img-container img { width: 100%; height: 100%; object-fit: contain; position: absolute; inset: 0; }

        /* ── Metrics Grid ── */
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 28px; }
        .metric-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 16px;
            transition: all 0.3s var(--ease);
            box-shadow: var(--shadow-sm);
            animation: metricFadeIn 0.5s var(--ease) both;
        }
        .metric-card:nth-child(1) { animation-delay: 0.05s; }
        .metric-card:nth-child(2) { animation-delay: 0.1s; }
        .metric-card:nth-child(3) { animation-delay: 0.15s; }
        .metric-card:nth-child(4) { animation-delay: 0.2s; }
        .metric-card:nth-child(5) { animation-delay: 0.25s; }
        .metric-card:nth-child(6) { animation-delay: 0.3s; }
        .metric-card:nth-child(7) { animation-delay: 0.35s; }
        @keyframes metricFadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        .metric-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--border-hover); }
        .metric-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: var(--text-secondary); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
        .metric-stats { display: flex; justify-content: space-between; align-items: flex-end; }
        .stat-col { display: flex; flex-direction: column; gap: 2px; }
        .stat-label { font-size: 10px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .stat-val { font-size: 20px; font-weight: 800; color: var(--text-primary); line-height: 1.1; letter-spacing: -0.03em; }
        .stat-diff { font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 3px; }
        .diff-pos { background: rgba(34, 197, 94, 0.1); color: var(--success); }
        .diff-neg { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        /* ── AI Section ── */
        .ai-section {
            background: linear-gradient(180deg, rgba(59, 130, 246, 0.04) 0%, transparent 100%);
            border: 1px solid var(--border); border-radius: var(--radius-2xl);
            padding: 32px; text-align: center; position: relative; overflow: hidden;
        }
        .ai-section::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); }

        .ai-prompt { max-width: 600px; margin: 0 auto; }
        .ai-title {
            font-size: 22px; font-weight: 700; margin-bottom: 10px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            color: var(--text-primary); letter-spacing: -0.03em;
            line-height: 1.25;
        }
        .ai-desc { font-size: 14px; color: var(--text-secondary); margin-bottom: 20px; line-height: 1.65; letter-spacing: -0.005em; }

        .btn-ai {
            padding: 12px 28px;
            background: var(--accent-gradient);
            border: none; color: #fff; border-radius: var(--radius-md);
            font-size: 14px; font-weight: 700; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.3s var(--ease);
            box-shadow: var(--shadow-glow);
        }
        .btn-ai:hover { box-shadow: var(--shadow-glow-lg); transform: translateY(-2px); filter: brightness(1.05); }
        [data-theme="light"] .btn-ai { color: #fff; }

        /* ── Chat Bubbles ── */
        .chat-bubble {
            padding: 12px 16px; border-radius: var(--radius-md);
            margin-bottom: 10px; max-width: 88%; font-size: 14px; line-height: 1.6;
            animation: chatSlideIn 0.35s var(--ease) both;
        }
        @keyframes chatSlideIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .chat-user {
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text-primary); margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .chat-bot {
            background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.15);
            color: var(--text-primary); margin-right: auto;
            border-top-left-radius: 4px;
        }

        /* ── AI Output ── */
        #ai-output { text-align: left; margin-top: 24px; display: none; }
        .problem-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 18px; margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s var(--ease);
        }
        .problem-card:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); border-color: var(--border-hover); }
        .problem-header { font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .solution-item { background: rgba(59, 130, 246, 0.03); border-left: 3px solid var(--gold); padding: 14px; margin-bottom: 10px; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
        .solution-title { font-weight: 600; color: var(--text-primary); margin-bottom: 6px; font-size: 14px; }
        .solution-pros-cons { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; }
        .pro-list, .con-list { margin: 0; padding-left: 16px; }
        .pro-list li { color: #4ade80; margin-bottom: 4px; }
        .con-list li { color: #f87171; margin-bottom: 4px; }

        /* ── Checkout Modal ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: none; align-items: center; justify-content: center;
            z-index: 1000; opacity: 0;
            transition: opacity 0.35s var(--ease);
        }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-content {
            background: var(--bg-glass);
            backdrop-filter: blur(30px) saturate(1.4);
            -webkit-backdrop-filter: blur(30px) saturate(1.4);
            border: 1px solid var(--border); border-radius: var(--radius-2xl);
            padding: 32px; width: 100%; max-width: 480px;
            position: relative; box-shadow: var(--shadow-lg);
            transform: translateY(16px) scale(0.98);
            transition: transform 0.4s var(--ease);
        }
        .modal-overlay.active .modal-content { transform: translateY(0) scale(1); }
        .modal-close {
            position: absolute; top: 14px; right: 14px;
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text-secondary); cursor: pointer;
            border-radius: var(--radius-sm); padding: 6px;
            transition: all 0.2s;
        }
        .modal-close:hover { background: var(--bg-card-hover); color: var(--text-primary); }

        .plan-options { display: grid; gap: 12px; margin: 20px 0; }
        .plan-card-opt {
            padding: 14px 16px; border-radius: var(--radius-lg);
            border: 1px solid var(--border); background: var(--bg-card);
            cursor: pointer; display: flex; justify-content: space-between; align-items: center;
            transition: all 0.25s var(--ease);
        }
        .plan-card-opt:hover { border-color: var(--gold); background: var(--bg-card-hover); transform: translateY(-1px); }
        .plan-card-opt.selected { border-width: 2px; border-color: var(--gold); background: rgba(59, 130, 246, 0.06); box-shadow: 0 0 0 1px var(--gold); }

        .form-input {
            width: 100%; padding: 11px 14px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text-primary);
            font-family: inherit; font-size: 14px; margin-top: 8px;
            transition: border-color 0.2s, box-shadow 0.2s;
            color-scheme: dark;
        }
        [data-theme="light"] .form-input { color-scheme: light; }
        .form-input option { background: #16161f; color: #f0f0f5; }
        [data-theme="light"] .form-input option { background: #fff; color: #0f172a; }
        .form-input:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12); }

        .btn-primary {
            width: 100%; padding: 13px;
            background: var(--accent-gradient);
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-size: 15px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.3s var(--ease);
            box-shadow: var(--shadow-glow);
        }
        .btn-primary:hover { box-shadow: var(--shadow-glow-lg); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(1px); }
        [data-theme="light"] .btn-primary { color: #fff; }

        .ai-summary {
            background: rgba(59, 130, 246, 0.04); border: 1px solid rgba(59, 130, 246, 0.15);
            padding: 16px; border-radius: var(--radius-md);
            margin-bottom: 20px; color: var(--text-secondary); line-height: 1.7;
            font-size: 14px;
        }

        .cost-badge {
            font-size: 12px; font-weight: 500; opacity: 0.75;
            margin-left: 6px; background: rgba(0,0,0,0.2);
            padding: 2px 8px; border-radius: 100px;
        }

        .lang-btn {
            flex: 1; padding: 6px 0; background: transparent; border: none;
            color: var(--text-tertiary); border-radius: 6px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all 0.2s var(--ease);
        }
        .lang-btn:hover { color: var(--text-secondary); }
        .lang-btn.active { background: var(--bg-primary); color: var(--text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border); }

        /* ── API Key Info Banner ── */
        .api-key-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.06) 0%, rgba(129, 140, 248, 0.04) 100%);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: var(--radius-md); padding: 14px 16px;
            margin-bottom: 16px;
        }
        .api-key-info h4 { font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-primary); display: flex; align-items: center; gap: 6px; }
        .api-key-info p { font-size: 12px; color: var(--text-tertiary); line-height: 1.5; margin: 0; }
        .api-key-info a { color: var(--gold); text-decoration: underline; text-underline-offset: 2px; }

        /* ── AI Output Fade-in Animations ── */
        .ai-fade-in { animation: aiFadeIn 0.6s var(--ease) both; }
        .ai-fade-in-delay-1 { animation-delay: 0.1s; }
        .ai-fade-in-delay-2 { animation-delay: 0.2s; }
        .ai-fade-in-delay-3 { animation-delay: 0.3s; }
        .ai-fade-in-delay-4 { animation-delay: 0.4s; }
        @keyframes aiFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* ═══════════════════════════════════════════════
           Projects Page
           ═══════════════════════════════════════════════ */
        .projects-header-title {
            font-size: 20px; font-weight: 800; letter-spacing: -0.03em;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 0;
        }
        .projects-subtitle {
            color: var(--text-tertiary); font-size: 13px; font-weight: 500; margin-top: 2px;
        }

        /* Stats row */
        .projects-stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 20px 12px;
            text-align: center; transition: border-color 0.2s, transform 0.2s var(--ease);
        }
        .projects-stat-card:hover { border-color: var(--border-hover); transform: translateY(-2px); }
        .projects-stat-value { font-size: 28px; font-weight: 800; letter-spacing: -0.04em; }
        .projects-stat-label {
            font-size: 11px; color: var(--text-tertiary); font-weight: 700;
            margin-top: 4px; text-transform: uppercase; letter-spacing: 0.6px;
        }

        /* Search bar */
        .projects-search-bar {
            display: flex; gap: 10px; margin-bottom: 20px;
            flex-wrap: wrap; align-items: center;
        }
        .projects-search-wrap {
            position: relative; flex: 1; min-width: 200px;
        }
        .projects-search-wrap > [data-lucide] {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: var(--text-tertiary); pointer-events: none;
        }
        .projects-search-input {
            width: 100%; padding: 9px 12px 9px 38px;
            font-size: 13px; font-weight: 500; font-family: var(--font-family);
            background: var(--bg-card); color: var(--text-primary);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            outline: none; transition: border-color 0.2s;
        }
        .projects-search-input::placeholder { color: var(--text-tertiary); }
        .projects-search-input:focus { border-color: var(--gold); }

        /* Filter buttons */
        .projects-filter-group {
            display: flex; gap: 4px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 4px;
        }
        .projects-filter-btn {
            font-size: 12px; font-weight: 600; font-family: var(--font-family);
            padding: 5px 14px; border: none; background: transparent;
            color: var(--text-tertiary); border-radius: 6px; cursor: pointer;
            transition: all 0.2s var(--ease);
        }
        .projects-filter-btn:hover { color: var(--text-primary); background: var(--bg-card-hover); }
        .projects-filter-btn.active {
            color: #fff; background: var(--accent-gradient); box-shadow: var(--shadow-glow);
        }

        /* Mode select dropdown */
        .projects-mode-select {
            font-size: 12px; font-weight: 500; font-family: var(--font-family);
            padding: 8px 12px; max-width: 170px;
            background: var(--bg-card); color: var(--text-primary);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            cursor: pointer; outline: none; transition: border-color 0.2s;
        }
        .projects-mode-select:focus { border-color: var(--gold); }

        /* Projects grid */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 14px;
        }

        /* Empty state */
        .projects-empty {
            grid-column: 1 / -1; text-align: center;
            padding: 80px 20px; color: var(--text-tertiary);
            background: var(--bg-card); border-radius: var(--radius-lg);
            border: 1px dashed var(--border);
        }
        .projects-empty h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); }
        .projects-empty p { font-size: 13px; color: var(--text-tertiary); max-width: 300px; margin: 0 auto; }
        .projects-empty-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--bg-secondary); display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }

        /* ═══════════════════════════════════════════════
           Settings — Provider Cards
           ═══════════════════════════════════════════════ */
        .provider-card {
            border: 2px solid var(--border); background: var(--bg-card);
            border-radius: 10px; padding: 14px 10px;
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            transition: all 0.25s var(--ease); cursor: pointer;
        }
        .provider-card:hover { border-color: var(--border-hover); background: var(--bg-card-hover); transform: translateY(-2px); }
        .provider-card.selected {
            border-color: var(--gold); background: rgba(59, 130, 246, 0.08);
            box-shadow: 0 0 16px var(--gold-glow);
        }
        .provider-card-emoji { font-size: 22px; line-height: 1; }
        .provider-card-name { font-size: 13px; font-weight: 700; color: var(--text-primary); }
        .provider-card-model { font-size: 11px; color: var(--text-tertiary); }
        .provider-card-badge {
            font-size: 9px; font-weight: 800; letter-spacing: 0.5px;
            padding: 2px 8px; border-radius: 4px;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .upload-grid { grid-template-columns: 1fr; }
            .visuals-grid { grid-template-columns: 1fr; }
            .metrics-grid { grid-template-columns: 1fr; }
            .solution-pros-cons { grid-template-columns: 1fr; }
            .btn-analyze { padding: 12px 24px; font-size: 14px; }
            .btn-ai { padding: 10px 20px; font-size: 13px; }
            .ai-upgrade-banner { flex-direction: column; text-align: center; gap: 12px; }
            .sidebar { display: none; }
            .right-sidebar { width: 100%; right: -100%; }
            .main-container { padding: 24px 16px; }
            .projects-grid { grid-template-columns: 1fr; }
            .projects-search-bar { flex-direction: column; }
            .projects-filter-group { width: 100%; }
            #projects-stats { grid-template-columns: repeat(2, 1fr); }
        }

        .ai-upgrade-banner {
            margin-top: 20px; padding: 14px 16px;
            background: var(--bg-card); border: 1px dashed rgba(59, 130, 246, 0.3);
            border-radius: var(--radius-md);
            display: flex; justify-content: space-between; align-items: center; text-align: left;
        }

        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* ── Text Utilities ── */
        .text-gold { color: var(--gold) !important; }
        .text-secondary { color: var(--text-secondary) !important; }

    </style>
</head>
<body>

    <header>
        <a href="#" onclick="window.location.reload(); return false;" class="logo">
            <div class="logo-icon">MV</div>
            <div class="logo-text">MercuryVision</div>
        </a>
        
        <div class="usage-bar">
            <div class="usage-text"><span data-i18n="usage_limits">Usage Limits:</span> <span id="usage-count">3</span>/15,000 credits</div>
            <div class="progress-wrapper">
                <div class="progress-fill" style="width: 2%;"></div>
            </div>
        </div>

        <div class="header-actions">
            <button class="profile-btn" id="profile-trigger">
                <div class="avatar" id="user-avatar">U</div>
                <div class="profile-info">
                    <span class="profile-name" id="user-name">Guest</span>
                    <span class="profile-plan" id="user-plan">Free Plan</span>
                </div>
                <i data-lucide="chevron-down" width="16" height="16"></i>
            </button>
            
            <div class="dropdown-menu" id="profile-dropdown">
                <div class="theme-segmented">
                    <button class="theme-btn" data-theme-val="light"><i data-lucide="sun" width="16" height="16"></i></button>
                    <button class="theme-btn active" data-theme-val="dark"><i data-lucide="moon" width="16" height="16"></i></button>
                    <button class="theme-btn" data-theme-val="system"><i data-lucide="monitor" width="16" height="16"></i></button>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <div class="dropdown-item no-hover" style="flex-direction: column; align-items: flex-start; gap: 8px; cursor: default;">
                    <div style="display:flex; align-items:center; gap:8px;"><i data-lucide="globe" width="16" height="16"></i> <span data-i18n="language">Language</span></div>
                    <div class="lang-segmented" style="display:flex; width:100%; gap:4px; background: var(--bg-secondary); padding: 4px; border-radius: 8px; border: 1px solid var(--border);">
                        <button class="lang-btn" data-lang="en">EN</button>
                        <button class="lang-btn" data-lang="ru">RU</button>
                        <button class="lang-btn" data-lang="kz">KZ</button>
                    </div>
                </div>
                
                <div class="dropdown-divider"></div>

                <button class="dropdown-item" id="settings-nav-btn" onclick="openSettingsModal()">
                    <i data-lucide="settings" width="16" height="16"></i> <span data-i18n="settings">Settings</span>
                </button>
                
                <a href="#upgrade" class="dropdown-item btn-upgrade-inline">
                    <span style="display:flex;align-items:center;gap:10px;"><i data-lucide="zap" width="16" height="16"></i> <span data-i18n="upgrade_plan">Upgrade Plan</span></span>
                </a>
                
                <button class="dropdown-item text-danger" id="btn-logout">
                    <i data-lucide="log-out" width="16" height="16"></i> <span data-i18n="logout">Log out</span>
                </button>
            </div>
        </div>
    </header>

    <div class="app-layout">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <button class="nav-item active" data-target="view-dashboard">
                    <i data-lucide="layout-dashboard" width="20" height="20"></i>
                    <span data-i18n="nav_dashboard">Dashboard</span>
                </button>
                <button class="nav-item" data-target="view-projects">
                    <i data-lucide="folder-open" width="20" height="20"></i>
                    <span data-i18n="nav_projects">Projects</span>
                </button>
            </nav>
            <div class="sidebar-history">
                <div class="sidebar-heading" data-i18n="recent_analyses">Recent Analyses</div>
                <div id="sidebar-projects-list" style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- Populated via JS -->
                </div>
            </div>
        </aside>

        <div class="app-content">
            <div id="view-dashboard" class="view-section active">
                <div class="main-container">
        
        <div id="input-state">
            <h2 class="section-title"><i data-lucide="layers" width="20" height="20"></i> <span data-i18n="select_mode">Select Analysis Mode</span></h2>
            <div class="modes-grid">
                <div class="mode-card active" data-mode="compare" data-plan="Free">
                    <div class="mode-icon"><i data-lucide="git-compare"></i></div>
                    <div class="mode-info">
                        <div class="mode-title" data-i18n="compare">AI Compare</div>
                        <div class="mode-desc" data-i18n="compare_desc">General change detection.</div>
                    </div>
                </div>
                <div class="mode-card" data-mode="water" data-plan="Free">
                    <div class="mode-icon"><i data-lucide="droplet"></i></div>
                    <div class="mode-info">
                        <div class="mode-title" data-i18n="water">Water Dynamics</div>
                        <div class="mode-desc" data-i18n="water_desc">Track hydrological changes.</div>
                    </div>
                </div>
                <div class="mode-card" data-mode="forest" data-plan="Free">
                    <div class="mode-icon"><i data-lucide="trees"></i></div>
                    <div class="mode-info">
                        <div class="mode-title" data-i18n="forest">Forest Analytics</div>
                        <div class="mode-desc" data-i18n="forest_desc">Monitor deforestation.</div>
                    </div>
                </div>
                <div class="mode-card locked" data-mode="agriculture" data-plan="Lite">
                    <div class="locked-badge"><i data-lucide="lock" width="10" height="10"></i> Lite+</div>
                    <div class="mode-icon"><i data-lucide="leaf"></i></div>
                    <div class="mode-info">
                        <div class="mode-title" data-i18n="agriculture">NDVI / Agriculture</div>
                        <div class="mode-desc" data-i18n="agriculture_desc">Vegetation health assessment.</div>
                    </div>
                </div>
                <div class="mode-card locked" data-mode="urban" data-plan="Standard">
                    <div class="locked-badge"><i data-lucide="lock" width="10" height="10"></i> Standard+</div>
                    <div class="mode-icon"><i data-lucide="building-2"></i></div>
                    <div class="mode-info">
                        <div class="mode-title" data-i18n="urban">Urban Intelligence</div>
                        <div class="mode-desc" data-i18n="urban_desc">Urbanization tracking.</div>
                    </div>
                </div>
            </div>
            
            <div id="mode-error"><i data-lucide="info" width="16" height="16" style="display:inline-block; vertical-align:middle; margin-right:4px;"></i> <span id="mode-error-text" data-i18n="upgrade_mode">Upgrade to unlock this mode.</span></div>
            <div class="model-selector">
                <label for="changeformer-model">ChangeFormer model</label>
                <select id="changeformer-model" class="model-select">
                    <option value="base">Base (balanced)</option>
                    <option value="fast">Fast (quick preview)</option>
                    <option value="large">Large (high sensitivity)</option>
                </select>
            </div>

            <h2 class="section-title" style="margin-top: 24px;"><i data-lucide="image" width="20" height="20"></i> <span data-i18n="upload_imagery">Upload Imagery</span></h2>
            <div class="upload-grid">
                <!-- Dropzone Before -->
                <div class="dropzone" id="dz-before">
                    <input type="file" id="file-before" accept="image/jpeg, image/png, image/webp" class="dz-input">
                    <div class="dropzone-loader"><i data-lucide="loader-2" class="animate-spin" width="32" height="32"></i></div>
                    <div class="dropzone-content">
                        <i data-lucide="upload-cloud" width="48" height="48" class="dropzone-icon"></i>
                        <div class="dropzone-title" data-i18n="upload_before">Before Image</div>
                        <div class="dropzone-hint" data-i18n="dropzone_hint">Drag & drop or click to upload<br>JPG, PNG, WEBP (Max 10MB)</div>
                    </div>
                    <img id="preview-before" class="preview-img" alt="Before preview">
                    <button class="remove-img-btn" id="remove-before"><i data-lucide="x" width="16" height="16"></i></button>
                </div>
                <!-- Dropzone After -->
                <div class="dropzone" id="dz-after">
                    <input type="file" id="file-after" accept="image/jpeg, image/png, image/webp" class="dz-input">
                    <div class="dropzone-loader"><i data-lucide="loader-2" class="animate-spin" width="32" height="32"></i></div>
                    <div class="dropzone-content">
                        <i data-lucide="upload-cloud" width="48" height="48" class="dropzone-icon"></i>
                        <div class="dropzone-title" data-i18n="upload_after">After Image</div>
                        <div class="dropzone-hint" data-i18n="dropzone_hint">Drag & drop or click to upload<br>JPG, PNG, WEBP (Max 10MB)</div>
                    </div>
                    <img id="preview-after" class="preview-img" alt="After preview">
                    <button class="remove-img-btn" id="remove-after"><i data-lucide="x" width="16" height="16"></i></button>
                </div>
            </div>

            <div class="action-bar">
                <button class="btn-analyze" id="btn-run-analyze">
                    <span data-i18n="run_analyze">Run Analyze</span> <span class="cost-badge">-25 Credits</span>
                    <i data-lucide="play" width="18" height="18" id="analyze-icon"></i>
                </button>
                <div id="analyze-error" class="inline-error" data-i18n="analyze_error_msg">Please select both images to proceed.</div>
            </div>
        </div>

        <!-- Results Area (Hidden initially) -->
        <div id="results-area">
            <div class="results-header">
                <h2 class="section-title" style="margin:0;"><i data-lucide="bar-chart-2" width="20" height="20"></i> <span data-i18n="results">Analysis Results</span></h2>
                <div class="export-actions">
                    <button class="btn-outline" onclick="exportData('csv')"><i data-lucide="file-spreadsheet" width="16" height="16"></i> <span data-i18n="export_csv">Export CSV</span></button>
                    <button class="btn-outline" onclick="exportData('pdf')"><i data-lucide="file-text" width="16" height="16"></i> <span data-i18n="export_pdf">Export PDF (Pro)</span></button>
                </div>
            </div>

            <div class="visuals-container">
                <div class="visuals-slider-wrap">
                    <div style="font-size:14px; font-weight:600; color:var(--text-secondary); margin-bottom:12px; display:flex; align-items:center; gap:8px;"><i data-lucide="split-square-horizontal" width="16" height="16"></i> Interactive Comparison</div>
                    <div class="image-slider" id="interactive-slider">
                        <img id="res-img-before" src="" alt="Before">
                        <div class="slider-after-container" id="slider-clipper">
                            <img id="res-img-after" src="" alt="After">
                        </div>
                        <div class="slider-handle" id="slider-handle">
                            <i data-lucide="chevrons-left-right" width="16" height="16"></i>
                        </div>
                        <div class="slider-tag tag-before" data-i18n="before">Before</div>
                        <div class="slider-tag tag-after" data-i18n="after">After</div>
                    </div>
                </div>
                
                <div class="visuals-output-wrap">
                    <div style="font-size:14px; font-weight:600; color:var(--text-secondary); margin-bottom:12px; display:flex; align-items:center; gap:8px; opacity:0;">Output Spacer</div>
                    <div class="visual-card" style="border-color: var(--gold); box-shadow: 0 0 20px var(--gold-glow);">
                        <div class="visual-header"><i data-lucide="layers"></i> <span data-i18n="output">ChangeFormer Output</span></div>
                        <div class="visual-img-container"><img id="res-img-output" src="" alt="Output"></div>
                    </div>
                </div>
            </div>

            <div class="metrics-grid" id="metrics-container">
                <!-- Populated via JS -->
            </div>

            <!-- AI Insights Section (inline, below metrics) -->
            <div class="ai-section" id="ai-inline-section" style="margin-bottom: 32px;">
                <div class="ai-prompt">
                    <h3 class="ai-title"><i data-lucide="sparkles" width="24" height="24"></i> <span data-i18n="ai_title">AI Assistant</span></h3>
                    <p class="ai-desc" data-i18n="ai_generate_hint">Generate an AI-powered analytical report from your results.</p>
                    <button class="btn-ai" id="btn-generate-ai-inline" onclick="window.openAIAndGenerate()">
                        <i data-lucide="sparkles" width="18" height="18"></i>
                        <span data-i18n="ai_btn">Generate Report</span>
                        <span class="cost-badge">-15 Credits</span>
                    </button>
                </div>
            </div>

                </div>
            </div>

            <!-- Projects View -->
            <div id="view-projects" class="view-section">
                <div class="main-container">
                    <!-- Projects Header -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
                        <div>
                            <h2 class="projects-header-title"><i data-lucide="folder-open" width="22" height="22"></i> <span data-i18n="projects_title">Your Projects</span></h2>
                            <p class="projects-subtitle">All analysis history and AI generated reports in one place.</p>
                        </div>
                        <button class="btn-analyze" id="btn-new-analysis" style="padding:10px 20px; font-size:13px;" onclick="document.querySelector('.nav-item[data-target=view-dashboard]').click()">
                            <i data-lucide="plus" width="16" height="16"></i> New Analysis
                        </button>
                    </div>

                    <!-- Stats Row -->
                    <div id="projects-stats" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:12px; margin-bottom:24px;">
                        <div class="projects-stat-card">
                            <div class="projects-stat-value" style="color:var(--gold);" id="stat-total">—</div>
                            <div class="projects-stat-label">Total Projects</div>
                        </div>
                        <div class="projects-stat-card">
                            <div class="projects-stat-value" style="color:var(--success);" id="stat-thisweek">—</div>
                            <div class="projects-stat-label">This Week</div>
                        </div>
                        <div class="projects-stat-card">
                            <div class="projects-stat-value" style="color:var(--warning);" id="stat-mode">—</div>
                            <div class="projects-stat-label">Top Mode</div>
                        </div>
                        <div class="projects-stat-card">
                            <div class="projects-stat-value" style="color:var(--gold-light);" id="stat-ai">—</div>
                            <div class="projects-stat-label">AI Reports</div>
                        </div>
                    </div>

                    <!-- Search + Filter -->
                    <div class="projects-search-bar">
                        <div class="projects-search-wrap">
                            <i data-lucide="search" width="16" height="16"></i>
                            <input type="text" id="project-search" class="projects-search-input" placeholder="Search projects..." oninput="filterProjects()">
                        </div>
                        <div class="projects-filter-group">
                            <button class="projects-filter-btn active" id="filter-all" onclick="setProjectFilter('all', this)">All</button>
                            <button class="projects-filter-btn" id="filter-today" onclick="setProjectFilter('today', this)">Today</button>
                            <button class="projects-filter-btn" id="filter-week" onclick="setProjectFilter('week', this)">This Week</button>
                        </div>
                        <select id="filter-mode" class="projects-mode-select" onchange="filterProjects()">
                            <option value="">All Modes</option>
                            <option value="compare">AI Compare</option>
                            <option value="water">Water Dynamics</option>
                            <option value="forest">Forest Analytics</option>
                            <option value="agriculture">NDVI / Agriculture</option>
                            <option value="urban">Urban Intelligence</option>
                        </select>
                    </div>

                    <!-- Projects Grid -->
                    <div id="projects-list" class="projects-grid">
                        <div class="projects-empty">
                            <i data-lucide="loader-2" width="36" height="36" class="animate-spin" style="opacity:0.4; margin-bottom:16px;"></i>
                            <p style="font-size: 14px; font-weight:500;">Loading projects...</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <button class="floating-ai-btn" id="floating-ai-toggle" onclick="toggleAiSidebar()">
        <i data-lucide="sparkles" width="24" height="24"></i>
    </button>
    
    <!-- AI Sliding Right Sidebar -->
    <aside class="right-sidebar" id="ai-sidebar">
        <button class="btn-close-sidebar" onclick="toggleAiSidebar()"><i data-lucide="x" width="20" height="20"></i></button>
        <div class="ai-header" style="display:flex; flex-direction:column; align-items:flex-start; margin-bottom: 20px; margin-top: 6px;">
            <h3 class="ai-title" style="margin-bottom:6px; justify-content:flex-start; font-size:17px; letter-spacing:-0.02em;"><i data-lucide="sparkles" class="text-gold"></i> <span data-i18n="ai_title">AI Assistant</span></h3>
            <p class="ai-desc" style="margin:0; font-size: 13px; color:var(--text-secondary);" data-i18n="ai_desc">Strategic analyst companion.</p>
        </div>

        <!-- API Key Info Banner -->
        <div class="api-key-info" id="api-key-banner">
            <h4><i data-lucide="key" width="14" height="14"></i> <span data-i18n="api_key_setup">API Key Setup</span></h4>
            <p data-i18n="api_key_info">For unlimited AI generations, add your own API key in <a href="#" onclick="openSettingsModal(); return false;" data-i18n="settings">Settings</a>. Supports OpenAI (GPT-4o) and Google Gemini.</p>
        </div>

        <div id="ai-entry" style="text-align:center; padding: 32px 16px; border: 1px dashed var(--border); border-radius: 12px; background: var(--bg-card);">
            <div style="width:56px; height:56px; border-radius:50%; background: var(--accent-gradient); display:flex; align-items:center; justify-content:center; margin: 0 auto 16px;">
                <i data-lucide="bot" width="28" height="28" style="color:#fff;"></i>
            </div>
            <h3 style="font-size:15px; margin-bottom:6px; font-weight:600;" data-i18n="data_context_ready">Data Context Ready</h3>
            <p style="font-size:12px; color:var(--text-tertiary); margin-bottom:16px;" data-i18n="ai_generate_hint">Generate an AI-powered analytical report from your results.</p>
            <button class="btn-ai" id="btn-generate-ai" style="padding: 11px 22px; font-size:14px;">
                <i data-lucide="sparkles" width="16" height="16"></i>
                <span data-i18n="ai_btn">Generate Report</span> <span class="cost-badge">-15 Credits</span>
            </button>
        </div>
        
        <div id="ai-output" style="display:none; text-align:left; margin-bottom:20px;">
            <!-- Populated via JS (Initial Report) -->
        </div>
        
        <div id="chat-history" style="display:none; flex: 1; overflow-y: auto; flex-direction:column; gap: 12px; margin-bottom: 14px; padding-right: 6px; text-align:left;">
            <!-- Messages go here -->
        </div>

        <div id="chat-input-container" style="display:none; gap: 8px; margin-top: auto; padding-top: 14px; border-top: 1px solid var(--border);">
            <input type="text" id="chat-input" class="form-input" style="margin:0; background: var(--bg-card);" placeholder="Ask me..." onkeypress="if(event.key === 'Enter') sendChatMessage()">
            <button class="btn-ai" style="width: 42px; padding: 0; border-radius:10px; flex-shrink: 0; justify-content:center;" id="btn-send-chat" onclick="sendChatMessage()">
                <i data-lucide="send" width="16" height="16"></i>
            </button>
        </div>
        
        <div id="ai-error" class="inline-error" style="position:static; margin-top:14px;"></div>
    </aside>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeCheckout()"><i data-lucide="x" width="20" height="20"></i></button>
            <div id="checkout-step-1">
                <h2 style="font-size:24px; font-weight:bold; margin-bottom:8px;">Upgrade Plan</h2>
                <p style="color:var(--text-secondary);font-size:14px;">Select the enterprise plan that suits your needs.</p>
                <div class="plan-options">
                    <div class="plan-card-opt" onclick="selectPlan('Lite')">
                        <div><strong style="font-size:16px;">Lite</strong><br><span style="font-size:13px;color:var(--text-secondary);">Water & Forest Modes</span></div>
                        <div style="font-weight:700;">$5.99 / mo</div>
                    </div>
                    <div class="plan-card-opt" onclick="selectPlan('Standard')">
                        <div><strong style="font-size:16px;">Standard <span style="font-size:10px;background:var(--gold);color:#fff;padding:2px 6px;border-radius:10px;margin-left:4px;">POPULAR</span></strong><br><span style="font-size:13px;color:var(--text-secondary);">All Models + Export</span></div>
                        <div style="font-weight:700;">$24.99 / mo</div>
                    </div>
                    <div class="plan-card-opt" onclick="selectPlan('Pro')">
                        <div><strong style="font-size:16px;">Pro</strong><br><span style="font-size:13px;color:var(--text-secondary);">Full Platform + API</span></div>
                        <div style="font-weight:700;">$74.99 / mo</div>
                    </div>
                </div>
            </div>
            
            <div id="checkout-step-2" style="display:none;">
                <h2 style="font-size:20px; font-weight:bold; margin-bottom:8px;">Payment Details</h2>
                <p style="color:var(--text-secondary);font-size:14px;margin-bottom:24px;">Complete your purchase for the <span id="checkout-plan-name" style="color:var(--text-primary);font-weight:700;"></span> plan.</p>
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:600;color:var(--text-secondary);">Card Information</label>
                    <input type="text" class="form-input" placeholder="4242 4242 4242 4242" value="4242 4242 4242 4242">
                </div>
                <div style="display:flex;gap:12px;margin-bottom:32px;">
                    <input type="text" class="form-input" placeholder="MM/YY" value="12/26">
                    <input type="text" class="form-input" placeholder="CVC" value="123">
                </div>
                <button class="btn-primary" id="btn-pay-now" onclick="processPayment()">
                    <i data-lucide="lock" width="18" height="18"></i> Pay Securely
                </button>
                <button class="btn-outline" style="width:100%;margin-top:12px;text-align:center;justify-content:center;border:none;" onclick="document.getElementById('checkout-step-2').style.display='none';document.getElementById('checkout-step-1').style.display='block';">Back</button>
            </div>
            
            <div id="checkout-step-3" style="display:none; text-align:center; padding: 24px 0;">
                <i data-lucide="check-circle" width="64" height="64" style="color:var(--success);margin-bottom:16px; margin-left:auto; margin-right:auto; display:block;"></i>
                <h2 style="font-size:24px; font-weight:bold; margin-bottom:8px;">Payment Successful!</h2>
                <p style="color:var(--text-secondary);font-size:14px;margin-bottom:32px;">Your plan has been upgraded. You now have access to premium features.</p>
                <button class="btn-outline" style="padding:12px 32px;" onclick="closeCheckout(true)">Continue to Dashboard</button>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settings-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeSettingsModal()"><i data-lucide="x" width="20" height="20"></i></button>
            <h2 style="font-size:18px; font-weight:700; margin-bottom:6px; display:flex; align-items:center; gap:8px;"><i data-lucide="settings" width="20" height="20"></i> <span data-i18n="settings">Platform Settings</span></h2>
            <p style="color:var(--text-secondary);font-size:13px;margin-bottom:20px;" data-i18n="settings_desc">Configure your AI models and custom API keys here.</p>
            
            <div style="margin-bottom: 20px; padding: 16px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; text-align:left;">
                <label style="font-size:12px; font-weight:700; color:var(--text-secondary); margin-bottom:10px; display:block; text-transform:uppercase; letter-spacing:0.5px;">AI Provider</label>
                <!-- Visual provider cards instead of select -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;" id="provider-cards">
                    <label style="cursor:pointer;">
                        <input type="radio" name="ai-provider" value="openai" id="radio-openai" style="display:none;" checked>
                        <div class="provider-card selected" data-provider="openai" onclick="selectProvider('openai')">
                            <span class="provider-card-emoji">🤖</span>
                            <span class="provider-card-name">OpenAI</span>
                            <span class="provider-card-model">GPT-4o</span>
                            <span class="provider-card-badge" style="color:var(--success); background:rgba(34,197,94,0.12);">RECOMMENDED</span>
                        </div>
                    </label>
                    <label style="cursor:pointer;">
                        <input type="radio" name="ai-provider" value="gemini" id="radio-gemini" style="display:none;">
                        <div class="provider-card" data-provider="gemini" onclick="selectProvider('gemini')">
                            <span class="provider-card-emoji">✨</span>
                            <span class="provider-card-name">Gemini</span>
                            <span class="provider-card-model">2.0 Flash</span>
                            <span class="provider-card-badge" style="color:var(--gold); background:rgba(59,130,246,0.12);">FAST</span>
                        </div>
                    </label>
                </div>
                <!-- hidden select for backward compat -->
                <select id="ai-model-provider" style="display:none;">
                    <option value="openai">OpenAI (GPT-4o)</option>
                    <option value="gemini">Google Gemini 2.0 Flash</option>
                </select>
                <input type="password" id="custom-api-key" class="form-input" style="margin:0;" placeholder="API Key (comma-separated for key rotation)" value="">
                <button class="btn-primary" style="width:100%; padding:11px; margin-top:10px;" onclick="saveApiKey()">
                    <i data-lucide="save" width="16" height="16"></i> <span data-i18n="save_api_settings">Save API Settings</span>
                </button>
            </div>

            <!-- API Key Instructions -->
            <div style="padding: 14px 16px; background: rgba(59, 130, 246, 0.04); border: 1px solid rgba(59, 130, 246, 0.12); border-radius: 10px; text-align:left;">
                <p style="font-size:12px; font-weight:700; color:var(--text-primary); margin-bottom:8px; display:flex; align-items:center; gap:6px;"><i data-lucide="info" width="14" height="14" style="color:var(--gold);"></i> <span data-i18n="how_to_get_key">How to get an API Key</span></p>
                <div style="font-size:12px; color:var(--text-tertiary); line-height:1.6;">
                    <p style="margin-bottom:8px;"><strong style="color:var(--text-secondary);">OpenAI (GPT-4o):</strong><br>
                    1. <span data-i18n="openai_step1">Go to</span> <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener" style="color:var(--gold);">platform.openai.com/api-keys</a><br>
                    2. <span data-i18n="openai_step2">Create a new secret key and paste it above.</span></p>
                    <p style="margin:0;"><strong style="color:var(--text-secondary);">Google Gemini:</strong><br>
                    1. <span data-i18n="gemini_step1">Go to</span> <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener" style="color:var(--gold);">aistudio.google.com/apikey</a><br>
                    2. <span data-i18n="gemini_step2">Create an API key and paste it above.</span></p>
                </div>
                <p style="font-size:11px; color:var(--text-tertiary); margin-top:10px; border-top:1px solid var(--border); padding-top:8px;" data-i18n="api_key_storage_note">Keys are stored securely in your browser's local storage only. Leave blank to use shared server credits.</p>
            </div>
        </div>
    </div>

    <!-- Firebase API Config -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
        import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCeMGFoDpdc_FZ7WpVgJVgATHYQSEKSQMs",
            authDomain: "mercuryvision26.firebaseapp.com",
            databaseURL: "https://mercuryvision26-default-rtdb.europe-west1.firebasedatabase.app",
            projectId: "mercuryvision26",
            storageBucket: "mercuryvision26.firebasestorage.app",
            messagingSenderId: "688481376868",
            appId: "1:688481376868:web:d58ac7617adc0e0add5d71",
            measurementId: "G-G82P5WHTGK"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        
        window.parseApiResponse = async function(response) {
            const text = await response.text();
            let data = null;
            try {
                data = text ? JSON.parse(text) : null;
            } catch (e) {
                const preview = text.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 160);
                throw new Error(preview || `HTTP ${response.status}`);
            }
            if (!response.ok) {
                throw new Error((data && (data.error || data.message)) || `HTTP ${response.status}`);
            }
            return data || {};
        };
        
        let currentUser = null;
        let userPlan = 'Free'; // Default. In real app, fetch from custom claims or DB.

        onAuthStateChanged(auth, async (user) => {
            if (user) {
                currentUser = user;
                document.getElementById('user-name').textContent = user.displayName || user.email.split('@')[0];
                document.getElementById('user-avatar').textContent = (user.displayName || user.email).charAt(0).toUpperCase();

                try {
                    // Force token refresh to avoid expired token; establish PHP session first
                    const idToken = await user.getIdToken(true);
                    let sessionRes = await fetch('/api/session.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify({ action: 'login', idToken })
                    });

                    // Fallback: dev_login for localhost when Firebase token verification fails
                    if (!sessionRes.ok && (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
                        sessionRes = await fetch('/api/session.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            credentials: 'include',
                            body: JSON.stringify({ action: 'dev_login', email: user.email || 'dev@example.com', username: user.displayName || '' })
                        });
                    }
                    if (!sessionRes.ok) {
                        console.warn('Could not establish backend session.');
                        return;
                    }

                    // Session is now valid — safely fetch user data without risk of 401
                    const response = await fetch('/api/user_data.php', { credentials: 'include' });
                    if (response.ok) {
                        const data = await window.parseApiResponse(response);
                        if (data.plan) {
                            window.userPlan = data.plan;
                            document.getElementById('user-plan').textContent = data.plan + ' Plan';
                            
                            const settingsBtn = document.getElementById('settings-nav-btn');
                            if (settingsBtn) {
                                settingsBtn.style.display = (window.userPlan.toLowerCase() === 'free') ? 'none' : 'flex';
                            }
                            
                            syncUploadLimitByPlan(data.plan, data?.upload?.limit_mb ?? null);
                            applyUploadLimitHint();
                        }
                        if (data.usage) {
                            updateUsageUI(data.usage);
                        }

                        // Session confirmed — unlock all API calls
                        window._authReady = true;
                        if (typeof window.loadProjects === 'function') window.loadProjects();
                    }
                } catch(error) {
                    console.warn('Auth setup note:', error.message);
                }
            } else {
                window.location.href = '/auth';
            }
        });
        document.getElementById('btn-logout').addEventListener('click', async () => {
            try {
                await fetch('/api/session.php?action=logout', { method: 'POST' });
            } catch (e) { console.error('Error logging out backend session'); }
            signOut(auth);
        });

        // Setup App Logic globally so it can be accessed
        window.userPlan = window.userPlan || 'Free';
    </script>

    <script>
        window.SERVER_UPLOAD_BYTES = <?= (int)$serverLimit ?>;
    </script>
    <script>
        const SERVER_UPLOAD_BYTES = window.SERVER_UPLOAD_BYTES || +(10 * 1024 * 1024);
        let currentUploadBytes = SERVER_UPLOAD_BYTES;
        let currentUploadMb = Math.max(1, Math.floor(currentUploadBytes / (1024 * 1024)));

        // State & DOM
        let selectedMode = 'compare';
        let selectedModel = 'base';
        let fileBeforeObj = null;
        let fileAfterObj = null;
        let currentResults = null;

        lucide.createIcons();

        // UI Interactions
        const profileTrigger = document.getElementById('profile-trigger');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target) && !profileTrigger.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });

        const themeBtns = document.querySelectorAll('.theme-btn');
        themeBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                themeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const theme = btn.dataset.themeVal;
                let isDark = true;
                if(theme === 'light') isDark = false;
                else if(theme === 'system') isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            });
        });

        // Translations
        const translations = {
            en: {
                compare: 'AI Compare', water: 'Water Dynamics', forest: 'Forest Analytics', agriculture: 'NDVI / Agriculture', urban: 'Urban Intelligence',
                upgrade_mode: 'Upgrade to unlock this mode.', run_analyze: 'Run Analyze', upload_before: 'Before Image', upload_after: 'After Image',
                results: 'Analysis Results', before: 'Before', after: 'After', output: 'ChangeFormer Output',
                ai_title: 'AI Assistant', ai_desc: 'Strategic analyst companion.', ai_btn: 'Generate Report',
                export_csv: 'Export CSV', export_pdf: 'Export PDF (Pro)',
                language: 'Language', upgrade_plan: 'Upgrade Plan', logout: 'Log out', settings: 'Settings', processing: 'Processing...', generating: 'Generating...',
                dropzone_hint: 'Drag & drop or click to upload<br>JPG, PNG, WEBP (Max 10MB)', usage_limits: 'Usage Limits:',
                nav_dashboard: 'Dashboard', nav_projects: 'Projects', recent_analyses: 'Recent Analyses', select_mode: 'Select Analysis Mode',
                upload_imagery: 'Upload Imagery', projects_title: 'Your Projects', analyze_error_msg: 'Please select both images to proceed.',
                // New keys for redesigned UI
                api_key_setup: 'API Key Setup',
                api_key_info: 'For unlimited AI generations, add your own API key in <a href="#" onclick="openSettingsModal(); return false;">Settings</a>. Supports OpenAI (GPT-4o) and Google Gemini.',
                data_context_ready: 'Data Context Ready',
                ai_generate_hint: 'Generate an AI-powered analytical report from your results.',
                settings_desc: 'Configure your AI models and custom API keys here.',
                save_api_settings: 'Save API Settings',
                how_to_get_key: 'How to get an API Key',
                openai_step1: 'Go to', openai_step2: 'Create a new secret key and paste it above.',
                gemini_step1: 'Go to', gemini_step2: 'Create an API key and paste it above.',
                api_key_storage_note: "Keys are stored securely in your browser's local storage only. Leave blank to use shared server credits.",
                want_more: 'Want more detailed solutions?', upgrade_hint: 'Upgrade to Pro to unlock comprehensive solution plans.',
                primary_solution: 'Primary Solution',
                executive_summary: 'Executive Summary',
                // Metric labels
                'Changed Area': 'Changed Area', 'Change Percentage': 'Change Percentage', 'Water Body Area': 'Water Body Area', 'Forest Cover': 'Forest Cover',
                'Vegetation Index': 'Vegetation Index', 'Urban Area': 'Urban Area', 'Confidence Score': 'Confidence Score',
                'Area': 'Area', 'Shift': 'Shift', 'Luminance': 'Luminance', 'Anomalies': 'Anomalies', 'Integrity': 'Integrity'
            },
            ru: {
                compare: 'ИИ Сравнение', water: 'Водная Динамика', forest: 'Лесная Аналитика', agriculture: 'NDVI Анализ', urban: 'Городской Анализ',
                upgrade_mode: 'Обновите тариф для этого режима.', run_analyze: 'Запустить анализ', upload_before: 'До (Снимок)', upload_after: 'После (Снимок)',
                results: 'Результаты анализа', before: 'До', after: 'После', output: 'Результат ChangeFormer',
                ai_title: 'ИИ Ассистент', ai_desc: 'Стратегический аналитический помощник.', ai_btn: 'Создать отчёт',
                export_csv: 'Экспорт CSV', export_pdf: 'Экспорт PDF (Pro)',
                language: 'Язык', upgrade_plan: 'Обновить тариф', logout: 'Выйти', settings: 'Настройки', processing: 'Обработка...', generating: 'Генерация...',
                dropzone_hint: 'Перетащите или нажмите для загрузки<br>JPG, PNG, WEBP (Макс 10МБ)', usage_limits: 'Лимит использования:',
                nav_dashboard: 'Панель управления', nav_projects: 'Проекты', recent_analyses: 'Недавние анализы', select_mode: 'Выберите режим анализа',
                upload_imagery: 'Загрузить снимки', projects_title: 'Ваши проекты', analyze_error_msg: 'Выберите оба изображения для продолжения.',
                // New keys for redesigned UI
                api_key_setup: 'Настройка API ключа',
                api_key_info: 'Для безлимитных ИИ генераций добавьте свой API ключ в <a href="#" onclick="openSettingsModal(); return false;">Настройках</a>. Поддерживаются OpenAI (GPT-4o) и Google Gemini.',
                data_context_ready: 'Данные готовы',
                ai_generate_hint: 'Создайте аналитический отчёт на основе результатов анализа с помощью ИИ.',
                settings_desc: 'Настройте модели ИИ и пользовательские API ключи.',
                save_api_settings: 'Сохранить настройки API',
                how_to_get_key: 'Как получить API ключ',
                openai_step1: 'Перейдите на', openai_step2: 'Создайте новый секретный ключ и вставьте его выше.',
                gemini_step1: 'Перейдите на', gemini_step2: 'Создайте API ключ и вставьте его выше.',
                api_key_storage_note: 'Ключи хранятся безопасно только в локальном хранилище вашего браузера. Оставьте пустым для использования общих серверных кредитов.',
                want_more: 'Хотите более детальные решения?', upgrade_hint: 'Обновите до Pro для доступа к полным планам решений.',
                primary_solution: 'Основное решение',
                executive_summary: 'Краткий обзор',
                // Metric labels
                'Changed Area': 'Измененная площадь', 'Change Percentage': 'Процент изменений', 'Water Body Area': 'Площадь водоемов', 'Forest Cover': 'Лесной покров',
                'Vegetation Index': 'Индекс растительности', 'Urban Area': 'Городская застройка', 'Confidence Score': 'Уверенность ИИ',
                'Area': 'Площадь', 'Shift': 'Сдвиг', 'Luminance': 'Яркость', 'Anomalies': 'Аномалии', 'Integrity': 'Целостность'
            },
            kz: {
                compare: 'AI Салыстыру', water: 'Су Динамикасы', forest: 'Орман Аналитикасы', agriculture: 'NDVI Талдау', urban: 'Қалалық Талдау',
                upgrade_mode: 'Бұл режимді ашу үшін тарифті жаңартыңыз.', run_analyze: 'Талдауды бастау', upload_before: 'Дейін (Сурет)', upload_after: 'Кейін (Сурет)',
                results: 'Талдау нәтижелері', before: 'Дейін', after: 'Кейін', output: 'ChangeFormer Нәтижесі',
                ai_title: 'AI Көмекші', ai_desc: 'Стратегиялық аналитикалық көмекші.', ai_btn: 'Есеп жасау',
                export_csv: 'CSV экспорттау', export_pdf: 'PDF экспорттау (Pro)',
                language: 'Тіл', upgrade_plan: 'Тарифті жаңарту', logout: 'Шығу', settings: 'Параметрлер', processing: 'Өңделуде...', generating: 'Жасалуда...',
                dropzone_hint: 'Жүктеу үшін сүйреңіз немесе басыңыз<br>JPG, PNG, WEBP (Қолдау 10МБ дейін)', usage_limits: 'Қолдану шегі:',
                nav_dashboard: 'Басқару панелі', nav_projects: 'Жобалар', recent_analyses: 'Соңғы талдаулар', select_mode: 'Талдау режимін таңдаңыз',
                upload_imagery: 'Суреттерді жүктеу', projects_title: 'Сіздің жобаларыңыз', analyze_error_msg: 'Жалғастыру үшін екі суретті де таңдаңыз.',
                // New keys for redesigned UI
                api_key_setup: 'API кілтін орнату',
                api_key_info: 'Шексіз AI генерациялары үшін өзіңіздің API кілтіңізді <a href="#" onclick="openSettingsModal(); return false;">Параметрлерге</a> қосыңыз. OpenAI (GPT-4o) және Google Gemini қолдау көрсетіледі.',
                data_context_ready: 'Деректер дайын',
                ai_generate_hint: 'Нәтижелерге негізделген AI-аналитикалық есеп жасаңыз.',
                settings_desc: 'AI модельдерін және API кілттерін теңшеңіз.',
                save_api_settings: 'API параметрлерін сақтау',
                how_to_get_key: 'API кілтін қалай алуға болады',
                openai_step1: 'Мына жерге өтіңіз', openai_step2: 'Жаңа құпия кілт жасаңыз және жоғарыға қойыңыз.',
                gemini_step1: 'Мына жерге өтіңіз', gemini_step2: 'API кілтін жасаңыз және жоғарыға қойыңыз.',
                api_key_storage_note: 'Кілттер тек браузеріңіздің жергілікті қоймасында қауіпсіз сақталады. Ортақ серверлік кредиттерді пайдалану үшін бос қалдырыңыз.',
                want_more: 'Толығырақ шешімдер қалайсыз ба?', upgrade_hint: 'Толық шешім жоспарларына қол жеткізу үшін Pro-ға жаңартыңыз.',
                primary_solution: 'Негізгі шешім',
                executive_summary: 'Қысқаша шолу',
                // Metric labels
                'Changed Area': 'Өзгерген аумақ', 'Change Percentage': 'Өзгеріс пайызы', 'Water Body Area': 'Су қоймаларының ауданы', 'Forest Cover': 'Орман жамылғысы',
                'Vegetation Index': 'Өсімдік индексі', 'Urban Area': 'Қала аумағы', 'Confidence Score': 'Сенімділік деңгейі',
                'Area': 'Аумақ', 'Shift': 'Ауысу', 'Luminance': 'Жарықтық', 'Anomalies': 'Аномалиялар', 'Integrity': 'Тұтастық'
            }
        };

        let currentLang = localStorage.getItem('lang') || 'en';
        const t = (key) => (translations[currentLang] && translations[currentLang][key]) ? translations[currentLang][key] : key;
        const planUploadLimitMb = (plan) => {
            switch ((plan || 'Free').toLowerCase()) {
                case 'lite': return 5;
                case 'standard': return 10;
                case 'pro': return 10;
                case 'enterprise': return 20;
                default: return 2;
            }
        };
        const syncUploadLimitByPlan = (plan, apiLimitMb = null) => {
            const mbByPlan = apiLimitMb && Number(apiLimitMb) > 0 ? Number(apiLimitMb) : planUploadLimitMb(plan);
            currentUploadBytes = Math.min(SERVER_UPLOAD_BYTES, Math.floor(mbByPlan * 1024 * 1024));
            currentUploadMb = Math.max(1, Math.floor(currentUploadBytes / (1024 * 1024)));
        };
        const applyUploadLimitHint = () => {
            document.querySelectorAll('.dropzone-hint').forEach((el) => {
                el.innerHTML = el.innerHTML.replace(/Max\s*\d+MB/gi, `Max ${currentUploadMb}MB`).replace(/Макс\s*\d+МБ/gi, `Макс ${currentUploadMb}МБ`);
            });
        };

        function updateLanguage(langCode) {
            currentLang = langCode;
            localStorage.setItem('lang', langCode);
            document.documentElement.lang = langCode;
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                let value = translations[langCode] && translations[langCode][key];
                if (value) el.innerHTML = value;
            });
            applyUploadLimitHint();
            // Fix: Re-translate dynamically rendered metric labels on language switch
            retranslateMetrics();
        }

        function retranslateMetrics() {
            const metricsContainer = document.getElementById('metrics-container');
            if (!metricsContainer) return;
            metricsContainer.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                const value = translations[currentLang] && translations[currentLang][key];
                if (value) el.innerHTML = value;
            });
        }
        
        // Run initial translation
        updateLanguage(currentLang);

        // Hide API key banner if user already has a key
        if (localStorage.getItem('mv_api_key')) {
            const banner = document.getElementById('api-key-banner');
            if (banner) banner.style.display = 'none';
        }

        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                updateLanguage(btn.dataset.lang);
            });
        });
        
        const activeLangBtn = document.querySelector(`.lang-btn[data-lang="${currentLang}"]`);
        if(activeLangBtn) activeLangBtn.classList.add('active');

        // Modes Selection
        const modeCards = document.querySelectorAll('.mode-card');
        const modeError = document.getElementById('mode-error');
        const modelSelect = document.getElementById('changeformer-model');
        modelSelect.addEventListener('change', (e) => {
            selectedModel = e.target.value || 'base';
        });
        
        modeCards.forEach(card => {
            card.addEventListener('click', () => {
                const planRequired = card.dataset.plan;
                // Simple mock plan check
                const planLevels = { 'Free': 0, 'Lite': 1, 'Pro': 2, 'Enterprise': 3 };
                const currentPlanLevel = planLevels[window.userPlan] || 0;
                
                if (planLevels[planRequired] > currentPlanLevel) {
                    modeError.style.display = 'block';
                    setTimeout(() => { modeError.style.display = 'none'; }, 4000);
                    return;
                }

                modeCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                selectedMode = card.dataset.mode;
            });
        });

        // Dropzones
        const dBefore = document.getElementById('dz-before');
        const dAfter = document.getElementById('dz-after');
        const fBefore = document.getElementById('file-before');
        const fAfter = document.getElementById('file-after');

        function setupDropzone(dz, fileInput, previewImg, removeBtn, fileSetter) {
            // Native file input handling is more robust for drag & drop
            dz.addEventListener('dragover', (e) => { 
                e.preventDefault(); e.stopPropagation(); dz.classList.add('dragover'); 
            });
            dz.addEventListener('dragleave', (e) => { 
                e.preventDefault(); e.stopPropagation(); dz.classList.remove('dragover'); 
            });
            dz.addEventListener('drop', (e) => {
                e.preventDefault(); e.stopPropagation(); dz.classList.remove('dragover');
            });
            
            fileInput.addEventListener('change', (e) => {
                if(e.target.files && e.target.files.length) {
                    handleFile(dz, e.target.files[0], previewImg, removeBtn, fileSetter);
                }
            });
            
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                previewImg.src = '';
                previewImg.style.display = 'none';
                removeBtn.style.display = 'none';
                fileSetter(null);
            });
        }

        async function compressToLimit(file, maxBytes) {
            if (!file || file.size <= maxBytes || !file.type.startsWith('image/')) {
                return file;
            }

            const dataUrl = await new Promise((resolve, reject) => {
                const r = new FileReader();
                r.onload = () => resolve(r.result);
                r.onerror = () => reject(new Error('Failed to read image'));
                r.readAsDataURL(file);
            });

            const img = await new Promise((resolve, reject) => {
                const i = new Image();
                i.onload = () => resolve(i);
                i.onerror = () => reject(new Error('Failed to decode image'));
                i.src = dataUrl;
            });

            let scale = 1.0;
            let quality = 0.9;
            let out = null;
            for (let i = 0; i < 8; i++) {
                const canvas = document.createElement('canvas');
                canvas.width = Math.max(1, Math.floor(img.width * scale));
                canvas.height = Math.max(1, Math.floor(img.height * scale));
                const ctx = canvas.getContext('2d');
                if (!ctx) break;
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', quality));
                if (!blob) break;
                out = blob;
                if (blob.size <= maxBytes) break;
                quality = Math.max(0.5, quality - 0.1);
                scale = Math.max(0.45, scale * 0.85);
            }

            if (!out) return file;
            const extSafeName = file.name.replace(/\.[^.]+$/, '') || 'upload';
            return new File([out], `${extSafeName}.webp`, { type: 'image/webp' });
        }

        async function handleFile(dz, file, imgEl, removeBtn, setter) {
            if (!file) return;
            dz.classList.add('loading');
            try {
                let finalFile = file;
                if (file.size > currentUploadBytes) {
                    finalFile = await compressToLimit(file, currentUploadBytes);
                }
                if (finalFile.size > currentUploadBytes) {
                    alert(`File is too large even after compression. Max for your plan is ${currentUploadMb}MB`);
                    dz.classList.remove('loading');
                    return;
                }

                setter(finalFile);
                const reader = new FileReader();
                reader.onload = (e) => {
                    imgEl.src = e.target.result;
                    imgEl.style.display = 'block';
                    removeBtn.style.display = 'flex';
                    dz.classList.remove('loading');
                };
                reader.onerror = () => {
                    alert("Failed to read file.");
                    dz.classList.remove('loading');
                };
                reader.readAsDataURL(finalFile);
            } catch (err) {
                console.error(err);
                alert("Error processing file: " + err.message);
                dz.classList.remove('loading');
            }
        }

        setupDropzone(dBefore, fBefore, document.getElementById('preview-before'), document.getElementById('remove-before'), (f) => fileBeforeObj = f);
        setupDropzone(dAfter, fAfter, document.getElementById('preview-after'), document.getElementById('remove-after'), (f) => fileAfterObj = f);

        // Run Analyze
        const btnAnalyze = document.getElementById('btn-run-analyze');
        const analyzeError = document.getElementById('analyze-error');
        const resultsArea = document.getElementById('results-area');
        
        function updateUsageUI(usageObj) {
            if (!usageObj) return;
            const { used, limit } = usageObj;
            let percent = 0;
            if (limit) {
                document.querySelector('.usage-text').innerHTML = `<span data-i18n="usage_limits">Usage Limits:</span> <span id="usage-count">${used.toLocaleString()}</span>/${limit.toLocaleString()} credits`;
                percent = Math.min(100, Math.round((used / limit) * 100));
            } else {
                document.querySelector('.usage-text').innerHTML = `<span data-i18n="usage_limits">Usage Limits:</span> <span id="usage-count">${used.toLocaleString()}</span> / Unlimited`;
            }
            const fillEl = document.querySelector('.progress-fill');
            if (fillEl) fillEl.style.width = percent + '%';
        }

        // Real API call to changeformer
        async function callChangeFormer(before, after, mode, model) {
            const formData = new FormData();
            formData.append('before', before);
            formData.append('after', after);
            formData.append('mode', mode);
            formData.append('model', model);

            const response = await fetch('/api/changeformer.php', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });
            return await window.parseApiResponse(response);
        }

        function renderResults(res) {
            if (!res) return;
            document.getElementById('res-img-before').src = res.beforeUrl || document.getElementById('preview-before').src;
            document.getElementById('res-img-after').src = res.afterUrl || document.getElementById('preview-after').src;
            document.getElementById('res-img-output').src = res.overlayUrl;

            const metricsContainer = document.getElementById('metrics-container');
            metricsContainer.innerHTML = '';
            const domainMetrics = (res.metrics && res.metrics.domain_metrics) ? res.metrics.domain_metrics : [];
            domainMetrics.forEach(m => {
                const translatedLabel = t(m.label);
                const diffStr = String(m.change);
                const diffClass = diffStr.includes('+') ? 'diff-pos' : (diffStr.includes('-') ? 'diff-neg' : '');
                metricsContainer.innerHTML += `
                    <div class="metric-card">
                        <div class="metric-header"><i data-lucide="${m.icon || 'activity'}" width="16" height="16"></i> <span data-i18n="${m.label}">${translatedLabel}</span></div>
                        <div class="metric-stats">
                            <div class="stat-col"><span class="stat-label" data-i18n="before">${t('before')}</span><span class="stat-val">${m.before}</span></div>
                            <div class="stat-col"><span class="stat-label" data-i18n="after">${t('after')}</span><span class="stat-val">${m.after}</span></div>
                            <div class="stat-diff ${diffClass}">${m.change}</div>
                        </div>
                    </div>
                `;
            });
            lucide.createIcons();
            document.getElementById('floating-ai-toggle').style.display = 'flex';
        }

        btnAnalyze.addEventListener('click', async () => {
            if (!fileBeforeObj || !fileAfterObj) {
                analyzeError.style.display = 'block';
                return;
            }
            analyzeError.style.display = 'none';
            btnAnalyze.disabled = true;
            btnAnalyze.innerHTML = `<i data-lucide="loader-2" class="animate-spin" width="18" height="18"></i> <span data-i18n="processing">Processing...</span>`;
            lucide.createIcons();

            // Lock inputs
            dBefore.style.pointerEvents = 'none';
            dAfter.style.pointerEvents = 'none';

            try {
                const res = await callChangeFormer(fileBeforeObj, fileAfterObj, selectedMode, selectedModel);
                currentResults = res;
                
                try {
                    const createRes = await fetch('/api/projects.php', {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'create_project', name: selectedMode.charAt(0).toUpperCase() + selectedMode.slice(1) + ' Analysis', description: 'Generated on ' + new Date().toLocaleString() })
                    });
                    const created = await window.parseApiResponse(createRes);
                    if (created && created.project && created.project.id) {
                        await fetch('/api/projects.php', {
                            method: 'POST',
                            credentials: 'include',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'save_analysis', project_id: created.project.id, mode: selectedMode, payload: res })
                        });
                        window.currentProjectId = created.project.id;
                    }
                } catch(err) {
                    console.error("Failed to save project history:", err);
                }
                
                if (res.usage) {
                    updateUsageUI(res.usage);
                }
                renderResults(res);

                resultsArea.style.display = 'block';
                setTimeout(() => {
                    resultsArea.style.opacity = '1';
                    resultsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);

                // Reset inline AI section for new analysis
                const inlineSection = document.getElementById('ai-inline-section');
                if (inlineSection) inlineSection.style.display = '';


            } catch (error) {
                analyzeError.textContent = "Inference failed: " + error.message;
                analyzeError.style.display = 'block';
            } finally {
                btnAnalyze.disabled = false;
                btnAnalyze.innerHTML = `<span data-i18n="run_analyze">Run Analyze</span> <span class="cost-badge">-25 Credits</span> <i data-lucide="play" width="18" height="18"></i>`;
                lucide.createIcons();
                dBefore.style.pointerEvents = 'auto';
                dAfter.style.pointerEvents = 'auto';
            }
        });

        // Generate AI Solution
        const btnAI = document.getElementById('btn-generate-ai');
        const aiOutput = document.getElementById('ai-output');
        const aiEntry = document.getElementById('ai-entry');
        const aiError = document.getElementById('ai-error');
        const chatInput = document.getElementById('chat-input');
        const btnSendChat = document.getElementById('btn-send-chat');
        const chatHistory = document.getElementById('chat-history');
        const chatInputContainer = document.getElementById('chat-input-container');

        window.chatMessages = []; // store messages in memory

        window.toggleAiSidebar = function() {
            const sidebar = document.getElementById('ai-sidebar');
            sidebar.classList.toggle('open');
        };

        window.selectProvider = function(provider) {
            document.querySelectorAll('.provider-card').forEach(card => {
                card.classList.remove('selected');
            });
            const selected = document.querySelector(`.provider-card[data-provider="${provider}"]`);
            if (selected) selected.classList.add('selected');
            const radio = document.getElementById(provider === 'gemini' ? 'radio-gemini' : 'radio-openai');
            if (radio) radio.checked = true;
            const hiddenSelect = document.getElementById('ai-model-provider');
            if (hiddenSelect) hiddenSelect.value = provider;
        };

        window.showToast = function(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.textContent = msg;
            Object.assign(toast.style, {
                position: 'fixed', bottom: '28px', left: '50%',
                transform: 'translateX(-50%) translateY(20px)',
                background: type === 'success' ? 'rgba(34,197,94,0.18)' : 'rgba(239,68,68,0.18)',
                color: type === 'success' ? '#4ade80' : '#f87171',
                border: `1px solid ${type === 'success' ? 'rgba(34,197,94,0.35)' : 'rgba(239,68,68,0.35)'}`,
                backdropFilter: 'blur(16px)', webkitBackdropFilter: 'blur(16px)',
                padding: '10px 22px', borderRadius: '100px',
                fontSize: '13px', fontWeight: '600', zIndex: '9999',
                pointerEvents: 'none', opacity: '0',
                transition: 'all 0.35s cubic-bezier(0.16,1,0.3,1)',
                letterSpacing: '-0.01em', whiteSpace: 'nowrap',
            });
            document.body.appendChild(toast);
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(-50%) translateY(0)';
            });
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(10px)';
                setTimeout(() => toast.remove(), 400);
            }, 2800);
        };

        window.openSettingsModal = function() {
            if (window.userPlan && window.userPlan.toLowerCase() === 'free') return; // Prevent opening for Free
            document.getElementById('profile-dropdown').classList.remove('show');
            const modal = document.getElementById('settings-modal');
            modal.classList.add('active');
            const savedKey = localStorage.getItem('mv_api_key') || '';
            const model = localStorage.getItem('mv_ai_model') || 'openai';
            document.getElementById('custom-api-key').value = savedKey;
            const modelSelect = document.getElementById('ai-model-provider');
            if(modelSelect) modelSelect.value = model;
            window.selectProvider(model);
        };

        window.closeSettingsModal = function() {
            document.getElementById('settings-modal').classList.remove('active');
        };

        window.saveApiKey = function() {
            const val = document.getElementById('custom-api-key').value.trim();
            const modelSelect = document.getElementById('ai-model-provider');
            const model = modelSelect ? modelSelect.value : 'openai';
            
            localStorage.setItem('mv_ai_model', model);
            if(val) {
                localStorage.setItem('mv_api_key', val);
                const banner = document.getElementById('api-key-banner');
                if (banner) banner.style.display = 'none';
            } else {
                localStorage.removeItem('mv_api_key');
                const banner = document.getElementById('api-key-banner');
                if (banner) banner.style.display = 'block';
            }
            window.closeSettingsModal();
            window.showToast(t('save_api_settings') + ' ✓');
        };

        // Open sidebar and kick off generation from inline button
        window.openAIAndGenerate = function() {
            const sidebar = document.getElementById('ai-sidebar');
            sidebar.classList.add('open');
            const btn = document.getElementById('btn-generate-ai');
            if (btn && !btn.disabled) btn.click();
        };

        function appendChatMsg(role, content) {
            const div = document.createElement('div');
            div.className = `chat-bubble ${role === 'user' ? 'chat-user' : 'chat-bot'}`;
            div.innerHTML = content;
            chatHistory.appendChild(div);
            setTimeout(()=> { chatHistory.scrollTop = chatHistory.scrollHeight; }, 10);
            window.chatMessages.push({role, content: role==='user'? content : 'Prior AI Report shown to user.'});
        }

        async function callAI(data) {
            const localKey = localStorage.getItem('mv_api_key');
            const localModel = localStorage.getItem('mv_ai_model') || 'openai';
            if(localKey) data.customApiKey = localKey;
            data.aiModel = localModel;
            data.language = currentLang;

            const response = await fetch('/api/ai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(data)
            });
            return await window.parseApiResponse(response);
        }

        btnAI.addEventListener('click', async () => {
            if(!currentResults) return;
            
            btnAI.innerHTML = '<i data-lucide="loader-2" class="animate-spin" width="16" height="16"></i> <span data-i18n="generating">' + t('generating') + '</span>';
            btnAI.disabled = true;
            lucide.createIcons();

            try {
                const payload = {
                    action: 'initial_report',
                    mode: selectedMode,
                    plan: window.userPlan,
                    metrics: currentResults.metrics,
                    changePercent: currentResults.changePercent,
                    hotspots: currentResults.hotspots || [],
                    imageRefs: {
                        beforeUrl: currentResults.beforeUrl || '',
                        afterUrl: currentResults.afterUrl || '',
                        overlayUrl: currentResults.overlayUrl || ''
                    },
                    project_id: window.currentProjectId
                };
                const aiRes = await callAI(payload);
                if (aiRes.usage) updateUsageUI(aiRes.usage);
                
                aiEntry.style.display = 'none';
                aiOutput.style.display = 'block';
                chatHistory.style.display = 'flex';
                chatInputContainer.style.display = 'flex';

                // Hide API key banner after generation
                const apiBanner = document.getElementById('api-key-banner');
                if (apiBanner) apiBanner.style.display = 'none';

                let html = `<div class="ai-summary ai-fade-in"><strong style="color:var(--text-primary);"><i data-lucide="bot" width="16" height="16" style="vertical-align:middle;margin-right:6px;"></i>${t('executive_summary')}:</strong><br><br>${aiRes.summary}</div>`;

                if (aiRes.problems) {
                    html += `<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px;">`;
                    aiRes.problems.slice(0, 4).forEach((p, idx) => {
                        let solHtml = '';
                        const solutions = aiRes.solutionsByProblem ? aiRes.solutionsByProblem[p.id] : [];
                        if (solutions && solutions.length > 0) {
                            const s = solutions[0];
                            solHtml += `
                                <div class="solution-item" style="border-left:none; padding:0; margin-bottom:0; background:none;">
                                    <div class="solution-title" style="margin-top:10px; margin-bottom:6px; font-size:11px; text-transform:uppercase; color:var(--text-tertiary); letter-spacing:0.5px;"><i data-lucide="zap" width="12" height="12"></i> ${t('primary_solution')}</div>
                                    <div style="font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:10px;">${s.title}</div>
                                    <div class="solution-pros-cons" style="grid-template-columns: 1fr; gap:6px;">
                                        <ul class="pro-list" style="margin:0; font-size:12px;">${(s.pros||[]).slice(0,2).map(pro => `<li>${pro}</li>`).join('')}</ul>
                                        <ul class="con-list" style="margin:0; font-size:12px;">${(s.cons||[]).slice(0,2).map(con => `<li>${con}</li>`).join('')}</ul>
                                    </div>
                                </div>
                            `;
                        }

                        html += `
                            <div class="problem-card ai-fade-in ai-fade-in-delay-${Math.min(idx + 1, 4)}" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:space-between; height:100%;">
                                <div>
                                    <div class="problem-header" style="font-size:15px;"><i data-lucide="alert-triangle" class="text-gold" width="16" height="16"></i> ${p.title}</div>
                                    <p style="font-size:13px;color:var(--text-secondary); margin-bottom:0; line-height:1.5;">${p.description}</p>
                                </div>
                                <div class="problem-solutions">${solHtml}</div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }

                if(window.userPlan === 'Free' || window.userPlan === 'Lite') {
                    html += `<div class="ai-upgrade-banner ai-fade-in ai-fade-in-delay-4">
                        <div>
                            <strong>${t('want_more')}</strong><br>
                            <span class="text-secondary" style="font-size:12px;">${t('upgrade_hint')}</span>
                        </div>
                        <a href="#upgrade" onclick="openCheckout()" class="btn-outline text-gold" style="border-color:var(--gold); font-size:12px;"><i data-lucide="zap" width="14" height="14"></i> ${t('upgrade_plan')}</a>
                    </div>`;
                }

                aiOutput.innerHTML = html;
                appendChatMsg('bot', html);
                lucide.createIcons();

                // Hide inline AI section since report is now in the sidebar
                const inlineSection = document.getElementById('ai-inline-section');
                if (inlineSection) inlineSection.style.display = 'none';

            } catch (err) {
                aiError.textContent = err?.message || "AI request failed. Please try again.";
                aiError.style.display = 'block';
                btnAI.innerHTML = '<i data-lucide="sparkles" width="16" height="16"></i> <span data-i18n="ai_btn">' + t('ai_btn') + '</span>';
                btnAI.disabled = false;
                lucide.createIcons();
            }
        });

        window.checkChatInputKey = function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                window.sendChatMessage();
            }
        };

        window.sendChatMessage = async function() {
            const msg = chatInput.value.trim();
            if(!msg) return;

            appendChatMsg('user', msg);
            chatInput.value = '';
            
            btnSendChat.disabled = true;
            btnSendChat.innerHTML = '<i data-lucide="loader-2" class="animate-spin" width="18" height="18"></i>';
            lucide.createIcons();

            try {
                const payload = {
                    action: 'chat_message',
                    messages: window.chatMessages,
                    project_id: window.currentProjectId,
                    plan: window.userPlan,
                    mode: selectedMode
                };
                
                const aiRes = await callAI(payload);
                if (aiRes.usage) {
                    updateUsageUI(aiRes.usage);
                }
                
                appendChatMsg('bot', aiRes.reply || aiRes.error || "Could not generate reply.");
            } catch (error) {
                appendChatMsg('bot', `<span class="text-danger">Error: ${error.message}</span>`);
            } finally {
                btnSendChat.disabled = false;
                btnSendChat.innerHTML = '<i data-lucide="send" width="18" height="18"></i>';
                lucide.createIcons();
            }
        };

        window.exportData = function(format) {
            if (!currentResults) {
                alert("No active results to export.");
                return;
            }
            if(format === 'pdf' && (window.userPlan === 'Free' || window.userPlan === 'Lite')) {
                alert("PDF Export requires Pro plan or higher.");
                return;
            }
            
            if (format === 'csv') {
                let csvContent = "\uFEFF"; // BOM for UTF-8
                csvContent += `${t('Metric') || 'Metric'},${t('before')},${t('after')},Change\n`;
                if (currentResults.metrics && currentResults.metrics.domain_metrics) {
                    currentResults.metrics.domain_metrics.forEach(m => {
                        csvContent += `"${t(m.label)}","${m.before}","${m.after}","${m.change}"\n`;
                    });
                }
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.setAttribute("href", url);
                link.setAttribute("download", `analysis_${selectedMode}_${new Date().getTime()}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else if (format === 'pdf') {
                const element = document.getElementById('results-area');
                if (element && window.html2pdf) {
                    const opt = {
                        margin:       0.5,
                        filename:     `analysis_${selectedMode}_${new Date().getTime()}.pdf`,
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, useCORS: true },
                        jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
                    };
                    html2pdf().set(opt).from(element).save();
                }
            }
        }

        // View Navigation
        const navItems = document.querySelectorAll('.nav-item');
        const viewSections = document.querySelectorAll('.view-section');

        navItems.forEach(item => {
            item.addEventListener('click', () => {
                const targetId = item.getAttribute('data-target');
                
                navItems.forEach(n => n.classList.remove('active'));
                item.classList.add('active');

                viewSections.forEach(v => {
                    if(v.id === targetId) {
                        v.classList.add('active');
                    } else {
                        v.classList.remove('active');
                    }
                });
                
                if (targetId === 'view-projects') {
                    if (window._authReady) loadProjects();
                }
            });
        });

        window.deleteProject = async function(id, e) {
            e.stopPropagation();
            if (!confirm('Are you sure you want to delete this project?')) return;
            try {
                const res = await fetch('/api/projects.php', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_project', project_id: id })
                });
                await window.parseApiResponse(res);
                if (window.currentProjectId === id) {
                    window.currentProjectId = null;
                    document.getElementById('res-img-before').src = '';
                    document.getElementById('res-img-after').src = '';
                    document.getElementById('res-img-output').src = '';
                    document.getElementById('metrics-container').innerHTML = '';
                    document.getElementById('results-area').style.display = 'none';
                    document.getElementById('floating-ai-toggle').style.display = 'none';
                    document.getElementById('ai-output').style.display = 'none';
                    document.getElementById('ai-entry').style.display = 'block';
                    currentResults = null;
                }
                loadProjects();
                window.showToast && window.showToast('Project deleted');
            } catch (err) {
                window.showToast ? window.showToast('Error: ' + err.message, 'error') : alert('Error deleting project: ' + err.message);
            }
        };

        function getGroupTitle(dateStr) {
            const today = new Date();
            const date = new Date(dateStr);
            const diffTime = today - date;
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            
            if (date.toDateString() === today.toDateString()) return 'Today';
            
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
            
            if (diffDays <= 7) return 'Previous 7 Days';
            if (diffDays <= 30) return 'Previous 30 Days';
            return 'Older';
        }

        let allProjects = []; // store globally for filtering
        let activeFilter = 'all';

        window.setProjectFilter = function(filter, btnEl) {
            activeFilter = filter;
            document.querySelectorAll('.projects-filter-btn').forEach(b => b.classList.remove('active'));
            if (btnEl) btnEl.classList.add('active');
            filterProjects();
        };

        window.filterProjects = function() {
            const search = (document.getElementById('project-search')?.value || '').toLowerCase();
            const modeFilter = document.getElementById('filter-mode')?.value || '';
            const now = new Date();
            const todayStr = now.toDateString();
            const weekAgo = new Date(now - 7 * 24 * 60 * 60 * 1000);

            let filtered = allProjects.filter(p => {
                const name = (p.name || '').toLowerCase();
                const mode = name.split(' ')[0];
                const date = new Date(p.created_at);

                if (search && !name.includes(search)) return false;
                if (modeFilter && mode !== modeFilter) return false;
                if (activeFilter === 'today' && date.toDateString() !== todayStr) return false;
                if (activeFilter === 'week' && date < weekAgo) return false;
                return true;
            });
            renderProjectsList(filtered, true);
        };

        window.loadProjects = async function loadProjects() {
            if (!window._authReady) return; // bail if session not ready
            const listEl = document.getElementById('projects-list');
            if (!listEl) return;
            listEl.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-tertiary);"><i data-lucide="loader-2" width="32" height="32" class="animate-spin" style="opacity:0.4;"></i></div>`;
            lucide.createIcons();
            try {
                const response = await fetch('/api/projects.php', { credentials: 'include' });
                if (response.status === 401) {
                    // Session expired mid-session — reload to re-auth
                    window._authReady = false;
                    listEl.innerHTML = `<div class="projects-empty"><div class="projects-empty-icon"><i data-lucide="lock" width="28" height="28" style="opacity:0.5;"></i></div><h3>Session expired</h3><p>Reloading...</p></div>`;
                    lucide.createIcons();
                    setTimeout(() => location.reload(), 1500);
                    return;
                }
                const data = await window.parseApiResponse(response);
                allProjects = data.projects || [];
                renderProjectsList(allProjects);
            } catch (err) {
                listEl.innerHTML = `
                    <div style="grid-column:1/-1; background:var(--danger-bg); border:1px solid var(--danger); border-radius:12px; padding:24px; text-align:center;">
                        <i data-lucide="wifi-off" width="32" height="32" style="color:var(--danger); margin-bottom:12px;"></i>
                        <p style="color:var(--danger); font-weight:600; margin-bottom:4px;">Could not load projects</p>
                        <p style="color:var(--text-tertiary); font-size:13px;">${err.message}</p>
                        <button class="btn-outline" style="margin-top:12px;" onclick="loadProjects()"><i data-lucide="refresh-cw" width="14" height="14"></i> Retry</button>
                    </div>`;
                lucide.createIcons();
            }
        };

        // If auth already resolved before this script ran, load now
        if (window._authReady) window.loadProjects();

        const modeIcons = { compare:'git-compare', water:'droplet', forest:'trees', agriculture:'leaf', urban:'building-2' };
        const modeColors = { compare:'var(--gold)', water:'#38bdf8', forest:'var(--success)', agriculture:'#a3e635', urban:'var(--warning)' };

        function renderProjectsList(projects, isFiltered) {
            const listEl = document.getElementById('projects-list');

            // Only update stats if not a filtering call
            if (!isFiltered) {
                const now = new Date();
                const weekAgo = new Date(now - 7 * 24 * 60 * 60 * 1000);
                const thisWeek = projects.filter(p => new Date(p.created_at) >= weekAgo).length;
                const modeCounts = {};
                projects.forEach(p => { const m = (p.name||'').split(' ')[0].toLowerCase(); modeCounts[m] = (modeCounts[m]||0)+1; });
                const topMode = Object.entries(modeCounts).sort((a,b)=>b[1]-a[1])[0]?.[0] || '—';
                const el = (id,v) => { const e = document.getElementById(id); if(e) e.textContent = v; };
                el('stat-total', projects.length);
                el('stat-thisweek', thisWeek);
                el('stat-mode', topMode.charAt(0).toUpperCase() + topMode.slice(1) || '—');
                el('stat-ai', projects.length);

                // Update sidebar
                let sidebarGroups = { 'Today':[], 'Yesterday':[], 'Previous 7 Days':[], 'Previous 30 Days':[], 'Older':[] };
                projects.forEach(p => {
                    const group = getGroupTitle(p.created_at);
                    sidebarGroups[group].push(`
                        <button class="history-item ${window.currentProjectId == p.id ? 'active' : ''}" onclick="viewProject('${p.id}')" title="${p.name}" style="width:100%;">
                            <i data-lucide="${modeIcons[(p.name||'').split(' ')[0].toLowerCase()] || 'layers'}" width="14" height="14" style="flex-shrink:0; opacity:0.7;"></i>
                            <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:12px;">${p.name || 'Project'}</span>
                        </button>`);
                });
                let sidebarHtml = '';
                for (const [grp, items] of Object.entries(sidebarGroups)) {
                    if (items.length) { sidebarHtml += `<div class="sidebar-heading" style="margin-top:12px; margin-bottom:6px;">${grp}</div>${items.join('')}`; }
                }
                const sidebarList = document.getElementById('sidebar-projects-list');
                if (sidebarList) sidebarList.innerHTML = sidebarHtml || `<div style="padding:10px; font-size:12px; color:var(--text-tertiary); text-align:center;">No history yet</div>`;
            }

            if (!projects.length) {
                listEl.innerHTML = `
                    <div class="projects-empty">
                        <div class="projects-empty-icon"><i data-lucide="folder-plus" width="28" height="28" style="opacity:0.5;"></i></div>
                        <h3>No projects yet</h3>
                        <p>Run your first satellite analysis to start building your project history.</p>
                        <button class="btn-ai" style="margin:0 auto;" onclick="document.querySelector('.nav-item[data-target=view-dashboard]').click()">
                            <i data-lucide="plus" width="16" height="16"></i> Start First Analysis
                        </button>
                    </div>`;
                lucide.createIcons();
                return;
            }

            let html = '';
            projects.forEach((p, idx) => {
                const mode = (p.name||'compare analysis').split(' ')[0].toLowerCase();
                const icon = modeIcons[mode] || 'layers';
                const color = modeColors[mode] || 'var(--gold)';
                const dateStr = new Date(p.created_at).toLocaleDateString(undefined, { month:'short', day:'numeric', year:'numeric' });
                const timeStr = new Date(p.created_at).toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
                html += `
                    <div class="project-card" style="animation:metricFadeIn 0.4s var(--ease) ${idx * 0.05}s both;">
                        <div class="project-card-header">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div class="project-card-icon" style="background:rgba(59,130,246,0.1); border:1px solid ${color}30;">
                                    <i data-lucide="${icon}" width="14" height="14" style="color:${color};"></i>
                                </div>
                                <div>
                                    <div class="project-card-mode" style="color:${color};">${mode}</div>
                                    <div class="project-card-date">${dateStr} · ${timeStr}</div>
                                </div>
                            </div>
                            <button class="project-delete-btn" onclick="deleteProject('${p.id}', event)" title="Delete">
                                <i data-lucide="trash-2" width="14" height="14"></i>
                            </button>
                        </div>
                        <div class="project-card-body">
                            <div class="project-card-name">${p.name || 'Untitled Project'}</div>
                            <div class="project-card-meta">Project #${p.id} · MercuryVision</div>
                        </div>
                        <div class="project-card-footer">
                            <button class="project-card-btn" onclick="viewProject('${p.id}')">
                                <i data-lucide="eye" width="12" height="12"></i> View
                            </button>
                            <button class="project-card-btn-ai" onclick="viewProject('${p.id}'); setTimeout(()=>window.openAIAndGenerate(),300);" title="Open & generate AI report">
                                <i data-lucide="sparkles" width="12" height="12"></i>
                            </button>
                        </div>
                    </div>`;
            });
            listEl.innerHTML = html;
            lucide.createIcons();
        }
        
        window.viewProject = async function(id) {
            try {
                const response = await fetch('/api/projects.php?action=project_detail&project_id=' + id, { credentials: 'include' });
                const data = await window.parseApiResponse(response);
                if (data && data.analyses && data.analyses.length > 0) {
                    const latest = data.analyses[0];
                    currentResults = latest.payload;
                    window.currentProjectId = data.project.id;
                    
                    // Reset chat state for new project
                    window.chatMessages = [];
                    const chatHistoryEl = document.getElementById('chat-history');
                    if (chatHistoryEl) {
                        chatHistoryEl.innerHTML = '';
                        chatHistoryEl.style.display = 'none';
                    }
                    const chatInputCont = document.getElementById('chat-input-container');
                    if (chatInputCont) chatInputCont.style.display = 'none';
                    
                    document.querySelector('.nav-item[data-target="view-dashboard"]').click();
                    
                    if (currentResults.usage) updateUsageUI(currentResults.usage);
                    renderResults(currentResults);
                    
                    const resultsArea = document.getElementById('results-area');
                    resultsArea.style.display = 'block';
                    resultsArea.style.opacity = '1';
                    
                    document.getElementById('floating-ai-toggle').style.display = 'flex';
                    
                    // Reset AI sidebar state
                    const aiEntry = document.getElementById('ai-entry');
                    const aiBtn = document.getElementById('btn-generate-ai');
                    const aiOutput = document.getElementById('ai-output');
                    aiOutput.style.display = 'none';
                    aiOutput.innerHTML = '';
                    aiEntry.style.display = 'block';
                    aiBtn.style.display = 'inline-flex';
                    aiBtn.disabled = false;
                    aiBtn.innerHTML = '<i data-lucide="sparkles" width="16" height="16"></i> <span data-i18n="ai_btn">' + t('ai_btn') + '</span> <span class="cost-badge">-15 Credits</span>';
                    document.getElementById('ai-error').style.display = 'none';
                    
                    // Show inline AI section again
                    const inlineSection = document.getElementById('ai-inline-section');
                    if (inlineSection) inlineSection.style.display = '';
                    
                    // Close AI sidebar
                    document.getElementById('ai-sidebar').classList.remove('open');
                    
                    lucide.createIcons();
                    
                    setTimeout(() => {
                        resultsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                    
                    // Removing legacy analysis list insertion
                    const analysisList = document.getElementById(`sidebar-analysis-list-${id}`);
                    if (analysisList) {
                        analysisList.remove();
                    }
                    loadProjects(); // Re-render sidebar active state
                } else {
                    window.showToast ? window.showToast('This project has no analysis data yet.', 'error') : alert("This project has no analysis data yet.");
                }
            } catch(e) {
                window.showToast ? window.showToast('Failed to load project: ' + e.message, 'error') : alert("Failed to load project details: " + e.message);
            }
        }

        // Checkout Logic
        window.openCheckout = function() {
            document.getElementById('checkout-modal').classList.add('active');
            document.getElementById('checkout-step-1').style.display = 'block';
            document.getElementById('checkout-step-2').style.display = 'none';
            document.getElementById('checkout-step-3').style.display = 'none';
            document.getElementById('profile-dropdown').classList.remove('show');
            lucide.createIcons();
        }

        window.closeCheckout = async function(success = false) {
            if(success && window.selectedPlanToBuy) {
                try {
                    const res = await fetch('/api/checkout.php', {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ plan: window.selectedPlanToBuy })
                    });
                    const data = await window.parseApiResponse(res);
                    document.getElementById('user-plan').textContent = data.plan + ' Plan';
                    window.userPlan = data.plan;
                    syncUploadLimitByPlan(window.userPlan);
                    applyUploadLimitHint();
                    document.querySelectorAll('.mode-card.locked').forEach(card => {
                        const reqPlan = card.dataset.plan;
                        if(window.userPlan === 'Standard' || window.userPlan === 'Pro' || window.userPlan === 'Enterprise') {
                            card.classList.remove('locked');
                            const badge = card.querySelector('.locked-badge'); if (badge) badge.remove();
                        } else if (window.userPlan === 'Lite' && reqPlan === 'Lite') {
                            card.classList.remove('locked');
                            const badge = card.querySelector('.locked-badge'); if (badge) badge.remove();
                        }
                    });
                } catch(err) {
                    // Silently handle Unauthorized and other checkout errors in demo mode
                    console.info('Checkout note:', err.message);
                }
            }
            document.getElementById('checkout-modal').classList.remove('active');
        }

        window.selectedPlanToBuy = '';
        window.selectPlan = function(planStr) {
            window.selectedPlanToBuy = planStr;
            document.getElementById('checkout-plan-name').textContent = planStr;
            document.getElementById('checkout-step-1').style.display = 'none';
            document.getElementById('checkout-step-2').style.display = 'block';
        }

        window.processPayment = function() {
            const btn = document.getElementById('btn-pay-now');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" width="18" height="18"></i> Processing...';
            lucide.createIcons();
            
            setTimeout(() => {
                document.getElementById('checkout-step-2').style.display = 'none';
                document.getElementById('checkout-step-3').style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="lock" width="18" height="18"></i> Pay Securely';
            }, 1500);
        }

        // Attach checkout to upgrade button
        document.querySelector('.btn-upgrade-inline').addEventListener('click', (e) => {
            e.preventDefault();
            window.openCheckout();
        });

        // Setup slider logic
        const sliderContainer = document.getElementById('interactive-slider');
        const sliderClipper = document.getElementById('slider-clipper');
        const sliderHandle = document.getElementById('slider-handle');
        
        if (sliderContainer && sliderClipper && sliderHandle) {
            let isDraggingSlider = false;
            
            const moveSlider = (e) => {
                if (!isDraggingSlider) return;
                const rect = sliderContainer.getBoundingClientRect();
                let x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
                x = Math.max(0, Math.min(x, rect.width));
                const percent = (x / rect.width) * 100;
                sliderClipper.style.width = percent + '%';
                sliderHandle.style.left = percent + '%';
            };

            sliderHandle.addEventListener('mousedown', () => isDraggingSlider = true);
            sliderHandle.addEventListener('touchstart', () => isDraggingSlider = true, {passive: true});
            window.addEventListener('mouseup', () => isDraggingSlider = false);
            window.addEventListener('touchend', () => isDraggingSlider = false);
            window.addEventListener('mousemove', moveSlider);
            window.addEventListener('touchmove', moveSlider, {passive: true});
        }

        // loadProjects() is called from onAuthStateChanged after session is ready
        // Do NOT call it here — Firebase auth is async and the PHP session isn't set up yet

    </script>
</body>
</html>
