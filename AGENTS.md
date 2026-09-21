# Panduan Agen — JA-EduSIS (`ja-edusis`)

> **MANDAT UNTUK SEMUA AGEN AI (Antigravity, Cursor, & Coding Assistant)**  
> Repositori: `/home/jejakawan/dev/ja-edusis`  
> Remote Git: `origin` → `git@github.com:jejak-awan/edusis.git` (Branch: `main`)  
> Peran: **Enterprise School Information System (SIS), Academic Management Engine & Educational Community Portal**  
> Database: PostgreSQL (Skema terisolasi **`edusis`**) · Valkey CT 206 (`10.20.0.206:6379`)  

---

## 1. Wajib Dibaca Sebelum Mulai (*Mandatory Reading*)

1. [`docs/AGENT_START_HERE.md`](docs/AGENT_START_HERE.md) — Cerita produk, domain sintesis, dan peta arsitektur.
2. [`docs/sis/01-overview-and-domain-map.md`](docs/sis/01-overview-and-domain-map.md) — Peta domain sistem informasi sekolah & standar Dapodik.
3. [`docs/sis/03-integration-katampi-cbt.md`](docs/sis/03-integration-katampi-cbt.md) — Batasan arsitektur SIS vs KataMPI CBT Engine.
4. [`docs/WORKFLOW.md`](docs/WORKFLOW.md) — Urutan kerja: audit → rencana → implementasi → verifikasi → catat.
5. [`docs/DOCUMENTATION.md`](docs/DOCUMENTATION.md) — Standar dokumentasi, SoT/SoC, dan aturan ADR.
6. [`.cursor/rules/school-governance.mdc`](.cursor/rules/school-governance.mdc) — Aturan tata kelola modul School.

Task skala M+: Buat/isi task brief di [`docs/work/`](docs/work/README.md) dari template [`docs/templates/task-brief.md`](docs/templates/task-brief.md).

---

## 2. Batasan Arsitektur & Lingkup Repositori

### A. Yang Termasuk dalam Lingkup JA-EduSIS
* **Modul Bisnis Utama (`Modules/School`)**:
  * Struktur Kelembagaan patuh Dapodik (Tahun Ajaran, Semester, Jurusan, Rombel, Siswa).
  * Akademik Kurikulum Merdeka (CP/TP, Jurnal Mengajar, Jadwal, Presensi, Asesmen, Leger, Cetak Raport, P5).
  * Kesiswaan & Disiplin (Buku Induk, Rekam Prestasi, Poin Pelanggaran Bertingkat SP1–SP3, Kasus BK).
  * GTK & Kepegawaian (Data Guru/Staf TU, Presensi Kehadiran, Monitoring Jam Ajar).
  * Tata Usaha & Persuratan (Buku Tamu, Surat Masuk/Keluar, Penomoran Otomatis, Disposisi).
  * Sarpras (Inventaris Ruangan & Tiket Perbaikan).
  * Layanan Terpadu: PPDB Online, E-Voting OSIS, dan WhatsApp Gateway.
* **Portal Publik & Komunitas**:
  * Tema Publik Baseline: **Sarangenge** (`Modules/Layout/views/themes/sarangenge`).
  * Warta & Publikasi Sekolah (`Modules/Publishing`).
  * Member Portal Siswa & Orang Tua (`Modules/Member`).
* **Kernel & Keamanan (`Modules/Core`)**:
  * RBAC Granular (Super Admin, Kepala Sekolah, Wakasek, Guru, Staf TU, Siswa, Orang Tua).
  * Enterprise Security, Audit Log SIEM, 2FA, dan WebAuthn Passkeys.

### B. Batasan Ketat (*Non-Negotiables*)
1. ❌ **DILARANG membangun engine CBT interaktif di codebase ini**. Seluruh evaluasi ujian online didelegasikan ke **JA-Katampi** (kluster Aksara). JA-EduSIS hanya menyediakan integrasi data siswa, SSO token, dan penarikan nilai ujian.
2. ❌ **DILARANG menggunakan prefix tabel selain `sch_*`** untuk domain sekolah di dalam PostgreSQL schema `edusis`.
3. ❌ **DILARANG commit file kredensial atau `.env`**.
4. ❌ **DILARANG menurunkan standar PHPStan Level 9** atau menambahkan error suppression tanpa mitigasi.

---

## 3. Alur Kerja Git & Mandat Workspace

* **Remote & Branch**: Repositori mandiri terisolasi di `origin/main` (`git@github.com:jejak-awan/edusis.git`).
* **Konvensi Commit**: Wajib menggunakan format Conventional Commits **`feat(...)`**, contoh:
  * `feat(school): implement student attendance bulk verification`
  * `feat(system): enhance redis connection pooling circuit breaker`
* **Sinkronisasi Dokumentasi Ops**: Jika mengedit berkas di folder global `docs/` workspace (`/home/jejakawan/dev/docs`), wajib sinkronisasi multi-host sesuai aturan di [`/home/jejakawan/dev/AGENTS.md`](../AGENTS.md).

---

## 4. Quality Gates Sebelum Menyelesaikan Tugas

Setiap pekerjaan wajib diverifikasi melalui rangkaian perintah berikut:

```bash
# 1. Backend tests (Pest)
cd backend && ./vendor/bin/pest

# 2. Static analysis strict typing (PHPStan Level 9)
cd backend && ./vendor/bin/phpstan analyse Modules/School -l 9 --no-progress

# 3. Frontend TypeScript & i18n checks
cd frontend && npm run i18n:check && npx vue-tsc -b

# 4. Sinkronisasi kapabilitas RBAC jika menambah izin baru
cd backend && php artisan rbac:sync
```
