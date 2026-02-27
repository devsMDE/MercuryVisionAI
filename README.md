<p align="center">
  <img src="https://img.shields.io/badge/Space%20AI-Satellite%20Intelligence-3b82f6?style=for-the-badge&logo=satellite&logoColor=white" alt="Space AI Badge"/>
  <img src="https://img.shields.io/badge/ChangeFormer-v6-818cf8?style=for-the-badge&logo=pytorch&logoColor=white" alt="ChangeFormer Badge"/>
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Badge"/>
  <img src="https://img.shields.io/badge/Python-3.10+-3776AB?style=for-the-badge&logo=python&logoColor=white" alt="Python Badge"/>
</p>

# 🛰️ MercuryVision Studio

**Enterprise AI platform for satellite imagery change detection and environmental intelligence.**

MercuryVision Studio transforms raw satellite imagery into strategic environmental insights using state-of-the-art deep learning. The platform combines the **ChangeFormer** transformer-based change detection model with **GPT-4o / Gemini** AI analysis to provide automated environmental monitoring, risk assessment, and decision support.

> 🏆 **AEROO Space AI Competition 2026** — Space-themed AI startup MVP

---

## 🎯 Problem Statement

Environmental monitoring using satellite data is traditionally:
- **Expensive** — requires specialized GIS software ($10k+/year)
- **Complex** — needs expert analysts for interpretation
- **Slow** — manual analysis takes days per study area
- **Inaccessible** — not available to small organizations, municipalities, or researchers

**MercuryVision solves this** by democratizing satellite change detection through an AI-first web platform that any user can operate without GIS expertise.

---

## 🚀 Key Features

| Feature | Description |
|---------|-------------|
| 🔬 **ChangeFormer AI** | Transformer-based neural network for pixel-level change detection (99% accuracy on LEVIR-CD benchmark) |
| 🌊 **5 Analysis Modes** | Water Dynamics, Forest Analytics, NDVI/Agriculture, Urban Intelligence, AI Compare |
| 🤖 **AI Decision Support** | GPT-4o / Gemini generates structured problem-solution reports with confidence scores |
| 💬 **AI Chat Assistant** | Context-aware chat about analysis results, responds in user's language (EN/RU/KZ) |
| 📊 **Interactive Results** | Before/after slider, change heatmaps, domain-specific metrics |
| 📄 **Export** | PDF reports with branding, CSV data export |
| 🌍 **Multilingual** | Full UI in English, Russian, and Kazakh |
| 🎨 **Premium UI** | Dark/light themes, glassmorphism, responsive design |
| 🔐 **Auth & Plans** | Firebase authentication, tiered credit system (Free/Lite/Standard/Pro) |
| 📁 **Project Management** | Save, browse, filter, and revisit past analyses |

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Landing Page │  │  Auth (SSO)  │  │  Dashboard   │  │
│  │  index.php   │  │  Firebase    │  │  SPA-like UI │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└────────────────────────┬────────────────────────────────┘
                         │ HTTP/REST
┌────────────────────────▼────────────────────────────────┐
│                   PHP API LAYER                         │
│  ┌─────────┐ ┌──────────┐ ┌─────────┐ ┌────────────┐   │
│  │ session │ │ projects │ │   ai    │ │changeformer│   │
│  │  .php   │ │  .php    │ │  .php   │ │   .php     │   │
│  └─────────┘ └──────────┘ └─────────┘ └────────────┘   │
│  ┌─────────────┐ ┌──────────┐ ┌─────────────────────┐  │
│  │ user_store  │ │  plans   │ │ checkout / export   │  │
│  │   .php      │ │  .php    │ │       .php          │  │
│  └─────────────┘ └──────────┘ └─────────────────────┘  │
└────────┬───────────────────────────────┬────────────────┘
         │                               │
┌────────▼────────┐          ┌───────────▼────────────┐
│   SQLite DB     │          │   ChangeFormer Engine  │
│   (app.db)      │          │   Python + PyTorch     │
│  ─ users        │          │   ─ FastAPI server     │
│  ─ projects     │          │   ─ Transformer model  │
│  ─ analyses     │          │   ─ Hotspot detection  │
│  ─ credits      │          │   ─ Domain metrics     │
└─────────────────┘          └────────────────────────┘
         │                               │
         └───────────┬───────────────────┘
                     │
          ┌──────────▼──────────┐
          │   OpenAI / Gemini   │
          │   GPT-4o / Gemini   │
          │   AI Analysis API   │
          └─────────────────────┘
```

---

## 🧠 AI Technologies Used

### 1. ChangeFormer v6 (Computer Vision)
- **Architecture**: Siamese transformer encoder with multi-scale feature fusion
- **Purpose**: Pixel-level binary change detection between two satellite images
- **Training**: Pre-trained on LEVIR-CD (637 pairs, 1024×1024) and DSIFN datasets
- **Inference**: Processes image pairs → produces binary change mask + overlay
- **Source**: Based on [ChangeFormer (IGARSS 2022)](https://github.com/wgcban/ChangeFormer)

### 2. GPT-4o / Gemini (NLP + Decision Support)
- **Structured Output**: JSON schema-enforced responses for consistent UI rendering
- **Contextual Analysis**: Receives change metrics, hotspots, and domain context
- **Multilingual**: Generates reports in the user's selected UI language
- **Features**:
  - Problem identification from change metrics
  - Solution generation with pros/cons analysis
  - Confidence scoring and assumption tracking
  - Interactive chat for follow-up questions

### 3. Domain-Specific Analysis Pipelines
Each analysis mode applies specialized algorithms:
- **Water Dynamics**: NDWI-inspired spectral analysis, shoreline tracking
- **Forest Analytics**: NDVI change, fragmentation index, canopy density
- **NDVI/Agriculture**: Vegetation vigor scoring, stress zone detection
- **Urban Intelligence**: Built-up area change, structural detection
- **AI Compare**: General-purpose change detection with anomaly scoring

---

## 📂 Project Structure

```
mercuryvision-studio/
├── index.php                 # Landing page (marketing, pricing, technologies)
├── auth/
│   └── index.php             # Firebase SSO authentication
├── dashboard/
│   └── index.php             # Main application dashboard (SPA-like)
├── api/
│   ├── session.php           # Auth session management (cookie-based)
│   ├── user_data.php         # User profile & usage data
│   ├── user_store.php        # User DB operations & credit tracking
│   ├── plans.php             # Plan configurations (Free/Lite/Standard/Pro)
│   ├── projects.php          # Projects CRUD + analysis storage
│   ├── changeformer.php      # Change detection API (Python bridge or fallback)
│   ├── ai.php                # AI report generation (OpenAI/Gemini)
│   ├── chat.php              # AI chat endpoint
│   ├── checkout.php          # Plan upgrade simulation
│   ├── export.php            # PDF/CSV export
│   ├── config.php            # Database & environment configuration
│   ├── json_db.php           # SQLite-free JSON database fallback
│   ├── verify.php            # Firebase token verification
│   └── storage/              # Uploaded images & results (gitignored)
├── ChangeFormer/
│   ├── api.py                # FastAPI inference server
│   ├── infer_pair.py         # CLI inference with metrics extraction
│   ├── models/               # ChangeFormer neural network architecture
│   │   ├── ChangeFormer.py   # Main model (V1-V6 variants)
│   │   ├── Transformer.py    # Multi-head attention & transformer blocks
│   │   ├── PixelWiseDotProduct.py
│   │   └── ...               # Encoder/decoder components
│   ├── datasets/             # Data loading utilities
│   ├── requirements.txt      # Python dependencies
│   └── misc/                 # Visualization & logging utilities
├── docs/
│   ├── ts_en.md              # Terms of Service (English)
│   ├── ts_ru.md              # Terms of Service (Russian)
│   └── ts_kz.md              # Terms of Service (Kazakh)
├── .env.example              # Environment template
├── .gitignore                # Git exclusions
├── php.ini                   # PHP configuration for uploads
├── start.bat                 # Windows quick-start script
├── robots.txt                # Search engine directives
└── sitemap.xml               # SEO sitemap
```

---

## ⚡ Quick Start

### Prerequisites
- **PHP 8.1+** with extensions: `pdo_sqlite`, `curl`, `openssl`, `mbstring`, `gd`
- **Python 3.10+** (optional, for ChangeFormer neural network inference)
- **Node.js** (optional, for Python API process management)

### 1. Clone & Configure
```bash
git clone https://github.com/your-team/mercuryvision-studio.git
cd mercuryvision-studio

# Configure environment
cp .env.example .env
# Edit .env with your API keys:
#   OPENAI_API_KEY=sk-...
#   PYTHON_API_URL=http://localhost:5000  (if using Python inference)
```

### 2. Start the Web Server
```bash
# Option A: Quick start (Windows)
start.bat

# Option B: PHP built-in server
php -S localhost:8000

# Option C: Apache/Nginx (production)
# Point document root to the project directory
```

### 3. Start ChangeFormer (Optional)
```bash
cd ChangeFormer
pip install -r requirements.txt
# Download pre-trained weights to pretrained/ChangeFormer.pt
uvicorn api:app --host 0.0.0.0 --port 5000
```

### 4. Open the Application
Navigate to http://localhost:8000

> **Note**: For local development without Firebase, the system will automatically use `dev_login` fallback on localhost.

---

## 💰 Business Model

### Target Market
| Segment | Size | Annual Revenue Potential |
|---------|------|------------------------|
| Environmental consulting firms | 12,000+ globally | $50M |
| Government agencies (EPA, ministries) | 1,500+ | $30M |
| Agriculture tech companies | 8,000+ | $40M |
| Insurance (climate risk) | 500+ | $25M |
| Academic institutions | 5,000+ | $15M |

### Pricing Plans

| Plan | Price | Credits | Features |
|------|-------|---------|----------|
| **Free** | $0 | 75/month | AI Compare mode, basic exports |
| **Lite** | $5.99/mo | 300/month | + Water & Forest analytics |
| **Standard** | $24.99/mo | 1,500/month | + All modes, PDF export, priority |
| **Pro** | $74.99/mo | Unlimited | + API access, custom models, SLA |

### Revenue Projections
- **Year 1**: 500 users × $15 ARPU = $90K ARR
- **Year 2**: 2,000 users × $25 ARPU = $600K ARR
- **Year 3**: 8,000 users × $35 ARPU = $3.4M ARR

### Competitive Advantages
| vs Competitor | MercuryVision Advantage |
|---------------|------------------------|
| Google Earth Engine | No coding required, instant AI reports |
| ESRI ArcGIS | 100× cheaper, web-based, no installation |
| Planet Labs | AI-driven analysis (not just imagery) |
| Descartes Labs | SMB-friendly pricing, multilingual |

---

## 🔒 Security

- Firebase Authentication (Google SSO, email/password)
- Server-side PHP sessions with HttpOnly, SameSite=Lax cookies
- CORS configured for credentials (origin-based)
- API keys stored in `.env` (never committed)
- Security headers: `X-Frame-Options`, `X-XSS-Protection`, `X-Content-Type-Options`
- Input validation on all API endpoints
- Credit system with rate limiting

---

## 🛣️ Roadmap

- [x] ChangeFormer AI change detection
- [x] Multi-mode analysis (Water, Forest, NDVI, Urban)
- [x] AI-powered decision support reports (GPT-4o / Gemini)
- [x] Interactive AI chat assistant
- [x] Project management with history
- [x] PDF/CSV export
- [x] Multilingual UI (EN/RU/KZ)
- [x] Credit-based billing system
- [ ] Sentinel-2 API integration (automatic satellite data fetching)
- [ ] Time-series analysis (multi-temporal stacking)
- [ ] Collaborative workspaces (team projects)
- [ ] Mobile-responsive PWA
- [ ] Custom model training (fine-tuning on user data)

---

## 📄 API Documentation

### Authentication
All API endpoints (except login) require a valid session cookie.

```bash
# Login (Firebase token)
POST /api/session.php
{ "action": "login", "idToken": "firebase-id-token" }

# Dev login (localhost only)
POST /api/session.php
{ "action": "dev_login", "email": "user@example.com" }
```

### Change Detection
```bash
POST /api/changeformer.php
Content-Type: multipart/form-data

Fields:
  mode: "compare" | "water" | "forest" | "agriculture" | "urban"
  model: "base" | "fast" | "large"
  before: <image file>
  after: <image file>

Response:
{
  "ok": true,
  "output_image": "base64...",
  "before_image": "base64...",
  "after_image": "base64...",
  "metrics": { ... },
  "usage": { "used": 25, "limit": 75, "plan": "Free" }
}
```

### AI Report Generation
```bash
POST /api/ai.php
{
  "action": "initial_report",
  "mode": "water",
  "changePercent": 12.5,
  "hotspots": [...],
  "metrics": {...},
  "language": "ru"
}

Response:
{
  "ok": true,
  "summary": "...",
  "problems": [...],
  "solutionsByProblem": [...]
}
```

### Projects CRUD
```bash
GET  /api/projects.php                              # List projects
GET  /api/projects.php?action=project_detail&project_id=1  # Get project
POST /api/projects.php  { "action": "create_project", "name": "..." }
POST /api/projects.php  { "action": "save_analysis", "project_id": 1, ... }
POST /api/projects.php  { "action": "delete_project", "project_id": 1 }
```

---

## 🧪 Technology Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| Frontend | HTML5, CSS3, JavaScript | SPA-like dashboard UI |
| Design | Glassmorphism, CSS Variables | Premium dark/light themes |
| Icons | Lucide Icons | Consistent iconography |
| Backend | PHP 8.1+ | REST API, session management |
| Database | SQLite (PDO) | User data, projects, analyses |
| AI Model | ChangeFormer (PyTorch) | Satellite change detection |
| AI NLP | OpenAI GPT-4o / Google Gemini | Report generation & chat |
| Auth | Firebase Auth | Google SSO, email login |
| Export | html2pdf.js | Client-side PDF generation |

---

## 👥 Team

**MercuryVision** — AEROO Space AI Competition 2026

---

## 📜 License

Proprietary — MercuryVision Studio. All rights reserved.
