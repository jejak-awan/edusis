# JA-EduSIS (`ja-edusis`)

<p align="center">
  <strong>Enterprise-Grade Integrated School Information System, Academic Engine & Educational Community Portal</strong>
</p>

<p align="center">
  <a href="#-ringkasan-sistem">Ringkasan</a> •
  <a href="#-fitur-utama">Fitur Utama</a> •
  <a href="#-arsitektur-sistem">Arsitektur</a> •
  <a href="#-struktur-direktori">Struktur Direktori</a> •
  <a href="#-panduan-menjalankan-dev-server">Instalasi & Dev</a> •
  <a href="#-quality-gates--pengujian">Quality Gates</a> •
  <a href="#-dokumentasi--referensi">Dokumentasi</a>
</p>

---

## 📌 Ringkasan Sistem

| Komponen | Spesifikasi |
| :--- | :--- |
| **Produk** | **JA-EduSIS** (*Jejakawan Educational School Information System*) |
| **Repositori** | `ja-edusis` (Hard-isolated repo, turunan Core Engine per [ADR-029](docs/adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md)) |
| **Arsitektur** | Modular Monolith (14 Modul Backend Laravel + 13 Modul Frontend Vue 3 SPA + Unified Public Portal) |
| **Versi** | `1.0.0-beta.2` |
| **Database Engine** | PostgreSQL 16+ / 18.x (Schema terisolasi **`edusis`**) |
| **Runtime Backend** | PHP 8.2+ / 8.3+ (Laravel 13 Modular Kernel) |
| **Runtime Frontend** | Node.js >=22.12.0 (Vue 3.5, Vite 8, Tailwind CSS v4, TypeScript) |
| **Tema Publik Baseline**| **Sarangenge** (Portal Sekolah & Kampus Terpadu per [ADR-032](docs/adr/ADR-032-unified-school-campus-theme-baseline-sarangenge.md)) |
| **Mesin Evaluasi CBT** | Didelegasikan ke **JA-Katampi** (Kluster Aksara CBT Engine; sinkronisasi via Webhook / REST) |
| **Kredensial Default** | `admin@edusis.local` / `password` |
| **Lisensi** | Proprietary — Jejakawan Ecosystem |

---

## 🌟 Fitur Utama

### 1. 🎓 School Information System Core (`Modules/School`)
- **Struktur Kelembagaan & Dapodik:**
  - Multi-jenjang (SD/SMP/SMA/SMK), Tahun Ajaran, Semester, Jurusan/Program Keahlian, dan Rombel (Rombongan Belajar).
  - Skema data patuh format atribut Dapodik Kemendikbudristek (tabel berprefix `sch_*`).
- **Akademik & Kurikulum Merdeka:**
  - Capaian Pembelajaran (CP) & Tujuan Pembelajaran (TP).
  - Jurnal Mengajar Harian Guru, Jadwal Pelajaran mingguan, dan Presensi Siswa per jam pelajaran.
  - Asesmen Sumatif & Formatif, Pengolahan Nilai Akhir, Leger Nilai, dan Cetak Raport Kurikulum Merdeka (termasuk modul Projek Penguatan Profil Pelajar Pancasila / P5).
- **Kesiswaan & Pembinaan Karakter:**
  - Buku Induk Siswa terpadu dan Rekam Riwayat Prestasi.
  - Sistem Kredit Poin Disiplin Akumulatif (skema bertingkat SP1, SP2, SP3) dengan penanganan kasus Bimbingan Konseling (BK).
- **Pendidik & Tenaga Kependidikan (GTK / Kepegawaian):**
  - Database Guru dan Staf TU, Presensi Kehadiran berbasis shift, dan monitoring beban ajar.
- **Tata Usaha, Persuratan & Sarana Prasarana:**
  - Buku Tamu Digital dengan rekam kedatangan tamu sekolah.
  - Administrasi Persuratan Kedinasan (Surat Masuk, Surat Keluar, Penomoran Otomatis, dan Alur Disposisi).
  - Sarpras: Inventaris Aset Ruangan dan Tiket Perbaikan/Maintenance fasilitas sekolah.
- **Layanan Khusus & Integrasi Ekosistem:**
  - **PPDB Online:** Formulir pendaftaran calon peserta didik baru, verifikasi berkas, dan pengumuman seleksi.
  - **E-Voting OSIS:** Pemilihan Ketua & Pengurus OSIS terintegrasi verifikasi pemilih berbasis token unik.
  - **WhatsApp Gateway:** Notifikasi otomatis presensi dan rekap nilai ke wali murid.
  - **Sinkronisasi KataMPI CBT:** Integrasi API/Webhook dua arah untuk data siswa, rombel, dan penarikan nilai ujian dari engine CBT KataMPI.

### 2. 🏛️ Portal Web & Komunitas Pendidikan (`Layout`, `Publishing`, `Member`)
- **Sarangenge Theme Baseline:** Portal publik sekolah yang modern, dinamis, dan ramah seluler:
  - Hero banner, sambutan kepala sekolah, agenda kalender akademik, warta prestasi, dan galeri ekstrakurikuler.
  - Widget interaktif: pengumuman penting, statistik sekolah, dan transparansi publik.
- **Warta & Publikasi Sekolah:** Modul berita, artikel guru/siswa, kategori, tag, dan moderasi komentar.
- **Portal Siswa & Orang Tua (Member Portal):** Dashboard mandiri untuk mengecek rekap kehadiran, histori poin kedisiplinan, jadwal pelajaran, dan pengumuman kelas.

### 3. 🛡️ Platform Kernel, Keamanan & Tata Kelola (`Core`)
- **Hierarki Peran (RBAC) Granular:** Hak akses spesifik untuk *Super Admin*, *Kepala Sekolah*, *Wakasek Kurikulum*, *Wakasek Kesiswaan*, *Guru Mapel*, *Wali Kelas*, *Staf TU*, *Siswa*, dan *Orang Tua*.
- **Enterprise Security:** Proteksi brute-force, IP rate-limiting, Bot Shield, audit log aktivitas pengguna (SIEM), autentikasi 2FA, dan WebAuthn Passkeys.
- **Data Studio & Manajemen Sistem:** Dynamic entity modeler, automated cron monitoring, backup database terisolasi, dan Redis cache explorer.

---

## 🛠️ Struktur Direktori

```
ja-edusis/
├── backend/                      # Laravel 13 Modular Architecture
│   ├── app/                      # HTTP Kernel, Middleware, Exception Handlers
│   ├── database/                 # Migrations & Seeders
│   ├── Modules/                  # 14 Modul Backend Terintegrasi:
│   │   ├── School/               # Core SIS: Academic, Student, HR, Fac, Adm, Vote, Comm
│   │   ├── Layout/               # Theme Engine & Sarangenge Baseline Theme
│   │   ├── Publishing/           # Portal Warta, Berita, Artikel & Komentar
│   │   ├── Member/               # Portal Siswa & Orang Tua
│   │   ├── Core/                 # System, IAM/RBAC, Infra, Security, Data Studio
│   │   ├── Analytics/            # Statistik & Kunjungan Portal
│   │   ├── Forms/                # Formulir Dinamis & Angket
│   │   ├── Media/                # Media Library & Manajemen Dokumen
│   │   ├── Library/              # Metadata & Tagging Engine
│   │   ├── Mail/                 # Transaksional Email (JA-Mail)
│   │   ├── Newsletter/           # Buletin & Langganan Informasi
│   │   ├── CmsAi/                # Asisten AI Konten & Taksonomi
│   │   ├── Search/               # Global Search Indexing
│   │   └── Site/                 # Site Pack Management
│   └── routes/                   # Route Registrars
├── frontend/                     # Vue 3.5 + Vite 8 SPA
│   ├── src/
│   │   ├── modules/              # 13 Modul Frontend Vue (School, Layout, Publishing, dll.)
│   │   ├── themes/               # Theme Runtime (sarangenge)
│   │   ├── engine/               # State Store (Pinia), Router, API Client, I18n
│   │   └── shared/               # UI Design System & Reusable Components
│   └── tests/                    # Vitest & Component Tests
├── docs/                         # Dokumentasi Resmi JA-EduSIS (sis/, adr/, architecture/, guides/)
└── scripts/                      # Otomasi Deployment, Node 22 Runner, dan Quality Gate
```

---

## 🚀 Panduan Menjalankan Dev Server

Proyek ini mewajibkan **Node.js >=22.12.0** dan **PHP 8.2+**.

### 1. Persiapan Lingkungan
```bash
# Pastikan Node.js 22 aktif
node -v # >= 22.12.0 (gunakan nvm use 22 atau scripts/use-node22.sh)
```

### 2. Backend (Laravel)
```bash
cd backend
composer install
php artisan migrate --force
php artisan db:seed --force
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Frontend (Vite Console & Portal)
```bash
cd frontend
npm install
npm run dev # Berjalan pada http://localhost:5273
```

---

## 🧪 Quality Gates & Pengujian

Sebelum melakukan commit kode atau deployment, jalankan serangkaian quality gates standar:

```bash
# 1. Jalankan Verifikasi Menyeluruh (Wajib Pass)
npm run agent:verify

# 2. Pengujian Unit & Static Analysis Backend
npm run quality:backend
npm run test:backend

# 3. Pengujian Tipe & Linter Frontend
npm run quality:frontend
```

---

## 📚 Dokumentasi & Referensi

- [docs/AGENT_START_HERE.md](docs/AGENT_START_HERE.md) — Panduan awal dan batasan arsitektur agen
- [docs/README.md](docs/README.md) — Indeks navigasi seluruh dokumen arsitektur
- [docs/sis/01-overview-and-domain-map.md](docs/sis/01-overview-and-domain-map.md) — Peta domain 7 sub-domain SIS
- [docs/sis/03-integration-katampi-cbt.md](docs/sis/03-integration-katampi-cbt.md) — Batasan integrasi JA-EduSIS vs KataMPI CBT
- [docs/adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md](docs/adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md) — Keputusan Arsitektur Resmi EduSIS
- [docs/adr/ADR-032-unified-school-campus-theme-baseline-sarangenge.md](docs/adr/ADR-032-unified-school-campus-theme-baseline-sarangenge.md) — Baseline Tema Sarangenge
