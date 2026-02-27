<?php
// MercuryVision Studio - Enterprise AI Satellite Intelligence
// index.php
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MercuryVision Studio - Enterprise AI Satellite Intelligence</title>
    <meta name="description" content="Transform satellite data into strategic insights. Real-time environmental monitoring with 99% accuracy.">
    <meta name="keywords" content="satellite analysis, AI change detection, environmental monitoring, NDVI, remote sensing">
    <meta property="og:title" content="MercuryVision Studio - Enterprise AI Satellite Intelligence">
    <meta property="og:description" content="Transform satellite data into strategic insights. Real-time environmental monitoring with 99% accuracy.">
    <meta property="og:type" content="website">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Premium AI Startup Design System */
        :root {
            --bg-primary: #09090b; 
            --bg-secondary: #121214;
            --bg-card: rgba(255, 255, 255, 0.03);
            --bg-card-hover: rgba(255, 255, 255, 0.05);
            --bg-glass: rgba(9, 9, 11, 0.75);
            
            --gold: #3b82f6;
            --gold-light: #60a5fa;
            --gold-glow: transparent;
            
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-tertiary: #71717a;
            --text-inverse: #000000;
            
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.15);
            
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.4);
            --shadow-glow: none;

            --icon-blue: #60a5fa;
            --icon-purple: #c084fc;
            --icon-green: #4ade80;
            --icon-orange: #fb923c;
            --icon-pink: #f472b6;

            --spacing-md: 24px;
            --spacing-lg: 32px;
            --spacing-xl: 48px;
            --spacing-2xl: 64px;
            --spacing-3xl: 96px;
            
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        [data-theme="light"] {
            --bg-primary: #ffffff;
            --bg-secondary: #f4f4f5;
            --bg-card: #ffffff;
            --bg-card-hover: #fafafa;
            --bg-glass: rgba(255, 255, 255, 0.85);
            
            --gold: #d97706;
            --gold-light: #f59e0b;
            --gold-glow: transparent;
            
            --text-primary: #09090b;
            --text-secondary: #52525b;
            --text-tertiary: #a1a1aa;
            --text-inverse: #ffffff;
            
            --border: rgba(0, 0, 0, 0.08);
            --border-hover: rgba(0, 0, 0, 0.15);
            
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0,0,0,0.02);
            --shadow-lg: 0 20px 40px -12px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0,0,0,0.02);
            --shadow-glow: none;
            
            --icon-blue: #2563eb;
            --icon-purple: #9333ea;
            --icon-green: #16a34a;
            --icon-orange: #ea580c;
            --icon-pink: #db2777;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
        
        body { 
            font-family: var(--font-family); 
            background: var(--bg-primary); 
            color: var(--text-primary); 
            line-height: 1.6; 
            overflow-x: hidden; 
            transition: background 0.4s var(--ease), color 0.4s var(--ease); 
        }
        
        .bg-pattern {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
            background-image: radial-gradient(var(--border) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.5;
            pointer-events: none;
        }

        .bg-effects { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; overflow: hidden; }
        .gradient-orb { position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.15; animation: float 25s ease-in-out infinite; }
        .orb-1 { width: 800px; height: 800px; background: radial-gradient(circle, var(--gold), transparent); top: -300px; right: -200px; }
        .orb-2 { width: 600px; height: 600px; background: radial-gradient(circle, var(--icon-blue), transparent); bottom: -200px; left: -150px; animation-delay: -12s; }
        .orb-3 { width: 500px; height: 500px; background: radial-gradient(circle, var(--icon-purple), transparent); top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: -8s; }
        
        @keyframes float { 
            0%, 100% { transform: translate(0, 0) scale(1); } 
            33% { transform: translate(30px, -50px) scale(1.05); } 
            66% { transform: translate(-20px, 40px) scale(0.95); } 
        }
        
        .container { max-width: 1280px; margin: 0 auto; padding: 0 var(--spacing-md); }
        .container-narrow { max-width: 960px; margin: 0 auto; padding: 0 var(--spacing-md); }
        section { padding: var(--spacing-3xl) 0; scroll-margin-top: 80px; }
        
        header { 
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000; 
            background: var(--bg-glass); 
            backdrop-filter: blur(24px); 
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border); 
            transition: all 0.3s var(--ease); 
        }
        .header-content { display: flex; align-items: center; justify-content: space-between; padding: 16px 0; }
        
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-primary); transition: opacity 0.2s; }
        .logo:hover { opacity: 0.8; }
        .logo-icon { width: 42px; height: 42px; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; color: #fff; }
        .logo-text { display: flex; flex-direction: column; gap: 2px; }
        .logo-brand { font-size: 17px; font-weight: 700; line-height: 1; letter-spacing: -0.5px; }
        .logo-descriptor { font-size: 10px; color: var(--gold); font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; }
        
        nav { display: flex; align-items: center; gap: var(--spacing-lg); }
        .nav-links { display: flex; gap: var(--spacing-lg); }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s var(--ease); position: relative; }
        .nav-links a:hover { color: var(--text-primary); }
        
        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .lang-switcher { display: flex; gap: 2px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 4px; box-shadow: var(--shadow-sm); }
        .lang-btn { padding: 6px 12px; background: transparent; border: none; color: var(--text-tertiary); cursor: pointer; border-radius: 6px; font-size: 13px; font-weight: 600; transition: all 0.2s var(--ease); }
        .lang-btn.active { background: var(--gold); color: #fff; }
        [data-theme="dark"] .lang-btn.active { color: #000; }
        
        .theme-toggle { width: 40px; height: 40px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s var(--ease); color: var(--text-secondary); box-shadow: var(--shadow-sm); }
        .theme-toggle:hover { background: var(--bg-card-hover); border-color: var(--border-hover); color: var(--text-primary); }
        
        .btn-primary { 
            padding: 10px 24px; 
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%); 
            color: #fff; 
            border: none; 
            border-radius: 8px; 
            font-size: 14px; 
            font-weight: 600; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.3s var(--ease); 
            box-shadow: var(--shadow-glow); 
        }
        [data-theme="light"] .btn-primary { color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--gold-glow); filter: brightness(1.1); }
        
        .hero { padding-top: calc(var(--spacing-3xl) + 100px); text-align: center; position: relative; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 24px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 100px; font-size: 12px; font-weight: 600; color: var(--gold); margin-bottom: var(--spacing-lg); letter-spacing: 1px; text-transform: uppercase; box-shadow: var(--shadow-sm); backdrop-filter: blur(10px); }
        .hero h1 { font-size: clamp(48px, 8vw, 88px); font-weight: 800; line-height: 1.05; margin-bottom: var(--spacing-md); letter-spacing: -2.5px; }
        .hero-gradient-text { background: linear-gradient(135deg, var(--text-primary) 30%, var(--text-tertiary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-subtitle { font-size: clamp(18px, 2.5vw, 22px); color: var(--text-secondary); max-width: 680px; margin: 0 auto var(--spacing-xl); line-height: 1.6; font-weight: 400; }
        
        .hero-cta { display: flex; gap: 16px; justify-content: center; align-items: center; flex-wrap: wrap; margin-bottom: var(--spacing-2xl); }
        .btn-hero { padding: 18px 36px; font-size: 16px; font-weight: 600; border-radius: 12px; text-decoration: none; transition: all 0.3s var(--ease); display: inline-flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-hero-primary { background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%); color: #fff; box-shadow: var(--shadow-glow); }
        [data-theme="light"] .btn-hero-primary { color: #fff; }
        .btn-hero-primary:hover { transform: translateY(-3px); filter: brightness(1.1); box-shadow: 0 16px 40px var(--gold-glow); }
        .btn-hero-secondary { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border); box-shadow: var(--shadow-sm); backdrop-filter: blur(10px); }
        .btn-hero-secondary:hover { background: var(--bg-card-hover); border-color: var(--text-tertiary); transform: translateY(-3px); }
        
        .hero-stats { display: flex; gap: var(--spacing-2xl); justify-content: center; flex-wrap: wrap; border-top: 1px solid var(--border); padding-top: var(--spacing-xl); max-width: 800px; margin: 0 auto; }
        .stat-item { display: flex; flex-direction: column; gap: 8px; align-items: center; }
        .stat-value { font-size: 20px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .stat-value::before { content: ''; display: block; width: 8px; height: 8px; background: var(--gold); border-radius: 50%; box-shadow: 0 0 10px var(--gold); }
        .stat-label { font-size: 14px; color: var(--text-secondary); font-weight: 500; }
        
        .features { background: var(--bg-secondary); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .section-header { text-align: center; margin-bottom: var(--spacing-2xl); }
        .section-title { font-size: clamp(32px, 5vw, 48px); font-weight: 800; margin-bottom: 16px; letter-spacing: -1.5px; color: var(--text-primary); }
        .section-subtitle { font-size: 18px; color: var(--text-secondary); max-width: 640px; margin: 0 auto; }
        
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
        .feature-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: var(--spacing-lg); transition: all 0.4s var(--ease); box-shadow: var(--shadow-sm); position: relative; z-index: 1; }
        .feature-card:hover { transform: translateY(-6px); border-color: var(--border-hover); box-shadow: var(--shadow-lg); background: var(--bg-card-hover); }
        
        .feature-icon { width: 64px; height: 64px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; box-shadow: var(--shadow-sm); }
        .feature-icon svg { width: 28px; height: 28px; }
        .icon-blue svg { color: var(--icon-blue); }
        .icon-purple svg { color: var(--icon-purple); }
        .icon-green svg { color: var(--icon-green); }
        .icon-orange svg { color: var(--icon-orange); }
        .icon-pink svg { color: var(--icon-pink); }
        
        .feature-title { font-size: 20px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.5px; color: var(--text-primary); }
        .feature-desc { font-size: 15px; color: var(--text-secondary); line-height: 1.6; }
        
        .pricing { text-align: center; }
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; max-width: 1300px; margin: var(--spacing-2xl) auto 0; }
        .pricing-card { 
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 32px;
            position: relative;
            transition: all 0.4s var(--ease);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
        }
        .pricing-card.featured { 
            border: 2px solid var(--gold);
            transform: scale(1.02);
            box-shadow: var(--shadow-lg), var(--shadow-glow);
            z-index: 2;
        }
        .pricing-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
        .pricing-card.featured:hover { transform: scale(1.02) translateY(-8px); }
        
        .pricing-badge { 
            position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(135deg, var(--gold), var(--gold-light)); color: #fff;
            padding: 6px 20px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            box-shadow: var(--shadow-sm);
        }
        [data-theme="light"] .pricing-badge { color: #fff; }
        
        .pricing-name { font-size: 24px; font-weight: 700; margin-bottom: 24px; color: var(--text-primary); letter-spacing: -0.5px; }
        .pricing-price-wrapper { display: flex; align-items: baseline; justify-content: center; gap: 12px; margin-bottom: 4px; }
        .pricing-price { font-size: 56px; font-weight: 800; color: var(--text-primary); line-height: 1; letter-spacing: -2px; }
        .pricing-original { font-size: 20px; color: var(--text-tertiary); text-decoration: line-through; font-weight: 500; }
        .pricing-usd { font-size: 14px; color: var(--text-tertiary); margin-bottom: 32px; display: block; font-weight: 500; }
        
        .pricing-features { list-style: none; text-align: left; margin-bottom: 40px; flex-grow: 1; display: flex; flex-direction: column; gap: 16px; }
        .pricing-features li { font-size: 15px; color: var(--text-secondary); display: flex; gap: 12px; align-items: flex-start; }
        .pricing-features li .check-icon { 
            display: flex; align-items: center; justify-content: center; 
            width: 24px; height: 24px; background: var(--gold-glow); border-radius: 50%; flex-shrink: 0; 
        }
        .pricing-features li .check-icon svg { width: 14px; height: 14px; stroke: var(--gold); stroke-width: 3; }
        
        .pricing-cta { 
            width: 100%; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: 600; 
            display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s var(--ease); 
        }
        .btn-secondary { background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        .btn-secondary:hover { border-color: var(--text-tertiary); background: var(--bg-card-hover); }
        
        .faq { background: var(--bg-secondary); border-top: 1px solid var(--border); }
        .faq-list { max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }
        .faq-item { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; transition: all 0.3s var(--ease); box-shadow: var(--shadow-sm); }
        .faq-item:hover { border-color: var(--border-hover); }
        .faq-question { width: 100%; padding: 24px; background: transparent; border: none; color: var(--text-primary); font-size: 17px; font-weight: 600; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 16px; font-family: inherit; }
        .faq-icon { flex-shrink: 0; color: var(--text-tertiary); transition: transform 0.3s var(--ease), color 0.3s; }
        .faq-item:hover .faq-icon { color: var(--text-primary); }
        .faq-item.active .faq-icon { transform: rotate(180deg); color: var(--gold); }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s var(--ease); }
        .faq-item.active .faq-answer { max-height: 500px; }
        .faq-answer-content { padding: 0 24px 24px; font-size: 15px; color: var(--text-secondary); line-height: 1.7; }
        
        .final-cta { text-align: center; background: linear-gradient(180deg, var(--bg-secondary) 0%, var(--bg-primary) 100%); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: calc(var(--spacing-3xl) * 1.5) 0; }
        .final-cta h2 { font-size: clamp(32px, 5vw, 56px); font-weight: 800; margin-bottom: var(--spacing-xl); max-width: 800px; margin-left: auto; margin-right: auto; letter-spacing: -1.5px; line-height: 1.1; }
        
        .footer-bottom { text-align: center; font-size: 14px; color: var(--text-tertiary); font-weight: 500; }
        
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero { padding-top: calc(var(--spacing-3xl) + 60px); }
            .hero h1 { font-size: 40px; }
            .hero-cta { flex-direction: column; width: 100%; padding: 0 20px; }
            .btn-hero { width: 100%; }
            .pricing-card.featured { transform: scale(1); }
            .pricing-card.featured:hover { transform: translateY(-8px); }
        }

        /* ── Glassmorphism Cards ── */
        .glass-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.04) inset, 0 8px 32px rgba(0,0,0,0.3);
            transition: all 0.4s var(--ease);
        }
        .glass-card:hover {
            border-color: rgba(255,255,255,0.16);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.07) inset, 0 16px 48px rgba(0,0,0,0.4);
            transform: translateY(-4px);
        }
        [data-theme="light"] .glass-card {
            background: rgba(255,255,255,0.7);
            border-color: rgba(0,0,0,0.08);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }

        /* ── How It Works ── */
        .how-section { padding: var(--spacing-3xl) 0; }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px; margin-top: var(--spacing-2xl);
            position: relative;
        }
        .step-card {
            padding: 32px 28px; position: relative;
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            transition: all 0.4s var(--ease);
            overflow: hidden;
        }
        .step-card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(59,130,246,0.06) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.4s;
        }
        .step-card:hover { border-color: rgba(59,130,246,0.3); transform: translateY(-4px); }
        .step-card:hover::before { opacity: 1; }
        [data-theme="light"] .step-card { background: rgba(255,255,255,0.8); border-color: rgba(0,0,0,0.08); }
        .step-number {
            font-size: 11px; font-weight: 800; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--gold); margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .step-number::before {
            content: ''; display: block; width: 6px; height: 6px;
            background: var(--gold); border-radius: 50%;
            box-shadow: 0 0 8px var(--gold);
        }
        .step-icon {
            width: 52px; height: 52px;
            background: rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 14px; display: flex;
            align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .step-title { font-size: 19px; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 10px; }
        .step-desc { font-size: 14px; color: var(--text-secondary); line-height: 1.65; }

        /* ── What You Get ── */
        .benefits-section { background: var(--bg-secondary); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: var(--spacing-3xl) 0; }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px; margin-top: var(--spacing-2xl);
        }
        .benefit-item {
            display: flex; gap: 16px; align-items: flex-start;
            padding: 22px 20px;
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            transition: all 0.3s var(--ease);
        }
        .benefit-item:hover {
            border-color: rgba(255,255,255,0.14);
            background: rgba(255,255,255,0.04);
            transform: translateX(4px);
        }
        [data-theme="light"] .benefit-item { background: rgba(255,255,255,0.8); border-color: rgba(0,0,0,0.07); }
        .benefit-icon-wrap {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid;
        }
        .benefit-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; letter-spacing: -0.02em; }
        .benefit-desc { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }

        /* ── Feature card upgrade ── */
        .feature-card {
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px; padding: var(--spacing-lg);
            transition: all 0.4s var(--ease);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.03) inset;
            position: relative; overflow: hidden;
        }
        .feature-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59,130,246,0.4), transparent);
            opacity: 0; transition: opacity 0.4s;
        }
        .feature-card:hover { transform: translateY(-6px); border-color: rgba(59,130,246,0.25); }
        .feature-card:hover::after { opacity: 1; }
        [data-theme="light"] .feature-card { background: rgba(255,255,255,0.7); border-color: rgba(0,0,0,0.08); }

        /* ── Technologies ── */
        .tech-section { padding: var(--spacing-3xl) 0; }
        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px; margin-top: var(--spacing-2xl);
        }
        .tech-card {
            padding: 32px 28px; position: relative; text-align: left;
        }
        .tech-icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; border: 1px solid;
        }
        .tech-name { font-size: 20px; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 10px; color: var(--text-primary); }
        .tech-desc { font-size: 14px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 16px; }
        .tech-tag {
            display: inline-block; padding: 4px 12px;
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 100px; font-size: 11px; font-weight: 600;
            color: var(--text-tertiary); letter-spacing: 0.05em; text-transform: uppercase;
        }
        [data-theme="light"] .tech-tag { background: rgba(0,0,0,0.04); border-color: rgba(0,0,0,0.08); }
    </style>

</head>
<body>

    <div class="bg-pattern"></div>

    <div class="bg-effects">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>

    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">
                    <div class="logo-icon">MV</div>
                    <div class="logo-text">
                        <div class="logo-brand" data-i18n="logo.brand">MercuryVision Studio</div>
                        <div class="logo-descriptor" data-i18n="logo.descriptor">Artificial Satellite Intelligence</div>
                    </div>
                </a>
                <nav>
                    <div class="nav-links">
                        <a href="#how-it-works" data-i18n="nav.how">How It Works</a>
                        <a href="#technologies" data-i18n="nav.tech">Technologies</a>
                        <a href="#features" data-i18n="nav.features">Features</a>
                        <a href="#pricing" data-i18n="nav.pricing">Pricing</a>
                        <a href="#faq" data-i18n="nav.faq">FAQ</a>
                    </div>
                    <div class="nav-actions">
                        <div class="lang-switcher">
                            <button class="lang-btn active" data-lang="en">EN</button>
                            <button class="lang-btn" data-lang="ru">RU</button>
                            <button class="lang-btn" data-lang="kz">KZ</button>
                        </div>
                        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                            <i data-lucide="sun" width="20" height="20"></i>
                        </button>
                        <a href="/auth" class="btn-primary">
                            <span data-i18n="cta.start_free">Start Free</span>
                            <i data-lucide="arrow-right" width="16" height="16"></i>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
     <div class="hero-badge">
    <span class="icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide-icon">
            <path d="M13 7 9 3 5 7l4 4"/><path d="m17 11 4 4-4 4-4-4"/><path d="m8 12 4 4 6-6-4-4Z"/><path d="m16 8 3-3"/><path d="M9 21a6 6 0 0 0-6-6"/>
        </svg>
    </span> 
    <span data-i18n="hero.badge">AI POWERED CHANGE DETECTION </span>
</div>
            <h1>
                <span class="hero-gradient-text" data-i18n="hero.title_line1">AI-Powered Satellite</span><br>
                <span class="hero-gradient-text" data-i18n="hero.title_line2">Environmental Analysis</span>
            </h1>
            <p class="hero-subtitle" data-i18n="hero.subtitle">Enterprise-grade platform for real-time environmental monitoring. Transform satellite data into actionable strategic intelligence.</p>
            <div class="hero-cta">
                <a href="/auth" class="btn-hero btn-hero-primary">
                    <i data-lucide="rocket" width="20" height="20"></i>
                    <span data-i18n="cta.start_trial">Start Free Trial</span>
                </a>
                <a href="#features" class="btn-hero btn-hero-secondary">
                    <i data-lucide="play-circle" width="20" height="20"></i>
                    <span data-i18n="cta.explore">Explore Features</span>
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-value">99.9%</div>
                    <div class="stat-label" data-i18n="hero.stat1">Accuracy</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" data-i18n="hero.stat2_val">Real-time</div>
                    <div class="stat-label" data-i18n="hero.stat2">Analysis</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">10+</div>
                    <div class="stat-label" data-i18n="hero.stat3">AI Solutions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" data-i18n="hero.stat4_val">Enterprise</div>
                    <div class="stat-label" data-i18n="hero.stat4">Security</div>
                </div>
            </div>
        </div>
    </section>
<!-- How It Works -->
    <section class="how-section" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); border-radius:100px; font-size:11px; font-weight:700; color:var(--gold); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:20px;">
                    <i data-lucide="zap" width="12" height="12"></i> <span data-i18n="how.badge">How It Works</span>
                </div>
                <h2 class="section-title" style="letter-spacing:-0.04em;" data-i18n="how.title">From image to insight<br>in 3 steps</h2>
                <p class="section-subtitle" data-i18n="how.subtitle">No expertise required. Upload, analyze, and get actionable intelligence in minutes.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number" data-i18n="how.s1.num">Step 01</div>
                    <div class="step-icon"><i data-lucide="upload-cloud" width="22" height="22" style="color:#60a5fa;"></i></div>
                    <div class="step-title" data-i18n="how.s1.title">Upload Satellite Images</div>
                    <p class="step-desc" data-i18n="how.s1.desc">Drag & drop your before/after satellite imagery. Supports Sentinel-2, Landsat, and any GeoTIFF up to 50MB per image.</p>
                </div>
                <div class="step-card">
                    <div class="step-number" data-i18n="how.s2.num">Step 02</div>
                    <div class="step-icon"><i data-lucide="cpu" width="22" height="22" style="color:#c084fc;"></i></div>
                    <div class="step-title" data-i18n="how.s2.title">Select Analysis Mode</div>
                    <p class="step-desc" data-i18n="how.s2.desc">Choose from 5 specialized AI modes — water dynamics, forest health, NDVI, urban growth, or general change detection.</p>
                </div>
                <div class="step-card">
                    <div class="step-number" data-i18n="how.s3.num">Step 03</div>
                    <div class="step-icon"><i data-lucide="bar-chart-3" width="22" height="22" style="color:#4ade80;"></i></div>
                    <div class="step-title" data-i18n="how.s3.title">Get Deep Intelligence</div>
                    <p class="step-desc" data-i18n="how.s3.desc">Receive pixel-level change maps, quantified metrics, and AI-generated expert reports ready for export or sharing.</p>
                </div>
                <div class="step-card">
                    <div class="step-number" data-i18n="how.s4.num">Step 04</div>
                    <div class="step-icon"><i data-lucide="share-2" width="22" height="22" style="color:#fb923c;"></i></div>
                    <div class="step-title" data-i18n="how.s4.title">Export & Share</div>
                    <p class="step-desc" data-i18n="how.s4.desc">Download professional PDF reports, raw CSV data, or share results directly. All projects saved to your dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Technologies -->
    <section class="tech-section" id="technologies">
        <div class="container">
            <div class="section-header">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(192,132,252,0.08); border:1px solid rgba(192,132,252,0.2); border-radius:100px; font-size:11px; font-weight:700; color:#c084fc; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:20px;">
                    <i data-lucide="cpu" width="12" height="12"></i> <span data-i18n="tech.badge">Core Technologies</span>
                </div>
                <h2 class="section-title" style="letter-spacing:-0.04em;" data-i18n="tech.title">Powered by cutting-edge<br>AI & space technology</h2>
                <p class="section-subtitle" data-i18n="tech.subtitle">Enterprise-grade infrastructure built on proven scientific frameworks and state-of-the-art neural networks.</p>
            </div>
            <div class="tech-grid">
                <div class="glass-card tech-card">
                    <div class="tech-icon" style="background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.25);">
                        <i data-lucide="brain" width="28" height="28" style="color:#60a5fa;"></i>
                    </div>
                    <h3 class="tech-name" data-i18n="tech.t1.title">ChangeFormer</h3>
                    <p class="tech-desc" data-i18n="tech.t1.desc">Transformer-based neural network for pixel-level change detection. Validated across 50+ biome types with 99% accuracy.</p>
                    <div class="tech-tag" data-i18n="tech.t1.tag">Deep Learning</div>
                </div>
                <div class="glass-card tech-card">
                    <div class="tech-icon" style="background:rgba(192,132,252,0.1); border-color:rgba(192,132,252,0.25);">
                        <i data-lucide="sparkles" width="28" height="28" style="color:#c084fc;"></i>
                    </div>
                    <h3 class="tech-name" data-i18n="tech.t2.title">GPT-4o & Gemini</h3>
                    <p class="tech-desc" data-i18n="tech.t2.desc">Multi-model AI architecture for expert-level analysis reports. Automatic language detection and geospatial reasoning.</p>
                    <div class="tech-tag" data-i18n="tech.t2.tag">Generative AI</div>
                </div>
                <div class="glass-card tech-card">
                    <div class="tech-icon" style="background:rgba(74,222,128,0.1); border-color:rgba(74,222,128,0.25);">
                        <i data-lucide="satellite" width="28" height="28" style="color:#4ade80;"></i>
                    </div>
                    <h3 class="tech-name" data-i18n="tech.t3.title">Sentinel-2</h3>
                    <p class="tech-desc" data-i18n="tech.t3.desc">Direct integration with ESA Copernicus satellite constellation. 10m resolution multispectral imagery updated every 5 days.</p>
                    <div class="tech-tag" data-i18n="tech.t3.tag">Earth Observation</div>
                </div>
                <div class="glass-card tech-card">
                    <div class="tech-icon" style="background:rgba(251,146,60,0.1); border-color:rgba(251,146,60,0.25);">
                        <i data-lucide="cog" width="28" height="28" style="color:#fb923c;"></i>
                    </div>
                    <h3 class="tech-name" data-i18n="tech.t4.title">GPU Inference</h3>
                    <p class="tech-desc" data-i18n="tech.t4.desc">CUDA-accelerated processing pipeline with automatic fallback. NumPy, PIL, and GD-based analysis for maximum compatibility.</p>
                    <div class="tech-tag" data-i18n="tech.t4.tag">Infrastructure</div>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title" data-i18n="features.title">Comprehensive Intelligence Suite</h2>
                <p class="section-subtitle" data-i18n="features.subtitle">10 AI-powered analysis modules delivering enterprise-grade environmental intelligence</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon icon-blue"><i data-lucide="git-compare"></i></div>
                    <h3 class="feature-title" data-i18n="features.f1.title">AI Compare</h3>
                    <p class="feature-desc" data-i18n="features.f1.desc">Advanced change detection with pixel-level accuracy and temporal analysis</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-blue"><i data-lucide="droplet"></i></div>
                    <h3 class="feature-title" data-i18n="features.f2.title">Water Dynamics</h3>
                    <p class="feature-desc" data-i18n="features.f2.desc">Real-time tracking of water bodies, coverage patterns, and hydrological changes</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-green"><i data-lucide="trees"></i></div>
                    <h3 class="feature-title" data-i18n="features.f3.title">Forest Analytics</h3>
                    <p class="feature-desc" data-i18n="features.f3.desc">Monitor deforestation, afforestation, and forest health with precision mapping</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-green"><i data-lucide="leaf"></i></div>
                    <h3 class="feature-title" data-i18n="features.f4.title">NDVI Intelligence</h3>
                    <p class="feature-desc" data-i18n="features.f4.desc">Vegetation health assessment with biomass estimation and crop monitoring</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-orange"><i data-lucide="sprout"></i></div>
                    <h3 class="feature-title" data-i18n="features.f5.title">Soil Moisture</h3>
                    <p class="feature-desc" data-i18n="features.f5.desc">Advanced soil water content analysis for agriculture and drought prediction</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-orange"><i data-lucide="alert-triangle"></i></div>
                    <h3 class="feature-title" data-i18n="features.f6.title">Risk Assessment</h3>
                    <p class="feature-desc" data-i18n="features.f6.desc">Predictive land degradation models with early warning systems</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-purple"><i data-lucide="map"></i></div>
                    <h3 class="feature-title" data-i18n="features.f7.title">Fragmentation</h3>
                    <p class="feature-desc" data-i18n="features.f7.desc">Landscape connectivity analysis for biodiversity and ecosystem health</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-purple"><i data-lucide="building-2"></i></div>
                    <h3 class="feature-title" data-i18n="features.f8.title">Urban Intelligence</h3>
                    <p class="feature-desc" data-i18n="features.f8.desc">Track urbanization patterns, infrastructure growth, and city planning</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-pink"><i data-lucide="file-text"></i></div>
                    <h3 class="feature-title" data-i18n="features.f9.title">Enterprise Reports</h3>
                    <p class="feature-desc" data-i18n="features.f9.desc">Automated PDF/CSV reports with customizable templates and branding</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon icon-pink"><i data-lucide="bot"></i></div>
                    <h3 class="feature-title" data-i18n="features.f10.title">AI Assistant</h3>
                    <p class="feature-desc" data-i18n="features.f10.desc">Natural language interface for data queries and intelligent insights</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- What You Get -->
    <section class="benefits-section" id="benefits">
        <div class="container">
            <div class="section-header">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); border-radius:100px; font-size:11px; font-weight:700; color:var(--gold); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:20px;">
                    <i data-lucide="star" width="12" height="12"></i> <span data-i18n="benefits.badge">What You Get</span>
                </div>
                <h2 class="section-title" style="letter-spacing:-0.04em;" data-i18n="benefits.title">Everything you need<br>for satellite intelligence</h2>
                <p class="section-subtitle" data-i18n="benefits.subtitle">Built for researchers, environmental agencies, and enterprises who need real answers from Earth observation data.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(96,165,250,0.1); border-color:rgba(96,165,250,0.25);">
                        <i data-lucide="layers" width="18" height="18" style="color:#60a5fa;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b1.title">Multi-Temporal Analysis</div>
                        <div class="benefit-desc" data-i18n="benefits.b1.desc">Compare any two points in time to quantify environmental change with pixel precision.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(192,132,252,0.1); border-color:rgba(192,132,252,0.25);">
                        <i data-lucide="bot" width="18" height="18" style="color:#c084fc;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b2.title">GPT-4o & Gemini Reports</div>
                        <div class="benefit-desc" data-i18n="benefits.b2.desc">AI-generated expert-level analysis reports in plain language. No satellite expertise needed.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(74,222,128,0.1); border-color:rgba(74,222,128,0.25);">
                        <i data-lucide="shield-check" width="18" height="18" style="color:#4ade80;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b3.title">99% Accuracy</div>
                        <div class="benefit-desc" data-i18n="benefits.b3.desc">ChangeFormer neural network validated against ground truth across 50+ biome types.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(251,146,60,0.1); border-color:rgba(251,146,60,0.25);">
                        <i data-lucide="file-down" width="18" height="18" style="color:#fb923c;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b4.title">PDF & CSV Export</div>
                        <div class="benefit-desc" data-i18n="benefits.b4.desc">Professional reports with your branding, ready to share with stakeholders immediately.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(244,114,182,0.1); border-color:rgba(244,114,182,0.25);">
                        <i data-lucide="folder-open" width="18" height="18" style="color:#f472b6;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b5.title">Project History</div>
                        <div class="benefit-desc" data-i18n="benefits.b5.desc">All analyses saved and searchable. Filter by mode, date, or region. Never lose a result.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(96,165,250,0.1); border-color:rgba(96,165,250,0.25);">
                        <i data-lucide="zap" width="18" height="18" style="color:#60a5fa;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b6.title">Real-time Processing</div>
                        <div class="benefit-desc" data-i18n="benefits.b6.desc">GPU-accelerated inference delivers results in seconds, not hours. No queue waiting.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(74,222,128,0.1); border-color:rgba(74,222,128,0.25);">
                        <i data-lucide="key-round" width="18" height="18" style="color:#4ade80;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b7.title">BYO API Key</div>
                        <div class="benefit-desc" data-i18n="benefits.b7.desc">Use your own OpenAI or Gemini key for unlimited AI reports with no platform markup.</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-wrap" style="background:rgba(251,146,60,0.1); border-color:rgba(251,146,60,0.25);">
                        <i data-lucide="globe" width="18" height="18" style="color:#fb923c;"></i>
                    </div>
                    <div>
                        <div class="benefit-title" data-i18n="benefits.b8.title">3 Languages</div>
                        <div class="benefit-desc" data-i18n="benefits.b8.desc">Full UI in English, Russian, and Kazakh. Reports generated in the language you choose.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>    <!-- Pricing -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(251,146,60,0.08); border:1px solid rgba(251,146,60,0.2); border-radius:100px; font-size:11px; font-weight:700; color:#fb923c; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:20px;">
                    <i data-lucide="credit-card" width="12" height="12"></i> <span data-i18n="nav.pricing">Pricing</span>
                </div>
                <h2 class="section-title" data-i18n="pricing.title" style="letter-spacing:-0.04em; margin-bottom:16px;">Enterprise Pricing</h2>
                <p class="section-subtitle" data-i18n="pricing.subtitle" style="font-size:18px; line-height:1.6; max-width:600px; margin:0 auto; color:var(--text-secondary);">Start with our free tier. Scale as you grow. Upgrade seamlessly via Telegram.</p>
            </div>
            
            <div class="pricing-grid">
                <!-- FREE TIER -->
                <div class="pricing-card">
                    <div class="pricing-name" style="text-transform:uppercase; font-size:18px;">FREE</div>
                    <div class="pricing-price-wrapper"><div class="pricing-price">0₸</div></div>
                    <span class="pricing-usd" style="visibility:hidden;">$0 / 0₽</span>
                    
                    <ul class="pricing-features">
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> 75 analysis credits/month</li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> Basic AI Solutions</li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> Water + Forest modes</li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> No API Access</li>
                    </ul>
                    <a href="/auth" class="btn-hero-secondary" style="width:100%; justify-content:center; padding:12px; margin-top:auto;">Start For Free</a>
                </div>

                <!-- LITE TIER -->
                <div class="pricing-card">
                    <div class="pricing-name" style="text-transform:uppercase; font-size:18px;">LITE</div>
                    <div class="pricing-price-wrapper">
                        <div class="pricing-price">3,999₸</div>
                        <div class="pricing-original">5,999₸</div>
                    </div>
                    <span class="pricing-usd">$8.50 / 799₽ per month</span>
                    
                    <ul class="pricing-features">
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.lite.f1">15,000 analysis credits/month</span></li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.lite.f2">3 AI solutions</span></li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.lite.f3">Water + Forest analysis modes</span></li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.lite.f4">Core metrics dashboard</span></li>
                    </ul>
                    <a href="/auth" class="btn-hero-secondary" style="width:100%; justify-content:center; padding:12px; margin-top:auto;"><span data-i18n="pricing.lite.cta">Get Started</span></a>
                </div>

                <!-- STANDARD TIER -->
                <div class="pricing-card featured">
                    <div class="pricing-badge" data-i18n="pricing.standard.badge">MOST POPULAR</div>
                    <div class="pricing-name" style="text-transform:uppercase; font-size:18px;">STANDARD</div>
                    <div class="pricing-price-wrapper">
                        <div class="pricing-price">11,999₸</div>
                        <div class="pricing-original">17,999₸</div>
                    </div>
                    <span class="pricing-usd">$24.99 / 2,249₽ per month</span>
                    
                    <ul class="pricing-features">
                        <li><div class="check-icon" style="background:var(--gold);"><i data-lucide="check" width="14" height="14" style="color:#fff;"></i></div> <span data-i18n="pricing.standard.f1">60,000 analysis credits</span></li>
                        <li><div class="check-icon" style="background:var(--gold);"><i data-lucide="check" width="14" height="14" style="color:#fff;"></i></div> <span data-i18n="pricing.standard.f2">6 advanced AI solutions</span></li>
                        <li><div class="check-icon" style="background:var(--gold);"><i data-lucide="check" width="14" height="14" style="color:#fff;"></i></div> <span data-i18n="pricing.standard.f3">All premium analysis modes</span></li>
                        <li><div class="check-icon" style="background:var(--gold);"><i data-lucide="check" width="14" height="14" style="color:#fff;"></i></div> <span data-i18n="pricing.standard.f4">Real-time monitoring dashboard</span></li>
                    </ul>
                    <a href="/auth" class="btn-primary" style="width:100%; justify-content:center; padding:12px; margin-top:auto;"><span data-i18n="pricing.standard.cta">Get Standard</span></a>
                </div>

                <!-- PRO TIER -->
                <div class="pricing-card">
                    <div class="pricing-name" style="text-transform:uppercase; font-size:18px;">PRO</div>
                    <div class="pricing-price-wrapper">
                        <div class="pricing-price">35,999₸</div>
                        <div class="pricing-original">53,999₸</div>
                    </div>
                    <span class="pricing-usd">$74.99 / 6,749₽ per month</span>
                    
                    <ul class="pricing-features">
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.pro.f1">500,000 analysis credits</span></li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.pro.f2">10 enterprise solutions</span></li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.pro.f4">Full API access + webhooks</span></li>
                        <li><div class="check-icon"><i data-lucide="check" width="14" height="14"></i></div> <span data-i18n="pricing.pro.f5">Priority processing + 24/7 support</span></li>
                    </ul>
                    <a href="/auth" class="btn-hero-secondary" style="width:100%; justify-content:center; padding:12px; margin-top:auto;"><span data-i18n="pricing.pro.cta">Get Pro</span></a>
                </div>
            </div>
            
        </div>
    </section>

    <section class="faq" id="faq">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title" data-i18n="faq.title">Frequently Asked Questions</h2>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span data-i18n="faq.q1.q">How accurate are the analysis results?</span>
                        <i class="faq-icon" data-lucide="chevron-down" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content" data-i18n="faq.q1.a">Our AI models achieve 99% accuracy in change detection using advanced pixel-based classification. Results are analytical proxies optimized for environmental intelligence, validated against ground truth data and peer-reviewed methodologies.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span data-i18n="faq.q2.q">What satellite data sources do you use?</span>
                        <i class="faq-icon" data-lucide="chevron-down" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content" data-i18n="faq.q2.a">We integrate multi-source satellite imagery including Sentinel-2, Landsat-8/9, MODIS, and commercial providers. Our platform automatically selects optimal data based on resolution requirements, temporal coverage, and analysis objectives.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span data-i18n="faq.q3.q">Why is email verification required?</span>
                        <i class="faq-icon" data-lucide="chevron-down" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content" data-i18n="faq.q3.a">Email verification ensures workspace security, data integrity, and account recovery. It also enables us to deliver analysis results, important updates, and maintain compliance with enterprise security standards.</div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span data-i18n="faq.q4.q">How does the upgrade process work?</span>
                        <i class="faq-icon" data-lucide="chevron-down" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content" data-i18n="faq.q4.a">Payments are processed securely through our Telegram bot integration. Plan activation typically occurs within 1-2 hours during business hours. You'll receive instant confirmation and can start using premium features immediately upon activation.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section style="text-align:center; padding: calc(var(--spacing-3xl) * 1.2) 0; border-top:1px solid var(--border); background:linear-gradient(180deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);">
        <div class="container-narrow">
            <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); border-radius:100px; font-size:11px; font-weight:700; color:var(--gold); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:24px;">
                <i data-lucide="rocket" width="12" height="12"></i> <span data-i18n="cta.start_today">Start Today</span>
            </div>
            <h2 style="font-size:clamp(32px,5vw,52px); font-weight:800; letter-spacing:-0.04em; line-height:1.1; margin-bottom:20px;" data-i18n="cta.transform_title">Transform satellite data<br>into actionable intelligence</h2>
            <p style="font-size:17px; color:var(--text-secondary); max-width:520px; margin:0 auto 36px; line-height:1.6;" data-i18n="cta.transform_desc">Join researchers and agencies already using MercuryVision Studio to monitor our changing planet.</p>
            <a href="/auth" class="btn-hero btn-hero-primary" style="margin:0 auto;">
                <i data-lucide="rocket" width="20" height="20"></i>
                <span data-i18n="cta.start_trial">Start Free Trial</span>
            </a>
        </div>
    </section>

    <footer style="padding: 40px 0; background: var(--bg-primary); border-top: 1px solid var(--border);">
        <div class="container">
            <div style="text-align: center; font-size: 14px; color: var(--text-tertiary); font-weight: 500;">
                &copy; <?= date('Y'); ?> MercuryVision Studio. All rights reserved.
            </div>
        </div>
    </footer>


    <script>
        lucide.createIcons();
        <?php $copyYear = date('Y'); ?>
        const translations = {
            en: {
                logo: { brand: 'MercuryVision Studio', descriptor: 'AI POWERED CHANGE DETECTION' },
                nav: { how: 'How It Works', tech: 'Technologies', features: 'Features', pricing: 'Pricing', faq: 'FAQ' },
                tech: { badge: 'Core Technologies', title: 'Powered by cutting-edge AI & space technology', subtitle: 'Enterprise-grade infrastructure built on proven scientific frameworks and state-of-the-art neural networks.', t1: { title: 'ChangeFormer', desc: 'Transformer-based neural network for pixel-level change detection. Validated across 50+ biome types with 99% accuracy.', tag: 'Deep Learning' }, t2: { title: 'GPT-4o & Gemini', desc: 'Multi-model AI architecture for expert-level analysis reports. Automatic language detection and geospatial reasoning.', tag: 'Generative AI' }, t3: { title: 'Sentinel-2', desc: 'Direct integration with ESA Copernicus satellite constellation. 10m resolution multispectral imagery updated every 5 days.', tag: 'Earth Observation' }, t4: { title: 'GPU Inference', desc: 'CUDA-accelerated processing pipeline with automatic fallback. NumPy, PIL, and GD-based analysis for maximum compatibility.', tag: 'Infrastructure' } },
                cta: { start_today: 'Start Today', transform_title: 'Transform satellite data into actionable intelligence', transform_desc: 'Join researchers and agencies already using MercuryVision Studio to monitor our changing planet.', start_free: 'Start Free', start_trial: 'Start Free Trial', explore: 'Explore Features' },
                hero: { badge: ' AI POWERED CHANGE DETECTION', title_line1: 'AI-Powered Satellite', title_line2: 'Environmental Analysis', subtitle: 'Enterprise-grade platform for real-time environmental monitoring. Transform satellite data into actionable strategic intelligence.', stat1: 'Accuracy', stat2_val: 'Real-time', stat2: 'Analysis', stat3: 'AI Solutions', stat4_val: 'Enterprise', stat4: 'Security' },
                how: {
                    badge: 'How It Works',
                    title: 'From image to insight in 3 steps',
                    subtitle: 'No expertise required. Upload, analyze, and get actionable intelligence in minutes.',
                    s1: { num: 'Step 01', title: 'Upload Satellite Images', desc: 'Drag & drop your before/after satellite imagery. Supports Sentinel-2, Landsat, and any GeoTIFF up to 50MB per image.' },
                    s2: { num: 'Step 02', title: 'Select Analysis Mode', desc: 'Choose from 5 specialized AI modes — water dynamics, forest health, NDVI, urban growth, or general change detection.' },
                    s3: { num: 'Step 03', title: 'Get Deep Intelligence', desc: 'Receive pixel-level change maps, quantified metrics, and AI-generated expert reports ready for export or sharing.' },
                    s4: { num: 'Step 04', title: 'Export & Share', desc: 'Download professional PDF reports, raw CSV data, or share results directly. All projects saved to your dashboard.' }
                },
                benefits: {
                    badge: 'What You Get',
                    title: 'Everything you need for satellite intelligence',
                    subtitle: 'Built for researchers, environmental agencies, and enterprises who need real answers from Earth observation data.',
                    b1: { title: 'Multi-Temporal Analysis', desc: 'Compare any two points in time to quantify environmental change with pixel precision.' },
                    b2: { title: 'GPT-4o & Gemini Reports', desc: 'AI-generated expert-level analysis reports in plain language. No satellite expertise needed.' },
                    b3: { title: '99% Accuracy', desc: 'ChangeFormer neural network validated against ground truth across 50+ biome types.' },
                    b4: { title: 'PDF & CSV Export', desc: 'Professional reports with your branding, ready to share with stakeholders immediately.' },
                    b5: { title: 'Project History', desc: 'All analyses saved and searchable. Filter by mode, date, or region. Never lose a result.' },
                    b6: { title: 'Real-time Processing', desc: 'GPU-accelerated inference delivers results in seconds, not hours. No queue waiting.' },
                    b7: { title: 'BYO API Key', desc: 'Use your own OpenAI or Gemini key for unlimited AI reports with no platform markup.' },
                    b8: { title: '3 Languages', desc: 'Full UI in English, Russian, and Kazakh. Reports generated in the language you choose.' }
                },
                features: { title: 'Comprehensive Intelligence Suite', subtitle: '10 AI-powered analysis modules delivering enterprise-grade environmental intelligence', f1: { title: 'AI Compare', desc: 'Advanced change detection with pixel-level accuracy and temporal analysis' }, f2: { title: 'Water Dynamics', desc: 'Real-time tracking of water bodies, coverage patterns, and hydrological changes' }, f3: { title: 'Forest Analytics', desc: 'Monitor deforestation, afforestation, and forest health with precision mapping' }, f4: { title: 'NDVI Intelligence', desc: 'Vegetation health assessment with biomass estimation and crop monitoring' }, f5: { title: 'Soil Moisture', desc: 'Advanced soil water content analysis for agriculture and drought prediction' }, f6: { title: 'Risk Assessment', desc: 'Predictive land degradation models with early warning systems' }, f7: { title: 'Fragmentation', desc: 'Landscape connectivity analysis for biodiversity and ecosystem health' }, f8: { title: 'Urban Intelligence', desc: 'Track urbanization patterns, infrastructure growth, and city planning' }, f9: { title: 'Enterprise Reports', desc: 'Automated PDF/CSV reports with customizable templates and branding' }, f10: { title: 'AI Assistant', desc: 'Natural language interface for data queries and intelligent insights' } },
                pricing: { title: 'Enterprise Pricing', subtitle: 'Start with our free tier. Scale as you grow. Upgrade seamlessly via Telegram.', lite: { f1: '15,000 analysis credits/month', f2: '3 AI-powered solutions', f3: 'Water + Forest analysis modes', f4: 'Core metrics dashboard', f5: 'Email support (48h response)', cta: 'Get Started' }, standard: { badge: 'MOST POPULAR', f1: '60,000 analysis credits', f2: '6 advanced AI solutions', f3: 'All premium analysis modes', f4: 'Real-time monitoring dashboard', f5: 'PDF + CSV export with branding', cta: 'Get Standard' }, pro: { f1: '500,000 analysis credits', f2: '10 enterprise solutions', f3: 'Team collaboration workspace', f4: 'Full API access + webhooks', f5: 'Priority processing + 24/7 support', cta: 'Get Pro' } },
                faq: { title: 'Frequently Asked Questions', q1: { q: 'How accurate are the analysis results?', a: 'Our AI models achieve 99% accuracy in change detection using advanced pixel-based classification. Results are analytical proxies optimized for environmental intelligence, validated against ground truth data and peer-reviewed methodologies.' }, q2: { q: 'What satellite data sources do you use?', a: 'We integrate multi-source satellite imagery including Sentinel-2, Landsat-8/9, MODIS, and commercial providers. Our platform automatically selects optimal data based on resolution requirements, temporal coverage, and analysis objectives.' }, q3: { q: 'Why is email verification required?', a: 'Email verification ensures workspace security, data integrity, and account recovery. It also enables us to deliver analysis results, important updates, and maintain compliance with enterprise security standards.' }, q4: { q: 'How does the upgrade process work?', a: 'Payments are processed securely through our Telegram bot integration. Plan activation typically occurs within 1-2 hours during business hours. You\'ll receive instant confirmation and can start using premium features immediately upon activation.' } },
                final: { title: 'Transform satellite data into actionable intelligence in minutes' },
                footer: { desc: 'Enterprise-grade AI platform for environmental intelligence and satellite analysis. Transform raw imagery into strategic environmental intelligence.', product: { title: 'Product', features: 'Features', pricing: 'Pricing', api: 'API Docs' }, legal: { title: 'Legal', terms: 'Terms', privacy: 'Privacy', security: 'Security' }, contact: { title: 'Connect', telegram: 'Telegram', email: 'Sales', support: 'Support' }, copyright: '© <?= $copyYear ?> MercuryVision Studio. All rights reserved.' }

            },
            ru: {
                logo: { brand: 'MercuryVision Studio', descriptor: 'ИИ СПУТНИКОВЫЙ АНАЛИЗ' },
                nav: { how: 'Как это работает', tech: 'Технологии', features: 'Возможности', pricing: 'Цены', faq: 'Вопросы' },
                tech: { badge: 'Технологии', title: 'На базе передовых AI и космических технологий', subtitle: 'Инфраструктура корпоративного уровня на основе проверенных научных фреймворков и нейросетей.', t1: { title: 'ChangeFormer', desc: 'Нейросеть на основе трансформера для пиксельного обнаружения изменений. Проверена в 50+ типах биомов с точностью 99%.', tag: 'Глубокое обучение' }, t2: { title: 'GPT-4o и Gemini', desc: 'Мультимодельная AI архитектура для экспертных отчетов анализа. Автоматическое определение языка и геопространственное мышление.', tag: 'Генеративный ИИ' }, t3: { title: 'Sentinel-2', desc: 'Прямая интеграция с группировкой спутников ESA Copernicus. Мультиспектральные снимки 10м разрешения каждые 5 дней.', tag: 'Наблюдение Земли' }, t4: { title: 'GPU Инференс', desc: 'Ускоренный CUDA конвейер обработки с автоматическим fallback. NumPy, PIL и GD для максимальной совместимости.', tag: 'Инфраструктура' } },
                cta: { start_today: 'Начните сегодня', transform_title: 'Превращайте спутниковые данные в практические знания', transform_desc: 'Присоединяйтесь к исследователям и агентствам, которые уже используют MercuryVision Studio для мониторинга планеты.', start_free: 'Начать', start_trial: 'Начать бесплатно', explore: 'Узнать больше' },
                hero: { badge: ' ИИ СПУТНИКОВЫЙ АНАЛИЗ', title_line1: 'ИИ Спутниковый', title_line2: 'Экологический Анализ', subtitle: 'Платформа корпоративного уровня для мониторинга окружающей среды в реальном времени. Преобразуйте спутниковые данные в стратегическую аналитику.', stat1: 'Точность', stat2_val: 'Реальное время', stat2: 'Анализ', stat3: 'ИИ Решений', stat4_val: 'Корпоративная', stat4: 'Безопасность' },
                how: {
                    badge: 'Как это работает',
                    title: 'От снимка к аналитике за 3 шага',
                    subtitle: 'Специальные знания не требуются. Загружайте, анализируйте и получайте результаты за считанные минуты.',
                    s1: { num: 'Шаг 01', title: 'Загрузка снимков', desc: 'Перетащите ваши спутниковые снимки «до» и «после». Поддержка Sentinel-2, Landsat и GeoTIFF до 50 МБ.' },
                    s2: { num: 'Шаг 02', title: 'Выбор режима', desc: 'Выберите один из 5 специализированных ИИ-режимов: динамика воды, здоровье лесов, NDVI, рост городов или общее обнаружение изменений.' },
                    s3: { num: 'Шаг 03', title: 'Глубокая аналитика', desc: 'Получайте карты изменений на уровне пикселей, количественные метрики и экспертные отчеты, созданные ИИ.' },
                    s4: { num: 'Шаг 04', title: 'Экспорт и обмен', desc: 'Скачивайте профессиональные PDF-отчеты, данные в CSV или делитесь результатами напрямую. Все проекты сохраняются в панели.' }
                },
                benefits: {
                    badge: 'Что вы получаете',
                    title: 'Все необходимое для спутниковой аналитики',
                    subtitle: 'Создано для исследователей, ведомств и компаний, которым нужны точные ответы на основе данных дистанционного зондирования Земли.',
                    b1: { title: 'Мультитемпоральный анализ', desc: 'Сравнивайте любые два момента времени для количественной оценки изменений с точностью до пикселя.' },
                    b2: { title: 'Отчеты GPT-4o и Gemini', desc: 'Экспертные аналитические отчеты от ИИ на простом языке. Знание специфики спутников не требуется.' },
                    b3: { title: 'Точность 99%', desc: 'Нейросеть ChangeFormer, проверенная на реальных данных в более чем 50 типах биомов.' },
                    b4: { title: 'Экспорт в PDF и CSV', desc: 'Профессиональные отчеты с вашим брендингом, готовые к отправке заинтересованным сторонам.' },
                    b5: { title: 'История проектов', desc: 'Все анализы сохраняются. Фильтруйте по режиму, дате или региону. Ни один результат не будет потерян.' },
                    b6: { title: 'Обработка в реальном времени', desc: 'Ускорение на GPU обеспечивает результаты за секунды, а не часы. Без очередей.' },
                    b7: { title: 'Свой API ключ', desc: 'Используйте свой ключ OpenAI или Gemini для неограниченных отчетов без наценки платформы.' },
                    b8: { title: '3 языка', desc: 'Интерфейс на английском, русском и казахском. Отчеты создаются на выбранном вами языке.' }
                },
                features: { title: 'Полный набор инструментов', subtitle: '10 модулей анализа на базе ИИ для корпоративной экологической аналитики', f1: { title: 'ИИ Сравнение', desc: 'Продвинутое обнаружение изменений с точностью до пикселя' }, f2: { title: 'Водная Динамика', desc: 'Отслеживание водоемов и гидрологических изменений в реальном времени' }, f3: { title: 'Лесная Аналитика', desc: 'Мониторинг вырубки, лесовосстановления и здоровья лесов' }, f4: { title: 'NDVI Анализ', desc: 'Оценка здоровья растительности и мониторинг сельхозкультур' }, f5: { title: 'Влажность Почвы', desc: 'Продвинутый анализ влаги для сельского хозяйства' }, f6: { title: 'Оценка Рисков', desc: 'Прогнозные модели деградации земель с системой раннего предупреждения' }, f7: { title: 'Фрагментация', desc: 'Анализ связности ландшафта для биоразнообразия' }, f8: { title: 'Городской Анализ', desc: 'Отслеживание урбанизации и развития инфраструктуры' }, f9: { title: 'Корп. Отчеты', desc: 'Автоматические PDF/CSV отчеты с настраиваемыми шаблонами' }, f10: { title: 'ИИ Ассистент', desc: 'Интерфейс на естественном языке для запросов данных' } },
                pricing: { title: 'Корпоративные тарифы', subtitle: 'Начните с бесплатного тарифа. Растите по мере необходимости. Обновление через Telegram.', lite: { f1: '15,000 кредитов анализа/месяц', f2: '3 ИИ решения', f3: 'Анализ воды + леса', f4: 'Базовая панель метрик', f5: 'Email поддержка (48ч ответ)', cta: 'Начать' }, standard: { badge: 'ПОПУЛЯРНЫЙ', f1: '60,000 кредитов анализа', f2: '6 продвинутых ИИ решений', f3: 'Все премиум режимы анализа', f4: 'Панель мониторинга реального времени', f5: 'Экспорт PDF + CSV с брендингом', cta: 'Получить Standard' }, pro: { f1: '500,000 кредитов анализа', f2: '10 корпоративных решений', f3: 'Командное рабочее пространство', f4: 'Полный API доступ + вебхуки', f5: 'Приоритетная обработка + 24/7 поддержка', cta: 'Получить Pro' } },
                faq: { title: 'Частые вопросы', q1: { q: 'Насколько точны результаты анализа?', a: 'Наши ИИ модели достигают 99% точности в обнаружении изменений, используя продвинутую пиксельную классификацию. Результаты проверены по эталонным данным и научным методологиям.' }, q2: { q: 'Какие источники спутниковых данных вы используете?', a: 'Мы интегрируем многоисточниковые спутниковые изображения включая Sentinel-2, Landsat-8/9, MODIS и коммерческие провайдеры. Платформа автоматически выбирает оптимальные данные.' }, q3: { q: 'Зачем требуется подтверждение email?', a: 'Подтверждение email обеспечивает безопасность рабочего пространства, целостность данных и восстановление аккаунта. Это также позволяет доставлять результаты анализа и важные обновления.' }, q4: { q: 'Как работает процесс обновления тарифа?', a: 'Платежи обрабатываются безопасно через интеграцию с Telegram ботом. Активация тарифа обычно происходит в течение 1-2 часов в рабочее время. Вы получите мгновенное подтверждение.' } },
                final: { title: 'Преобразуйте спутниковые данные в практическую аналитику за минуты' },
                footer: { desc: 'Корпоративная ИИ платформа для экологической аналитики и спутникового анализа. Превращайте сырые данные в стратегические инсайты.', product: { title: 'Продукт', features: 'Возможности', pricing: 'Цены', api: 'API Документация' }, legal: { title: 'Юридическое', terms: 'Условия', privacy: 'Конфиденциальность', security: 'Безопасность' }, contact: { title: 'Связь', telegram: 'Telegram', email: 'Продажи', support: 'Поддержка' }, copyright: '© <?= $copyYear ?> MercuryVision Studio. Все права защищены.' }

            },
            kz: {
                logo: { brand: 'MercuryVision Studio', descriptor: 'AI СЕРІКТІК ТАЛДАУ' },
                nav: { how: 'Бұл қалай жұмыс істейді', tech: 'Технологиялар', features: 'Мүмкіндіктер', pricing: 'Бағалар', faq: 'Сұрақтар' },
                tech: { badge: 'Технологиялар', title: 'Озық AI және ғарыш технологиялары негізінде', subtitle: 'Дәлелденген ғылыми негіздер мен заманауи нейрожелілерге құрылған кәсіпорын деңгейіндегі инфрақұрылым.', t1: { title: 'ChangeFormer', desc: 'Пиксельдік өзгерістерді анықтауға арналған трансформер негізіндегі нейрожелі. 50+ биом түрінде 99% дәлдікпен тексерілген.', tag: 'Терең оқыту' }, t2: { title: 'GPT-4o және Gemini', desc: 'Сараптамалық талдау есептеріне арналған көп модельді AI архитектурасы. Автоматты тіл анықтау және геокеңістіктік ойлау.', tag: 'Генеративті AI' }, t3: { title: 'Sentinel-2', desc: 'ESA Copernicus серіктік тобымен тікелей интеграция. 10м ажыратымдылығы бар мультиспектральды кескіндер 5 күн сайын жаңартылады.', tag: 'Жерді бақылау' }, t4: { title: 'GPU Инференс', desc: 'CUDA жеделдететін өңдеу конвейері автоматты fallback. NumPy, PIL және GD максималды үйлесімділік үшін.', tag: 'Инфрақұрылым' } },
                cta: { start_today: 'Бүгін бастаңыз', transform_title: 'Серіктік деректерді практикалық білімге айналдырыңыз', transform_desc: 'Планетаны бақылау үшін MercuryVision Studio қолданып жүрген зерттеушілер мен агенттіктерге қосылыңыз.', start_free: 'Бастау', start_trial: 'Тегін бастау', explore: 'Көбірек білу' },
                hero: { badge: ' AI СЕРІКТІК ТАЛДАУ', title_line1: 'AI Серіктік', title_line2: 'Экологиялық Талдау', subtitle: 'Қоршаған ортаны нақты уақытта бақылауға арналған кәсіпорын деңгейіндегі платформа. Серіктік деректерді стратегиялық аналитикаға айналдырыңыз.', stat1: 'Дәлдік', stat2_val: 'Нақты уақыт', stat2: 'Талдау', stat3: 'AI Шешімдер', stat4_val: 'Кәсіпорын', stat4: 'Қауіпсіздік' },
                how: {
                    badge: 'Бұл қалай жұмыс істейді',
                    title: 'Суреттен аналитикаға 3 қадамда',
                    subtitle: 'Арнайы білім қажет емес. Жүктеңіз, талдаңыз және бірнеше минут ішінде нәтиже алыңыз.',
                    s1: { num: '01 қадам', title: 'Суреттерді жүктеу', desc: 'Серіктік суреттерді «дейін» және «кейін» форматында жүктеңіз. Sentinel-2, Landsat және 50 МБ-қа дейінгі GeoTIFF қолдауы.' },
                    s2: { num: '02 қадам', title: 'Талдау режимін таңдау', desc: '5 мамандандырылған AI режимінің бірін таңдаңыз: су динамикасы, орман денсаулығы, NDVI, қала өсуі немесе жалпы өзгерістер.' },
                    s3: { num: '03 қадам', title: 'Терең аналитика', desc: 'Пиксель деңгейіндегі өзгерістер картасын, сандық метрикаларды және AI жасаған сараптамалық есептерді алыңыз.' },
                    s4: { num: '04 қадам', title: 'Экспорт және бөлісу', desc: 'Кәсіби PDF есептерін, CSV деректерін жүктеңіз немесе нәтижелермен тікелей бөлісіңіз. Барлық жобалар сақталады.' }
                },
                benefits: {
                    badge: 'Сіз не аласыз',
                    title: 'Серіктік талдау үшін қажеттінің бәрі',
                    subtitle: 'Жерді қашықтықтан зондтау деректері негізінде нақты жауаптар қажет зерттеушілер, мекемелер мен компаниялар үшін жасалған.',
                    b1: { title: 'Мультитемпоральды талдау', desc: 'Өзгерістерді пиксель дәлдігімен бағалау үшін кез келген екі уақыт нүктесін салыстырыңыз.' },
                    b2: { title: 'GPT-4o және Gemini есептері', desc: 'AI жасаған сараптамалық есептер қарапайым тілде. Серіктік білім қажет емес.' },
                    b3: { title: 'Дәлдік 99%', desc: '50-ден астам биом түрінде нақты деректермен расталған ChangeFormer нейрожелісі.' },
                    b4: { title: 'PDF және CSV экспорт', desc: 'Сіздің брендингіңізбен кәсіби есептер, мүдделі тараптарға жіберуге дайын.' },
                    b5: { title: 'Жобалар тарихы', desc: 'Барлық талдаулар сақталады. Режим, күн немесе аймақ бойынша сүзгіден өткізіңіз. Нәтижелер жоғалмайды.' },
                    b6: { title: 'Нақты уақытта өңдеу', desc: 'GPU үдеткіші нәтижелерді сағаттап емес, секундтарда береді. Кезексіз.' },
                    b7: { title: 'Өз API кілтіңіз', desc: 'Платформа үстемесінсіз шексіз есептер үшін өзіңіздің OpenAI немесе Gemini кілтіңізді пайдаланыңыз.' },
                    b8: { title: '3 тіл', desc: 'Ағылшын, орыс және қазақ тілдеріндегі интерфейс. Есептер сіз таңдаған тілде жасалады.' }
                },
                features: { title: 'Толық құралдар жиынтығы', subtitle: 'Кәсіпорын деңгейіндегі экологиялық аналитикаға арналған 10 AI талдау модулі', f1: { title: 'AI Салыстыру', desc: 'Пиксель дәлдігімен өзгерістерді анықтау' }, f2: { title: 'Су Динамикасы', desc: 'Су объектілері мен гидрологиялық өзгерістерді нақты уақытта қадағалау' }, f3: { title: 'Орман Аналитикасы', desc: 'Орман кесу, орман отырғызу және орман денсаулығын қадағалау' }, f4: { title: 'NDVI Талдау', desc: 'Өсімдік денсаулығын бағалау және егін қадағалау' }, f5: { title: 'Топырақ Ылғалдылығы', desc: 'Ауыл шаруашылығына арналған кеңейтілген ылғал талдау' }, f6: { title: 'Қауіп Бағалау', desc: 'Ерте ескерту жүйесімен жер деградациясының болжамды модельдері' }, f7: { title: 'Фрагментация', desc: 'Биоалуантүрлілік үшін ландшафт байланысын талдау' }, f8: { title: 'Қалалық Талдау', desc: 'Урбанизация және инфрақұрылым дамуын қадағалау' }, f9: { title: 'Кәсіпорын Есептері', desc: 'Реттелетін үлгілері бар автоматты PDF/CSV есептері' }, f10: { title: 'AI Көмекші', desc: 'Деректерге сұраулар үшін табиғи тіл интерфейсі' } },
                pricing: { title: 'Кәсіпорын тарифтері', subtitle: 'Тегін тарифпен бастаңыз. Қажеттілігіне қарай өсіңіз. Telegram арқылы жаңартыңыз.', lite: { f1: '15,000 талдау кредиттері/ай', f2: '3 AI шешімдері', f3: 'Су + орман талдауы', f4: 'Негізгі метрикалар панелі', f5: 'Email қолдау (48с жауап)', cta: 'Бастау' }, standard: { badge: 'ТАНЫМАЛ', f1: '60,000 талдау кредиттері', f2: '6 кеңейтілген AI шешімдері', f3: 'Барлық премиум талдау режимдері', f4: 'Нақты уақыт қадағалау панелі', f5: 'Брендингпен PDF + CSV экспорт', cta: 'Standard алу' }, pro: { f1: '500,000 талдау кредиттері', f2: '10 кәсіпорын шешімдері', f3: 'Командалық жұмыс кеңістігі', f4: 'Толық API қолжетімділік + вебхуктар', f5: 'Басым өңдеу + 24/7 қолдау', cta: 'Pro алу' } },
                faq: { title: 'Жиі қойылатын сұрақтар', q1: { q: 'Талдау нәтижелері қаншалықты дәл?', a: 'Біздің AI модельдері кеңейтілген пиксельдік жіктеуді пайдалана отырып, өзгерістерді анықтауда 99% дәлдікке жетеді. Нәтижелер эталондық деректер мен ғылыми әдістемелер бойынша тексерілген.' }, q2: { q: 'Сіз қандай серіктік деректер көздерін пайдаланасыз?', a: 'Біз Sentinel-2, Landsat-8/9, MODIS және коммерциялық провайдерлерді қоса алғанда, көп көздік серіктік кескіндерді біріктіреміз. Платформа оңтайлы деректерді автоматты түрде таңдайды.' }, q3: { q: 'Email растау неге қажет?', a: 'Email растау жұмыс кеңістігінің қауіпсіздігін, деректер тұтастығын және аккаунтты қалпына келтіруді қамтамасыз етеді. Бұл сондай-ақ талдау нәтижелері мен маңызды жаңартуларды жеткізуге мүмкіндік береді.' }, q4: { q: 'Тарифті жаңарту процесі қалай жұмыс істейді?', a: 'Төлемдер Telegram бот интеграциясы арқылы қауіпсіз өңделеді. Тариф белсендіру әдетте жұмыс уақытында 1-2 сағат ішінде болады. Сіз лезде растау аласыз.' } },
                final: { title: 'Серіктік деректерді бірнеше минутта практикалық аналитикаға айналдырыңыз' },
                footer: { desc: 'Экологиялық аналитика және серіктік талдауға арналған кәсіпорын AI платформасы. Шикі деректерді стратегиялық түсініктерге айналдырыңыз.', product: { title: 'Өнім', features: 'Мүмкіндіктер', pricing: 'Бағалар', api: 'API Құжаттама' }, legal: { title: 'Заңды', terms: 'Шарттар', privacy: 'Құпиялылық', security: 'Қауіпсіздік' }, contact: { title: 'Байланыс', telegram: 'Telegram', email: 'Сатылымдар', support: 'Қолдау' }, copyright: '© <?= $copyYear ?> MercuryVision Studio. Барлық құқықтар қорғалған.' }

            }
        };

        let currentLang = localStorage.getItem('lang') || 'en';
        let isDark = localStorage.getItem('theme') !== 'light';

        function updateLanguage(langCode) {
            currentLang = langCode;
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const keys = el.getAttribute('data-i18n').split('.');
                let value = translations[langCode];
                keys.forEach(k => { if(value) value = value[k]; });
                if (value) el.textContent = value;
            });
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === langCode);
            });
            localStorage.setItem('lang', langCode);
        }

        function toggleTheme() {
            isDark = !isDark;
            document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            const themeBtn = document.getElementById('themeToggle');
            themeBtn.innerHTML = isDark 
                ? '<i data-lucide="sun" width="20" height="20"></i>' 
                : '<i data-lucide="moon" width="20" height="20"></i>';
            lucide.createIcons();
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', () => updateLanguage(btn.dataset.lang));
        });

        document.getElementById('themeToggle').addEventListener('click', toggleTheme);

        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const item = button.parentElement;
                const isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(faq => faq.classList.remove('active'));
                if (!isActive) item.classList.add('active');
            });
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            });
        });

        window.addEventListener('DOMContentLoaded', () => {
            updateLanguage(currentLang);
            if (!isDark) {
                document.documentElement.setAttribute('data-theme', 'light');
                document.getElementById('themeToggle').innerHTML = '<i data-lucide="moon" width="20" height="20"></i>';
                lucide.createIcons();
            }
        });

        // Smooth Mouse Parallax for background orbs
        document.addEventListener('mousemove', (e) => {
            const orbs = document.querySelectorAll('.gradient-orb');
            const x = (window.innerWidth / 2 - e.pageX) * 0.05;
            const y = (window.innerHeight / 2 - e.pageY) * 0.05;
            
            orbs.forEach((orb, index) => {
                const multiplier = index === 0 ? 1 : (index === 1 ? -1 : 0.5);
                orb.style.transform = `translate(${x * multiplier}px, ${y * multiplier}px)`;
            });
        });
    </script>
</body>
</html>
