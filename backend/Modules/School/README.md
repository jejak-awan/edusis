# School Module (JA-EduSIS Backend)

Modul enterprise Sistem Informasi Sekolah (SIS / EduSIS) berbasis Laravel 11 dan PostgreSQL pada ekosistem Jejakawan OS. Mematuhi prinsip **Separation of Concerns (SoC)**, **Single Source of Truth (SoT)**, arsitektur Thin Controllers, pure Service Layer dengan database transactions, Spatie RBAC, dan zero fallback hacks.

## Boundary & Sub-Domain

Modul ini mengelola 12 sub-domain fungsional sekolah:
- **Akademik (`Academic/`)**: Tahun Ajaran, Semester, Jurusan/Kompetensi Keahlian, Rombel, Mata Pelajaran, Penjadwalan Mingguan, Jurnal Mengajar Pendidik, Asesmen TP (Tujuan Pembelajaran), Pengolahan Buku Raport Merdeka, Projek Penguatan Profil Pelajar Pancasila (P5).
- **Kesiswaan (`Student/`)**: Buku Induk Siswa (Dapodik & Ortu), Mutasi Poin Pelanggaran & Prestasi Disiplin, Penanganan SP1/SP2/SP3, Ekstrakurikuler.
- **Bimbingan Konseling (`Counseling/`)**: Layanan Konseling Kasus Siswa dengan proteksi kerahasiaan `is_confidential`.
- **Tata Usaha & Administrasi (`Administration/`)**: Manajemen Buku Tamu Digital, Penomoran Surat Resmi Otomatis, Disposisi Surat Masuk & Keluar, PPDB (Penerimaan Peserta Didik Baru).
- **E-Voting & Kesiswaan (`StudentAffairs/` & `Voting/`)**: Pemilihan Ketua OSIS / E-Voting, Generator DPT Token Acak Unik (8 karakter), Anti Double-Voting, Bilik Suara Realtime Quick Count.
- **Sarana & Prasarana (`Facilities/`)**: Manajemen Ruang Kelas/Lab, Inventaris Aset, Tiket Perbaikan Kerusakan (Maintenance).
- **SDM / PTK (`HR/`)**: Direktori Pendidik & Tenaga Kependidikan, Presensi Harian Guru.
- **Integrasi Eksternal (`Integration/` & `Communication/`)**: Katampi SSO / Roster Sync / Grade Sync, WhatsApp Gateway Notifikasi Presensi Siswa ke Orang Tua.

## Arsitektur Teknis

1. **Multi-Tenancy**: Seluruh tabel diisolasi per sekolah menggunakan foreign key `school_id`. Resolusi tenant ditangani secara terpusat oleh `SchoolContextMiddleware` dan `ScopedBySchool` trait. Dilarang keras menggunakan fallback `School::first()`.
2. **Thin Controllers**: Seluruh controller di `app/Http/Controllers/Api/` bertindak murni sebagai dispatcher HTTP (validasi via FormRequest, delegasi bisnis ke Service, standardisasi JSON response).
3. **Pure Service Layer**: Seluruh logika bisnis, kalkulasi nilai raport, mutasi poin, dan sinkronisasi transaksional dibungkus dalam `DB::transaction()` di `app/Services/`.
4. **FormRequest Validation**: Seluruh mutasi data divalidasi via FormRequest di `app/Http/Requests/` dengan dukungan aturan validasi nasional (`ValidNISN`, `ValidNIK`, `ValidPhoneNumber`).
5. **Kebijakan Otorisasi (Policies)**: Dilindungi oleh Spatie Permissions dan Model Policies di `app/Policies/` (`StudentPolicy`, `AcademicPolicy`, `CounselingPolicy`, `SarprasResourcePolicy`, `VotingPolicy`).

## Pengujian Otomatis

Modul ini memiliki cakupan pengujian menyeluruh (50 Feature & Unit Tests, 500+ assertions):
```bash
php artisan test Modules/School
```

## Referensi & ADR
- [ADR-031: School Module SoC, SoT, and Reference Alignment](../../../docs/adr/ADR-031-school-module-soc-sot-and-reference-alignment.md)
- [Comprehensive Reference Mapping](../../../docs/sis/07-comprehensive-reference-mapping-and-alignment.md)
