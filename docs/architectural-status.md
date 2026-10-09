# Architectural Status — JA-EduSIS (`ja-edusis`)

Update: 2026-09-21 · Versi Produk: **v1.0.0-beta.2**  
Canonical trunk: **`main`** (`git@github.com:jejak-awan/edusis.git`)  
Arsitektur Induk: **Jejakawan Core Engine (Hard-Isolated)** per [ADR-029](adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md) & [ADR-030](adr/ADR-030-school-module-scaffold-and-subdomain-architecture.md)

---

## 🏛️ 1. Domain Arsitektur Aktif

Aplikasi beroperasi dalam arsitektur **Modular Monolith** dengan pemisahan domain yang ketat:

| Domain Layer | Status | Modul Backend (`backend/Modules/`) | Modul Frontend (`frontend/src/modules/`) | Peran & Ruang Lingkup |
| :--- | :--- | :--- | :--- | :--- |
| **Core Kernel** | **Live** | `Core/app/{System,Infra,Security}` | `Core/{System,Infra,Security}` | IAM console (`srv_auth_users`), RBAC/ABAC, System Settings, Audit SIEM, Webhook, Redis Engine, Task Crons. |
| **School SIS** | **Live** | `School/` (`app/{Academic,Student,Staff,Letter,PPDB,Facility,Council,Attendance,Grading}`) | `School/` (`views/{academic,student,staff,attendance,grading,rapor}`) | Manajemen operasional sekolah: Kurikulum Merdeka, entitas Dapodik (`sch_*`), presensi geofencing, poin kesiswaan & BK, penilaian rapor/ledger. |
| **Community** | **Live** | `Community/` | `Community/` | Portal komunitas sekolah, alumni, komite sekolah, dan ekstrakurikuler. |
| **Extensions** | **Live** | `Extensions/` | `Extensions/` | Plugin sandbox, integrasi JA-Mail transaksional, webhook external. |

---

## ⚡ 2. CBT Engine Decoupling (KataMPI Integration)

Sesuai mandat [ADR-029](adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md) dan [`docs/sis/03-integration-katampi-cbt.md`](sis/03-integration-katampi-cbt.md):
- **JA-EduSIS TIDAK memuat mesin ujian komputer (CBT)** secara monolitik di backend.
- Seluruh ujian online, bank soal, anti-cheat, dan evaluasi real-time dialokasikan ke **JA-Katampi** yang berjalan di node terdedikasi (`ja-aksara` / CT 204).
- Integrasi dilakukan via API Token, SSO, dan sinkronisasi data peserta/jadwal ujian.

---

## 🗄️ 3. Skema Database & Caching

| Komponen | Identitas / Konfigurasi | Deskripsi |
| :--- | :--- | :--- |
| **Database Skema** | PostgreSQL 16 `edusis` | Seluruh tabel sekolah menggunakan prefiks resmi `sch_*` (contoh: `sch_students`, `sch_academic_years`). Tabel kernel menggunakan `sys_*`, `srv_*`. |
| **Valkey / Redis** | CT 206 (`10.20.0.206:6379`) | Dedicated index: `REDIS_DB=6`, `REDIS_CACHE_DB=7`, prefix cache `ja_edusis_cache:`. Terlindungi dengan *circuit breaker* otomatis pada dashboard. |
| **Session & Cookie** | Domain `edusis.example.com` | Redis session driver, secure cookies, strict CSRF validation. |

---

## 🌐 4. Lingkungan Dev & Preview Runtime

- **Host Pengembangan**: `ja-dev` (CT 207 — `192.168.88.71`)
- **Frontend Console SPA**: Port **`5373`** (Vite 8, Single Unified SPA). Port `5273` dipakai `ja-spek`.
- **Backend API**: Port **`8100`** (Laravel 13 serve). Port `8000` dipakai `ja-spek`.
- **Production Host**: `ja-srv` (CT 200 — `192.168.88.66`) via `publish-from-jadev.sh`
- **CBT Host**: `ja-aksara` (CT 204 — `10.20.0.204`) untuk KataMPI Engine

---

## 🛡️ 5. Standar Tata Kelola & Verifikasi Kualitas

Setiap agen pengembang wajib memastikan:
1. **Verifikasi Kualitas**: `npm run agent:verify` (termasuk validasi 293+ link dokumentasi dan SemVer `1.0.0-beta.2`).
2. **Backend Testing**: `npm run test:backend` (51 Pest feature/unit tests lulus).
3. **Frontend Type Check**: `npm run type-check` (`vue-tsc` 0 errors).
4. **Proteksi PII Siswa**: NISN, NIK, nilai rapor, dan rekam medis/BK terlindungi RBAC ketat dan tidak diekspos ke publik.
5. **Konvensi Git**: Wajib menggunakan format Conventional Commits `feat(...)`.

