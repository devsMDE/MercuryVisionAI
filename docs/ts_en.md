# MercuryVision Studio 2.1 – Technical Specification (TS)

## 1. Introduction
### 1.1 Purpose
This document provides the definitive Technical Specification for MercuryVision Studio, a premium, AI-powered web platform explicitly designated for geospatial imagery intelligence and change detection.

### 1.2 Scope
This document outlines the software architecture, high-end "Apple-like" UI/UX guidelines, robust security implementations ("FBI-level"), and backend integration methods utilizing PHP, Firebase, and ChangeFormer models.

## 2. Architecture
### 2.1 Frontend Stack
- **Languages:** HTML5, modern Vanilla JavaScript (ES6+ module support), CSS3 variables.
- **UI Paradigm:** Glassmorphism, dynamically adaptive layouts, micro-interactive transitions.
- **Iconography:** Lucide Icons.

### 2.2 Backend Stack
- **Server:** PHP 8+ handling API orchestration.
- **Database:** JSON-based lightweight local data storage (`api/json_db.php`) fallback mimicking PDO for maximum portability, alongside Firebase Realtime Database.
- **Authentication:** Google Firebase Auth (frontend JWT validation, backend session translation).

## 3. Core Functionality
- **AI Compare:** General image change detection.
- **Specialized Modes:** Water Dynamics, Forest Analytics, NDVI/Agriculture, Urban Intelligence.
- **AI Strategic Analyst Chat:** Context-aware sidebar providing automated, human-readable insights based on analysis metrics, retaining context history.

## 4. Design Guidelines (UI/UX)
- **Fluid Micro-Animations:** All elements must respond to `hover`, `focus`, and `click` states using cubic-bezier timing functions (`0.16, 1, 0.3, 1`).
- **Premium Aesthetics:** Avoid plain colors. Apply precise multi-stop gradients with soft shadow layers (`drop-shadow` and `box-shadow`) to maintain depth.

## 5. Security Requirements
- **Sanitization:** All API inputs and outputs must pass through strict validation blocks to explicitly prevent XSS and SQL injection.
- **CORS Mitigation:** Restrict headers and allowed domains in `config.php`.
- **Secret Management:** Client-level API key overrides securely kept in `localStorage`, never transmitted plainly.

## 6. Target Audience
Analysts, researchers, enterprise-tier decision-makers requiring an immutable source of truth for before-and-after satellite or topological comparison.
