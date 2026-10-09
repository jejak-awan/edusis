# Git Branching & Multi-Repository Architecture — `JA-EduSIS & Jejakawan Ecosystem`

**Update:** 2026-09-21 (Pasca Pembentukan JA-EduSIS — ADR-029 & ADR-030)

Ekosistem Jejakawan beroperasi dengan model **Upstream Core Engine + Vertikal Khusus & Downstream Deployments**:

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                          UPSTREAM CORE REPOSITORY                               │
│              ja-core_engine (git@github.com:jejak-awan/ja-core_engine.git)       │
│              Branch: main                                                       │
│              - Kernel, IAM, System, Security, RBAC Registry, Extension Engine   │
│              - ZERO Client Data / 100% Platform Agnostic                        │
└────────────────────────────────────────┬────────────────────────────────────────┘
                                         │
        ┌────────────────────────────────┼────────────────────────────────┐
        ▼                                ▼                                ▼
┌──────────────────────────┐ ┌──────────────────────────┐ ┌──────────────────────────┐
│   DOWNSTREAM PORTALS     │ │      JA-KATAMPI          │ │       JA-EDUSIS        │
│ k2net-portal / smkn6     │ │ ja-katampi (Aksara Engine│ │ ja-edusis (origin: edusis)│
│ (Portal Publik & CMS)    │ │ CBT/CAT Online Exam)     │ │ Branch: main              │
│ Sinkronisasi: git merge  │ │ Node: ja-aksara (CT 204) │ │ Model: Hard-Isolation     │
│ upstream main            │ │ Terpisah dari SIS        │ │ Patching: Cherry-pick only│
└──────────────────────────┘ └──────────────────────────┘ └──────────────────────────┘
```

---

## 1. Matriks & Peran Repositori

| Repositori | Peran / Scope | Branch Utama | Remote Git | Kebijakan Sinkronisasi |
| :--- | :--- | :--- | :--- | :--- |
| **`ja-edusis`** | **Sistem Informasi Sekolah Terpadu (SIS)** | `main` | `git@github.com:jejak-awan/edusis.git` | **Hard-Isolated** (Selective cherry-pick bugfix kernel saja; **dilarang** global merge upstream). |
| **`ja-katampi`** | **CBT & Assessment Engine (Aksara)** | `main` | `git@github.com:jejak-awan/ja-katampi.git` | Layanan mandiri terpisah di host `ja-aksara`, dihubungkan via API token & SSO. |
| **`ja-core_engine`** | **Upstream Kernel Foundation** | `main` | `git@github.com:jejak-awan/ja-core_engine.git` | Induk fondasi IAM, Security, dan modul platform. |

---

## 2. Batasan Kode JA-EduSIS (*Code Demarcation*)

### A. Modul Internal JA-EduSIS:
1. **School SIS Domain (`Modules/School/`)**:
   - Skema entitas standar Dapodik (`sch_*`).
   - Manajemen Akademik, Rombel, Kurikulum Merdeka, Presensi Sekolah (dengan geofencing & circuit breaker), Poin Kesiswaan/BK, dan Penilaian Rapor.
2. **Community Domain (`Modules/Community/`)**:
   - Portal komunitas alumni, ekstrakurikuler, dan forum komite sekolah.
3. **Core Kernel Foundation (`Modules/Core/`)**:
   - IAM, RBAC/ABAC, System Settings, Audit Logs, Webhook, Redis Manager.

### B. Batasan Decoupled CBT (Dilarang Monolitik):
> [!IMPORTANT]
> **DECOUPLED CBT ENGINE:**  
> Seluruh fungsionalitas Computer Based Test (CBT), bank butir soal, delivery soal real-time, anti-cheat browser lockdown, dan auto-grading ujian online **TIDAK BOLEH** dibangun sebagai modul monolitik di dalam `ja-edusis`. Fitur tersebut sepenuhnya dikelola oleh engine **JA-Katampi** di host Aksara (`ja-aksara`).

### C. Perlindungan Privasi Data (PII):
> [!CAUTION]
> **PROTEKSI DATA PRIBADI SISWA & GURU:**  
> Dilarang keras melakukan hardcoding atau commit data pribadi riil (NISN, NIK, alamat rumah, nomor HP orang tua, atau nilai rapor riil) ke repositori publik. Seluruh data pengujian wajib menggunakan database faker atau seeder generik.

---

## 3. Kebijakan Percabangan Git (*Branching Policy*)

- **Trunk Utama**: `main` (selalu siap deploy).
- **Feature / Bugfix**: Cabang pendek dari `main` (misal: `feat/sch-attendance-flow` atau `fix/redis-circuit-breaker`), diselesaikan via PR/commit terstruktur.
- **Konvensi Pesan Commit**: Wajib menggunakan format Conventional Commits `feat(...)` / `fix(...)`.
- **Aturan Upstream**:
  - **Dilarang** menjalankan `git merge upstream/main` ke repositori ini.
  - Jika terdapat perbaikan krusial pada kernel `ja-core_engine`, agen hanya diperbolehkan menyerap perbaikan tersebut menggunakan `git cherry-pick <commit-hash>`.

---

## 4. Topologi Server & Lingkungan Staging

### Host: `ja-dev` (CT 207 — `192.168.88.71` / `10.20.0.207`)
Host utama pengembangan dan verifikasi:
- **JA-EduSIS Web Console**: Port **`5373`** (Vite 8 SPA)
- **JA-EduSIS Backend API**: Port **`8100`** (Laravel 13 API)
- **PostgreSQL Database**: Port `5432`, Skema `edusis`
- **Valkey / Redis Hub**: Container CT 206 (`10.20.0.206:6379`), DB 6 & 7

### Host: `ja-aksara` (CT 204 — `192.168.88.68` / `10.20.0.204`)
Host dedicated untuk mesin ujian CBT (**JA-Katampi** / CBT Engine).

### Host: `ja-srv` (CT 200 — `192.168.88.66` / `10.20.0.200`)
Host **runtime produksi** (`/home/jejakawan/www/...`). Deployment dilakukan dari `ja-dev` via script sinkronisasi.

---

## 5. Quality Gate Sebelum Push (*Checklist*)

Setiap agen wajib memverifikasi:
- [ ] `npm run docs:links` — Tidak ada link dokumentasi yang rusak.
- [ ] `npm run versions:check` & `npm run modules:versions:check` — Konsistensi SemVer `1.0.0-beta.2`.
- [ ] `npm run test:backend` — Seluruh Pest feature/unit tests lulus.
- [ ] `npm run type-check` — Pemeriksaan tipe TypeScript frontend bersih (0 error).

