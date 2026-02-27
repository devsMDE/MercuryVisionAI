import re

with open('index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Font change
content = re.sub(
    r'family=Outfit:wght@[0-9;]+',
    'family=Inter:wght@300;400;500;600;700;800',
    content
)
content = content.replace("--font-family: 'Outfit'", "--font-family: 'Inter'")

pricing_start = content.find('<section class="pricing" id="pricing">')
pricing_end = content.find('</section>', pricing_start) + 10

pricing_html = '''    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(251,146,60,0.08); border:1px solid rgba(251,146,60,0.2); border-radius:100px; font-size:11px; font-weight:700; color:#fb923c; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:20px;">
                    <i data-lucide="credit-card" width="12" height="12"></i> <span data-i18n="nav.pricing">Pricing</span>
                </div>
                <h2 class="section-title" data-i18n="pricing.title" style="letter-spacing:-0.04em; margin-bottom:16px;">Enterprise Pricing</h2>
                <p class="section-subtitle" data-i18n="pricing.subtitle" style="font-size:18px; line-height:1.6; max-width:600px; margin:0 auto; color:var(--text-secondary);">Start with our free tier. Scale as you grow. Upgrade seamlessly via Telegram.</p>
            </div>
            
            <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap; margin-top:60px;">
                <!-- FREE TIER -->
                <div class="glass-card pricing-card-hover" style="flex:1; min-width:260px; max-width:280px; padding:32px 24px; text-align:left; border-top:2px solid rgba(255,255,255,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size:16px; font-weight:700; color:var(--text-secondary); margin-bottom:12px;">FREE</div>
                    <div style="font-size:42px; font-weight:800; color:var(--text-primary); margin-bottom:24px; letter-spacing:-2px;">0₸</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:12px; min-height:220px;">
                        <li style="display:flex; gap:10px; color:var(--text-secondary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            Basic Analysis Modes
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-secondary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            No API Access
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-secondary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            Standard Processing
                        </li>
                    </ul>
                    <a href="/auth" class="btn-primary-hover" style="display:block; width:100%; padding:12px; text-align:center; background:rgba(255,255,255,0.05); color:var(--text-primary); border-radius:12px; text-decoration:none; font-weight:600; border:1px solid var(--border); transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                        Start For Free
                    </a>
                </div>

                <!-- LITE TIER -->
                <div class="glass-card pricing-card-hover" style="flex:1; min-width:260px; max-width:280px; padding:32px 24px; text-align:left; border-top:2px solid rgba(255,255,255,0.3); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">Lite</div>
                    <div style="display:flex; align-items:baseline; gap:8px; margin-bottom:4px;">
                        <div style="font-size:42px; font-weight:800; color:var(--text-primary); letter-spacing:-2px;">2,879₸</div>
                        <div style="font-size:16px; color:var(--text-tertiary); text-decoration:line-through;">4,299₸</div>
                    </div>
                    <div style="font-size:13px; color:var(--text-secondary); font-weight:500; margin-bottom:24px;">$5.99 / 539₽ per month</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:12px; min-height:220px;">
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#60a5fa; flex-shrink:0;"></i>
                            Everything in Free
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#60a5fa; flex-shrink:0;"></i>
                            Water + Forest Maps
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#60a5fa; flex-shrink:0;"></i>
                            3 AI Solutions
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#60a5fa; flex-shrink:0;"></i>
                            Email Support
                        </li>
                    </ul>
                    <a href="https://t.me/MercuryVisionStudioBot?start=lite" class="btn-primary-hover" style="display:block; width:100%; padding:12px; text-align:center; background:rgba(255,255,255,0.05); color:var(--text-primary); border-radius:12px; text-decoration:none; font-weight:600; border:1px solid var(--border); transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                        Get Lite
                    </a>
                </div>
                
                <!-- STANDARD TIER -->
                <div class="glass-card pricing-card-hover" style="flex:1; min-width:270px; max-width:290px; padding:36px 28px; text-align:left; border-top:2px solid var(--gold); position:relative; transform:translateY(-8px); box-shadow:0 12px 40px rgba(59,130,246,0.15); background:rgba(59,130,246,0.03); z-index:2;">
                    <div style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:var(--gold); color:#fff; font-size:10px; font-weight:700; padding:4px 12px; border-radius:100px; letter-spacing:1px;" data-i18n="pricing.standard.badge">MOST POPULAR</div>
                    <div style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">Standard</div>
                    <div style="display:flex; align-items:baseline; gap:8px; margin-bottom:4px;">
                        <div style="font-size:42px; font-weight:800; color:var(--text-primary); letter-spacing:-2px;">11,999₸</div>
                        <div style="font-size:16px; color:var(--text-tertiary); text-decoration:line-through;">17,999₸</div>
                    </div>
                    <div style="font-size:13px; color:var(--text-secondary); font-weight:500; margin-bottom:24px;">$24.99 / 2,249₽ per month</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:12px; min-height:220px;">
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--gold); flex-shrink:0;"></i>
                            Everything in Lite
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--gold); flex-shrink:0;"></i>
                            All Analysis Modes
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--gold); flex-shrink:0;"></i>
                            6 AI Solutions
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--gold); flex-shrink:0;"></i>
                            Bring Your Own Keys
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:var(--gold); flex-shrink:0;"></i>
                            PDF & CSV Exports
                        </li>
                    </ul>
                    <a href="https://t.me/MercuryVisionStudioBot?start=standard" class="btn-primary btn-primary-hover" style="display:flex; width:100%; padding:12px; text-align:center; justify-content:center; align-items:center; border-radius:12px; text-decoration:none; font-weight:600; font-size:14px; transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                        <span data-i18n="pricing.standard.cta">Get Standard</span>
                    </a>
                </div>
                
                <!-- PRO TIER -->
                <div class="glass-card pricing-card-hover" style="flex:1; min-width:260px; max-width:280px; padding:32px 24px; text-align:left; border-top:2px solid #a855f7; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">PRO</div>
                    <div style="display:flex; align-items:baseline; gap:8px; margin-bottom:4px;">
                        <div style="font-size:42px; font-weight:800; color:var(--text-primary); letter-spacing:-2px;">35,999₸</div>
                        <div style="font-size:16px; color:var(--text-tertiary); text-decoration:line-through;">53,999₸</div>
                    </div>
                    <div style="font-size:13px; color:var(--text-secondary); font-weight:500; margin-bottom:24px;">$74.99 / 6,749₽ per month</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:12px; min-height:220px;">
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#a855f7; flex-shrink:0;"></i>
                            Everything in Standard
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#a855f7; flex-shrink:0;"></i>
                            Full API access + Webhooks
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#a855f7; flex-shrink:0;"></i>
                            Priority Processing
                        </li>
                        <li style="display:flex; gap:10px; color:var(--text-primary); font-size:14px; font-weight:500;">
                            <i data-lucide="check" width="16" height="16" style="color:#a855f7; flex-shrink:0;"></i>
                            24/7 Priority Support
                        </li>
                    </ul>
                    <a href="https://t.me/MercuryVisionStudioBot?start=pro" class="btn-primary-hover" style="display:block; width:100%; padding:12px; text-align:center; background:rgba(255,255,255,0.05); color:var(--text-primary); border-radius:12px; text-decoration:none; font-weight:600; border:1px solid var(--border); transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                        <span data-i18n="pricing.pro.cta">Get PRO</span>
                    </a>
                </div>
            </div>
            
        </div>
    </section>'''

if pricing_start != -1 a pricing_end != -1:
    new_content = content[:pricing_start] + pricing_html + content[pricing_end:]
    
    # Adding animation styles globally
    anim_style = """
        /* Animations & Interactions */
        .pricing-card-hover:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--text-tertiary); }
        .btn-primary-hover:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .glass-card { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .feature-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: var(--gold); }
        .benefit-item:hover { transform: translateX(4px); }
        .benefit-item { transition: transform 0.3s var(--ease); }
        .step-card { transition: transform 0.4s var(--ease), box-shadow 0.4s var(--ease); }
        .step-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
    """
    new_content = new_content.replace('</style>', anim_style + '\n    </style>')
    with open('index.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
