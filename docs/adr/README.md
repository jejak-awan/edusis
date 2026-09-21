# Architecture Decision Records (ADRs) — JA-EduSIS

Pusat dokumentasi keputusan arsitektur resmi (*Architecture Decision Records*) untuk sistem **JA-EduSIS** (`ja-edusis`).

> [!IMPORTANT]
> **Kebijakan Penomoran ADR:** Sesuai [`docs/DOCUMENTATION.md`](../DOCUMENTATION.md), nomor ADR bersifat **abadi dan tidak boleh di-renumber**. Nomor keputusan yang telah didekomisi atau dialokasikan pada artefak legacy tetap dicadangkan (*reserved*).

---

## 🎓 1. Domain Spesifik JA-EduSIS (Pilar Utama)

Keputusan arsitektur yang mendasari pembentukan, struktur modul sekolah, tata kelola data, dan baseline antarmuka EduSIS:

| Nomor | Judul Keputusan | Status | Scope |
| :--- | :--- | :--- | :--- |
| **[ADR-029](./ADR-029-edusis-domain-and-multi-repo-synthesis.md)** | Arsitektur Domain JA-EduSIS dan Sintesis Multi-Repositori | Accepted | `edusis:core` |
| **[ADR-030](./ADR-030-school-module-scaffold-and-subdomain-architecture.md)** | Struktur Scaffolding dan Sub-Domain Modul School (`Modules/School`) | Accepted | `edusis:school` |
| **[ADR-031](./ADR-031-school-module-soc-sot-and-reference-alignment.md)** | Standarisasi Pemrograman, SoC, SoT, dan Penyelarasan Aset `repo_referensi` | Accepted | `edusis:school` |
| **[ADR-032](./ADR-032-sarangenge-as-unified-baseline-theme-and-legacy-theme-pruning.md)** | Sarangenge Sebagai Baseline Theme Tunggal & Decommission Tema Lama | Accepted | `edusis:theme` |

---

## 🛡️ 2. Platform Core Kernel, IAM & Security Foundation

Keputusan teknis pada fondasi kernel platform, manajemen hak akses, keamanan, dan integrasi:

| Nomor | Judul Keputusan | Status | Scope |
| :--- | :--- | :--- | :--- |
| **[ADR-013](./ADR-013-console-sidebar-database-menu-preloading-and-reactive-hydration.md)** | Console Sidebar Database Menu Preloading & Reactive Hydration | Accepted | `core` |
| **[ADR-014](./ADR-014-three-tier-identity-whitelabel-brand-and-smart-sync.md)** | 3-Tier Identity, White Label & Smart Brand Sync | Accepted | `core` |
| **[ADR-015](./ADR-015-favicon-isolation-prepaint-guards-and-zero-race-lifecycle.md)** | Favicon Isolation Prepaint Guards & Zero-Race Lifecycle | Accepted | `core` |
| **[ADR-016](./ADR-016-module-aware-site-identity-and-dependency-orchestration.md)** | Module-Aware Site Identity & Dependency Orchestration | Accepted | `core` |
| **[ADR-017](./ADR-017-console-appearance-dual-mode-workspace-and-license-gating.md)** | Console Appearance Dual-Mode Workspace & License Gating | Accepted | `core` |
| **[ADR-019](./ADR-019-user-email-verification-and-privilege-lifecycle.md)** | User Email Verification & Privilege Lifecycle | Accepted | `core` |
| **[ADR-020](./ADR-020-rbac-hierarchy-route-hardening-and-ui-primitives.md)** | RBAC Hierarchy, Route Hardening & UI Primitives | Accepted | `core` |
| **[ADR-021](./ADR-021-capability-registry-auto-discovery-and-sso-scope-mapping.md)** | Capability Registry, Manifest & SSO Scope Mapping | Accepted | `core` |
| **[ADR-026](./ADR-026-three-tier-license-simplification-and-capability-matrix.md)** | Standarisasi Lisensi 3-Tier (Community, Pro, Enterprise) dan Penegakan Matriks Kapabilitas | Accepted | `core` |

---

## 🎨 3. Layout, Customizer & Antarmuka Publik

Keputusan teknis pada mesin tema, customizer live preview, dan komponen UI publik:

| Nomor | Judul Keputusan | Status | Scope |
| :--- | :--- | :--- | :--- |
| **[ADR-002](./ADR-002-customizer-page-isolation-and-site-branding.md)** | Customizer Page Isolation & Site Branding | Accepted | `core` |
| **[ADR-003](./ADR-003-customizer-canvas-proportional-zoom-and-mockup-frames.md)** | Customizer Canvas Proportional Zoom & Mockup Frames | Accepted | `core` |
| **[ADR-004](./ADR-004-floating-social-dock-and-customizer-controls.md)** | Floating Social Dock & Customizer Controls | Accepted | `core` |
| **[ADR-005](./ADR-005-brand-visual-styles-live-preview-sync-and-smart-poppers.md)** | Brand Visual Styles Live Preview Sync & Smart Poppers | Accepted | `core` |
| **[ADR-006](./ADR-006-master-layout-modes-and-homepage-sections.md)** | Master Layout Modes & Homepage Sections | Accepted | `core` |
| **[ADR-022](./ADR-022-upstream-core-curation-and-generic-theme-seeder-architecture.md)** | Upstream Curation, Zero Client Hardcoding & Generic Theme Demo Seeders | Accepted | `core` |
| **[ADR-023](./ADR-023-first-party-themes-as-registry-packs-and-license-quotas.md)** | First-Party Themes as Registry Packs & License Quotas | Accepted | `core` |
| **[ADR-024](./ADR-024-header-menu-alignment-customizer-controls-and-layout-parity.md)** | Header Menu Alignment Customizer Controls & Multi-Theme Layout Parity | Accepted | `layout` |
| **[ADR-025](./ADR-025-theme-customizer-default-mode-and-locale.md)** | Theme Customizer Default Mode, Site Locale, and Runtime Prepaint Resilience | Accepted | `layout` |
| **[ADR-027](./ADR-027-theme-customizer-properties-order-and-contact-map-parity.md)** | Theme Customizer Properties Ordering Hierarchy and Contact Map Parity | Accepted | `layout` |
| **[ADR-028](./ADR-028-menu-builder-properties-mega-menu-and-multi-theme-parity.md)** | Menu Builder Properties, Mega Menu Architecture, and Multi-Theme Parity | Accepted | `layout` |

---

## 📌 4. Nomor ADR yang Didekomisi / Dicadangkan (Reserved)

Nomor ADR berikut tetap dicadangkan untuk menjaga integritas riwayat arsitektur dan tidak digunakan ulang:
- **ADR-001, ADR-007, ADR-008**: Dialokasikan pada tema legacy `layung` yang telah didekomisi dan dihapus per ADR-032.
- **ADR-009**: Dialokasikan pada downstream eksternal `k2net-portal` (Simulator Bandwidth ISP) yang terpisah dari ekosistem EduSIS.
- **ADR-010**: Visual Builder Viewport Preview Scaling & Adaptive Toolbar — didekomisi bersama fitur Visual Builder (`JA-Builder`) yang dihapus dari JA-EduSIS.
- **ADR-011**: Visual Builder Modular Extension & License Gating — didekomisi bersama penghapusan ekstensi `visual-builder` dari codebase EduSIS.
- **ADR-012**: Data Model Studio Modular Extension & License Gating — didekomisi bersama penghapusan ekstensi `data-studio` dari codebase EduSIS.
- **ADR-018**: Platform Plugin SoC/SoT & Cinematic Nav (`cinematic-nav`) — didekomisi bersama penghapusan fitur `cinematic-nav` (DotNav) dan tema legacy (`janari`, `layung`, `sareupna`) per ADR-032.
