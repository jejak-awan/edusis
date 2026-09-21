# 01. Overview & Architecture — JA-EduSIS

> Navigasi docs: [`DOCUMENTATION.md`](../DOCUMENTATION.md) · School Domain: [`docs/sis/`](../sis/) · ADRs: [`adr/README.md`](../adr/README.md)

## 🌟 1. Ikhtisar Sistem
**JA-EduSIS (`ja-edusis`)** (*Jejakawan Educational School Information System*) adalah platform Sistem Informasi Sekolah (SIS) dan Manajemen Akademik modern berbasis arsitektur **Modular Monolith** yang dibangun di atas fondasi **Jejakawan Core Engine**. Dirancang untuk performa tinggi, isolasi domain yang kokoh, serta keandalan tata kelola data & infrastruktur pendidikan.

Sistem ini menggabungkan:
- **Backend**: Laravel **13** (PHP 8.2+, tested on 8.3) dengan pemisahan modul yang jelas di `backend/Modules/{Core,School,Community}`.
- **Frontend**: Vue 3.5 Single Unified Console SPA + TypeScript + Vite 8 + Tailwind CSS v4.
- **Database**: PostgreSQL 16+ (atau SQLite untuk automated testing) dengan skema terisolasi `edusis` (`sch_*`) serta Redis/Valkey untuk caching, session, dan queuing.
- **Decoupled Assessment**: Mesin ujian CBT/CAT dialokasikan secara independen ke **JA-Katampi** (Aksara Engine) di host terpisah via API token & SSO.

---

## 🏛️ 2. Arsitektur Domain JA-EduSIS

Struktur modular monolith JA-EduSIS dibagi ke dalam 3 tier domain utama:

```
                           ┌───────────────────────────┐
                           │         JA-EduSIS         │
                           └─────────────┬─────────────┘
                                         │
          ┌──────────────────────────────┼──────────────────────────────┐
          │                              │                              │
          ▼                              ▼                              ▼
┌──────────────────┐          ┌───────────────────┐          ┌───────────────────┐
│   Core Kernel    │          │     School SIS    │          │     Community     │
├──────────────────┤          ├───────────────────┤          ├───────────────────┤
│ • User & IAM     │          │ • Akademik & Mapel│          │ • Portal Alumni   │
│ • RBAC + ABAC    │          │ • Siswa & Rombel  │          │ • Ekstrakurikuler │
│ • System Settings│          │ • Kurikulum Merde.│          │ • Komite Sekolah  │
│ • Data Studio    │          │ • Presensi Geofenc│          │ • Hub Informasi   │
│ • Task Crons     │          │ • Poin & BK       │          └───────────────────┘
│ • Redis & SIEM   │          │ • Rapor & Ledger  │
└──────────────────┘          │ • Persuratan & TU │
                              └───────────────────┘
```

### A. School SIS Domain (`Modules/School/`)
Mengelola seluruh alur operasional pendidikan berstandar Dapodik Kemendikbudristek dan Kurikulum Merdeka:
1. **Akademik & Kurikulum**:
   - Tahun Ajaran, Semester, Mata Pelajaran, Jadwal Pembelajaran, Rombongan Belajar (Rombel).
   - Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP) Kurikulum Merdeka.
2. **Kesiswaan & BK**:
   - Induk Siswa (NISN, NIK, riwayat keluarga, mutasi siswa).
   - Tata Tertib, Sistem Poin Pelanggaran/Prestasi, dan Jurnal Bimbingan Konseling (BK).
3. **Presensi Terpadu**:
   - Presensi Harian Siswa & Guru berbasis Geofencing dan Jam Sekolah.
   - Dilengkapi *circuit breaker* otomatis untuk menjaga stabilitas polling antarmuka.
4. **Penilaian & Rapor**:
   - Asesmen Formatif & Sumatif, Ledger Nilai, Cetak Rapor Resmi Kurikulum Merdeka.
5. **Administrasi & Tata Usaha (TU)**:
   - Persuratan Dinas, Penomoran Surat Otomatis, Manajemen Sarana & Prasarana (Sarpras).

### B. Core Kernel Domain (`Modules/Core/`)
Menyediakan fondasi tata kelola sistem operasi aplikasi, otentikasi, dan konfigurasi global:
1. **IAM & Security**:
   - User Accounts (`srv_auth_users`), Role Hierarchy (Spatie RBAC), Permission Registry, ABAC Policies.
   - Dual Login support: Email atau Username. TOTP 2FA & WebAuthn Passkeys.
2. **System & Infrastructure**:
   - Dynamic Settings dengan in-memory caching.
   - Data Studio, Task Scheduler & Cron runner, Backup & Snapshot Manager, Webhook Dispatcher.
   - Redis Manager dengan monitoring memori real-time.
3. **Audit & Observability**:
   - SIEM Security Audit Logs, full activity journaling, dan CSP (Content Security Policy) report collector.

### C. Community Domain (`Modules/Community/`)
1. **Portal Komunitas**: Forum dan ruang kolaborasi sekolah, komite, dan orang tua.
2. **Alumni & Ekstrakurikuler**: Direktori alumni, agenda kegiatan, dan kepengurusan OSIS/ekskul.

---

## 🔒 3. Prinsip Isolasi Data & Multi-Schema

Untuk menjamin integritas data institusi dan performa:
- `ja-edusis` menggunakan skema PostgreSQL terisolasi: **`edusis`** (`DB_SCHEMA=edusis`).
- Semua entitas domain sekolah menggunakan prefiks tabel standar **`sch_*`** (contoh: `sch_students`, `sch_academic_years`, `sch_attendances`).
- Database Redis/Valkey menggunakan dedicated index (**`REDIS_DB=6`**, **`REDIS_CACHE_DB=7`**) dengan prefix unik **`ja_edusis_cache:`**.
- Mesin CBT terpisah di host `ja-aksara` mencegah beban komputasi ujian membebani database administratif sekolah.

