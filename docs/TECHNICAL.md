# MercuryVision Studio — Technical Documentation

## 1. System Overview

MercuryVision Studio is an enterprise AI platform for satellite imagery change detection and environmental monitoring. The system processes pairs of satellite images (before/after) through a ChangeFormer neural network to detect surface changes, then uses GPT-4o or Gemini to generate structured analytical reports.

### Core Pipeline

```
Input (2 satellite images) 
    → Preprocessing (resize, normalize)
    → ChangeFormer Neural Network (pixel-level change detection)
    → Domain-Specific Metric Extraction
    → AI Report Generation (GPT-4o/Gemini)
    → Interactive Dashboard Visualization
```

---

## 2. AI Model — ChangeFormer

### 2.1 Architecture

ChangeFormer is a transformer-based Siamese network for binary change detection:

- **Encoder**: Hierarchical multi-scale transformer with 4 stages
- **Decoder**: Feature fusion with MLP heads
- **Two branches** process the before/after images independently
- **Difference module** combines features for change prediction

Key components (in `ChangeFormer/models/`):
- `ChangeFormer.py` — Main model architecture (V1-V6 variants used in production)
- `Transformer.py` — Multi-head self-attention, transformer encoder blocks
- `PixelWiseDotProduct.py` — Feature comparison between temporal branches

### 2.2 Inference Pipeline

Located in `ChangeFormer/infer_pair.py`:

1. **Input**: Two RGB images (before, after)
2. **Preprocessing**: Resize to matching dimensions, normalize to float32
3. **Differencing**: Absolute pixel-wise difference → grayscale
4. **Thresholding**: Percentile-based adaptive threshold (varies by model: base=75th, fast=80th, large=70th percentile)
5. **Mask generation**: Binary change mask (changed/unchanged)
6. **Overlay creation**: Semi-transparent red overlay on changed regions
7. **Hotspot extraction**: Grid-based spatial clustering of highest change intensity regions
8. **Domain metrics**: Mode-specific metric computation (water/forest/agriculture/urban)

### 2.3 FastAPI Server

`ChangeFormer/api.py` provides a REST inference endpoint:
- `POST /predict` — Accepts two image files, returns base64-encoded change mask
- Pre-loads the PyTorch model into memory for fast inference
- GPU acceleration with CUDA if available, CPU fallback

### 2.4 Fallback Mode

When the Python ChangeFormer server is unavailable, `api/changeformer.php` provides a PHP-based fallback:
- Uses GD library for image processing
- Applies pixel differencing with configurable thresholds
- Generates domain-specific metrics based on statistical analysis
- Produces overlay visualizations and change masks

---

## 3. PHP API Layer

### 3.1 Session Management (`api/session.php`)

- Firebase ID token verification via Google's public keys
- PHP session cookies: `HttpOnly`, `SameSite=Lax`, `Secure` (in production)
- Development fallback: `dev_login` action for localhost testing
- Token refresh handling to prevent stale sessions

### 3.2 Credit System

Cost structure:
| Action | Credits |
|--------|---------|
| Change Detection Analysis | 25 |
| AI Report Generation | 15 |
| AI Chat Message | 5 |

Plan limits:
| Plan | Monthly Credits | Available Modes |
|------|----------------|-----------------|
| Free | 75 | compare |
| Lite | 300 | compare, water, forest |
| Standard | 1,500 | All modes |
| Pro | Unlimited | All modes + API |

Implementation: `api/user_store.php` tracks `credits_used` per user per month.

### 3.3 AI Integration (`api/ai.php`)

Two modes:
1. **`initial_report`** — Structured JSON response with JSON Schema enforcement
   - Schema: `summary`, `problems[]`, `solutionsByProblem[]`, `confidence`, `assumptions[]`
   - Each solution includes: `title`, `description`, `pros[]`, `cons[]`

2. **`chat_message`** — Conversational follow-up questions
   - Context-aware with full message history
   - Language-adaptive (responds in user's UI language)

Supports:
- **OpenAI**: GPT-4o-mini (default) or custom model via env
- **Google Gemini**: Via generativelanguage API
- **Local Fallback**: Pre-built JSON response when no API key available

### 3.4 Database Schema

SQLite database (`api/storage/app.db`):

```sql
-- Users table
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uid TEXT UNIQUE NOT NULL,
    email TEXT NOT NULL,
    username TEXT DEFAULT '',
    plan TEXT DEFAULT 'Free',
    credits_used INTEGER DEFAULT 0,
    credits_reset_month TEXT DEFAULT '',
    created_at TEXT DEFAULT (datetime('now')),
    updated_at TEXT DEFAULT (datetime('now'))
);

-- Projects table
CREATE TABLE projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uid TEXT NOT NULL,
    name TEXT NOT NULL,
    description TEXT DEFAULT '',
    created_at TEXT DEFAULT (datetime('now')),
    updated_at TEXT DEFAULT (datetime('now'))
);

-- Analyses table (stores full result payloads as JSON)
CREATE TABLE analyses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uid TEXT NOT NULL,
    project_id INTEGER NOT NULL,
    mode TEXT NOT NULL,
    payload TEXT NOT NULL,  -- JSON blob with images, metrics, AI reports
    created_at TEXT DEFAULT (datetime('now'))
);
```

Fallback: `api/json_db.php` provides a JSON-file-based DB when SQLite extension is unavailable.

---

## 4. Frontend Architecture

### 4.1 Single-Page Application Pattern

The dashboard (`dashboard/index.php`) operates as a SPA without a JS framework:
- View switching via CSS classes (`.view-section.active`)
- Navigation state managed by sidebar `nav-item` click handlers
- All API calls use `fetch()` with `credentials: 'include'`

### 4.2 Internationalization (i18n)

Three-language support: English, Russian, Kazakh.

Implementation:
- `window.translations` object with per-language key-value maps
- `t(key)` helper function for lookups
- `data-i18n` HTML attributes for automatic DOM updates
- Language persisted in `localStorage('mv_lang')`
- AI responses generated in the user's selected language

### 4.3 Theme System

Dark/Light/System theme support:
- CSS custom properties defined in `:root` and `[data-theme="light"]`
- Theme persisted in `localStorage('mv_theme')`
- System preference detection via `matchMedia('(prefers-color-scheme: dark)')`

### 4.4 Results Visualization

1. **Before/After Slider** — Interactive clip-path comparison
2. **Metrics Grid** — Domain-specific metric cards with before/after/change values
3. **AI Report Section** — Structured problem/solution cards with expandable details
4. **AI Chat** — Slide-out right sidebar with message bubbles

---

## 5. Environment Configuration

### 5.1 Environment Variables (`.env`)

```ini
OPENAI_API_KEY=sk-...            # OpenAI API key for AI reports
OPENAI_MODEL=gpt-4o-mini         # Model to use (default: gpt-4o-mini)
GEMINI_API_KEY=AI...              # Google Gemini API key (alternative)
PYTHON_API_URL=http://localhost:5000  # ChangeFormer FastAPI server URL
APP_ENV=local                     # Environment (local/production)
```

### 5.2 PHP Configuration (`php.ini`)

```ini
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 120
memory_limit = 256M
```

---

## 6. Deployment

### Local Development
```bash
php -S localhost:8000
```

### Production (Apache)
```apache
<VirtualHost *:443>
    DocumentRoot /var/www/mercuryvision-studio
    <Directory /var/www/mercuryvision-studio>
        AllowOverride All
        Require all granted
    </Directory>
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
</VirtualHost>
```

### Production (Nginx)
```nginx
server {
    listen 443 ssl;
    root /var/www/mercuryvision-studio;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location /api/storage/ {
        deny all;
    }
}
```

### Storage Permissions
```bash
chmod -R 775 storage/ api/storage/
chown -R www-data:www-data storage/ api/storage/
```

---

## 7. Testing the MVP

### Manual Testing Checklist

1. **Authentication**
   - [ ] Firebase login (Google, email/password)
   - [ ] Dev login on localhost
   - [ ] Session persistence across page reloads
   - [ ] Logout clears session

2. **Analysis**
   - [ ] Upload before/after images (JPG, PNG, WEBP)
   - [ ] All 5 modes produce results
   - [ ] Before/after slider works
   - [ ] Metrics display correctly in all 3 languages
   - [ ] Analysis saved to project automatically

3. **AI Reports**
   - [ ] Generate initial report
   - [ ] Report displays in correct language
   - [ ] Chat follow-up questions work
   - [ ] Error handling when API key missing

4. **Projects**
   - [ ] Projects list loads
   - [ ] View project restores analysis
   - [ ] Delete project works
   - [ ] Search and filter work

5. **Billing**
   - [ ] Credits deducted correctly
   - [ ] Limit enforcement works
   - [ ] Plan upgrade simulation works

---

*Last updated: February 2026*
*MercuryVision Studio — AEROO Space AI Competition*
