import sys
import re

with open('index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Extract sections
hero_end = content.find('</section>', content.find('<section class="hero"')) + 10
features_start = content.find('<section class="features" id="features">')
features_end = content.find('</section>', features_start) + 10
pricing_start = content.find('<section class="pricing" id="pricing">')
pricing_end = content.find('</section>', pricing_start) + 10
how_start = content.find('<!-- How It Works -->')
how_end = content.find('</section>', how_start) + 10
benefits_start = content.find('<!-- What You Get -->')
benefits_end = content.find('</section>', benefits_start) + 10
faq_start = content.find('<section class="faq" id="faq">')

# Get exact strings
features_str = content[features_start:features_end]
how_str = content[how_start:how_end]
benefits_str = content[benefits_start:benefits_end]

# New Pricing
pricing_str = '''    <!-- Pricing -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 18px; background:rgba(251,146,60,0.08); border:1px solid rgba(251,146,60,0.2); border-radius:100px; font-size:11px; font-weight:700; color:#fb923c; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:20px;">
                    <i data-lucide="credit-card" width="12" height="12"></i> <span data-i18n="nav.pricing">Pricing</span>
                </div>
                <h2 class="section-title" data-i18n="pricing.title" style="letter-spacing:-0.04em; margin-bottom:16px;">Enterprise Pricing</h2>
                <p class="section-subtitle" data-i18n="pricing.subtitle" style="font-size:18px; line-height:1.6; max-width:600px; margin:0 auto; color:var(--text-secondary);">Start with our free tier. Scale as you grow. Upgrade seamlessly via Telegram.</p>
            </div>
            
            <div style="display:flex; justify-content:center; gap:24px; flex-wrap:wrap; margin-top:60px;">
                <!-- FREE TIER -->
                <div class="glass-card" style="width:340px; padding:40px; text-align:left; border-top:2px solid rgba(255,255,255,0.1);">
                    <div style="font-size:18px; font-weight:700; color:var(--text-secondary); margin-bottom:16px;">FREE</div>
                    <div style="font-size:54px; font-weight:800; color:var(--text-primary); margin-bottom:24px; letter-spacing:-2px;">0₸</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:16px; min-height:220px;">
                        <li style="display:flex; gap:12px; color:var(--text-secondary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            75 credits included
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-secondary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            Basic Analysis Modes
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-secondary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            No API Access
                        </li>
                    </ul>
                    <a href="/auth" style="display:block; width:100%; padding:14px; text-align:center; background:rgba(255,255,255,0.05); color:var(--text-primary); border-radius:12px; text-decoration:none; font-weight:600; border:1px solid var(--border); transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        Start For Free
                    </a>
                </div>
                
                <!-- STANDARD TIER -->
                <div class="glass-card" style="width:360px; padding:44px 40px; text-align:left; border-top:2px solid var(--gold); position:relative; transform:translateY(-12px); box-shadow:0 12px 40px rgba(59,130,246,0.15); background:rgba(59,130,246,0.03);">
                    <div style="position:absolute; top:-14px; left:50%; transform:translateX(-50%); background:var(--gold); color:#fff; font-size:11px; font-weight:700; padding:6px 16px; border-radius:100px; letter-spacing:1px;" data-i18n="pricing.standard.badge">MOST POPULAR</div>
                    <div style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:12px;">Standard</div>
                    <div style="display:flex; align-items:baseline; gap:12px; margin-bottom:8px;">
                        <div style="font-size:54px; font-weight:800; color:var(--text-primary); letter-spacing:-2px;">11,999₸</div>
                        <div style="font-size:20px; color:var(--text-tertiary); text-decoration:line-through;">17,999₸</div>
                    </div>
                    <div style="font-size:14px; color:var(--text-secondary); font-weight:500; margin-bottom:24px;">$24.99 / 2,249₽ per month</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:16px; min-height:220px;">
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--gold); flex-shrink:0;"></i>
                            60,000 analysis credits
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--gold); flex-shrink:0;"></i>
                            <span data-i18n="pricing.standard.f2">6 advanced AI solutions</span>
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--gold); flex-shrink:0;"></i>
                            <span data-i18n="pricing.standard.f3">All premium analysis modes</span>
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--gold); flex-shrink:0;"></i>
                            Bring Your Own Keys
                        </li>
                    </ul>
                    <a href="https://t.me/MercuryVisionStudioBot?start=standard" class="btn-primary" style="display:flex; width:100%; padding:14px; text-align:center; justify-content:center; align-items:center; border-radius:12px; text-decoration:none; font-weight:600; font-size:15px;">
                        <span data-i18n="pricing.standard.cta">Get Standard</span>
                    </a>
                </div>
                
                <!-- PRO TIER -->
                <div class="glass-card" style="width:340px; padding:40px; text-align:left; border-top:2px solid rgba(255,255,255,0.1);">
                    <div style="font-size:18px; font-weight:700; color:var(--text-secondary); margin-bottom:12px;">PRO</div>
                    <div style="display:flex; align-items:baseline; gap:12px; margin-bottom:8px;">
                        <div style="font-size:54px; font-weight:800; color:var(--text-primary); letter-spacing:-2px;">35,999₸</div>
                        <div style="font-size:20px; color:var(--text-tertiary); text-decoration:line-through;">53,999₸</div>
                    </div>
                    <div style="font-size:14px; color:var(--text-secondary); font-weight:500; margin-bottom:24px;">$74.99 / 6,749₽ per month</div>
                    
                    <ul style="list-style:none; padding:0; margin:0 0 32px 0; display:flex; flex-direction:column; gap:16px; min-height:220px;">
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            <span data-i18n="pricing.pro.f1">500,000 analysis credits</span>
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            <span data-i18n="pricing.pro.f4">Full API access + webhooks</span>
                        </li>
                        <li style="display:flex; gap:12px; color:var(--text-primary); font-size:15px; font-weight:500;">
                            <i data-lucide="check" width="18" height="18" style="color:var(--text-secondary); flex-shrink:0;"></i>
                            <span data-i18n="pricing.pro.f5">Priority processing + 24/7 support</span>
                        </li>
                    </ul>
                    <a href="https://t.me/MercuryVisionStudioBot?start=pro" style="display:block; width:100%; padding:14px; text-align:center; background:rgba(255,255,255,0.05); color:var(--text-primary); border-radius:12px; text-decoration:none; font-weight:600; border:1px solid var(--border); transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <span data-i18n="pricing.pro.cta">Get PRO</span>
                    </a>
                </div>
            </div>
            
        </div>
    </section>'''

# Combine parts in new order
new_content = (
    content[:hero_end+1] + 
    "\\n\\n" + 
    how_str + 
    "\\n\\n" + 
    features_str + 
    "\\n\\n" + 
    benefits_str + 
    "\\n\\n" + 
    pricing_str + 
    "\\n\\n" + 
    content[faq_start:]
)

with open('index.php', 'w', encoding='utf-8') as f:
    f.write(new_content)
