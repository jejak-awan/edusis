# Sintesis Multi-Repositori: Landasan Rancangan JA-EduSIS

Dokumen ini mencatat keputusan komparasi teknis dan sumber inspirasi dari 3 repositori referensi di [`repo_referensi/`](file:///home/jejakawan/dev/repo_referensi/) yang disintesis ke dalam `ja-edusis`.

---

## 1. Tiga Pilar Repositori Referensi

1. **`cijulang` (`k2netid/cijulang`) — Basis Arsitektur Utama**:
   - Sistem riil yang digunakan oleh SMKN 1 Cijulang.
   - Menggunakan arsitektur modular Nwidart (`Modules/School`) dan frontend Pinia Vue 3 yang 100% selaras dengan platform Jejakawan.
   - Paling kuat pada: Master data Akademik, kepatuhan Dapodik untuk Siswa, sistem HR & Payroll sekolah, dan manajemen Sarana Prasarana (Sarpras).

2. **`ja-siska` (`jejak-awan/ja-siska`) — Spesialisasi Kesiswaan & BK**:
   - Sistem fokus kesiswaan dengan aturan disiplin yang sangat matang.
   - Paling kuat pada: Mekanisme akumulasi poin kredit perilaku siswa (reward & punishment), ambang batas penerbitan Surat Peringatan (SP), alur sesi Bimbingan Konseling (BK) dengan kerahasiaan kasus (`is_confidential`), presensi berbasis QR Code, dan logging integrasi WhatsApp Gateway (`WhatsAppLog`).

3. **`ja-sis` (`jejak-awan/ja-sis`) — Spesialisasi Tata Usaha & Ekstrakurikuler**:
   - Sistem informasi sekolah berbasis Laravel 12.
   - Paling kuat pada: Administrasi persuratan TU (Surat Masuk, Surat Keluar berpenomoran otomatis, alur disposisi pimpinan), modul E-Voting Pemilihan Ketua OSIS, dan pengelolaan pendaftaran anggota ekstrakurikuler.

---

## 2. Matriks Keputusan Komponen yang Diadopsi

| Komponen Fitur | Sumber Asal | Alasan Pemilihan & Adaptasi ke EduSIS |
|---|---|---|
| **Struktur Modul & Namespace** | `cijulang` | Struktur `Modules/School` langsung kompatibel dengan kernel `ja-edusis` tanpa perlu refactoring besar. Tabel menggunakan prefix `sch_*`. |
| **Model Data Akademik & Kurikulum** | `cijulang` | Memiliki `TeachingJournal` (jurnal KBM guru) dan model `Grade` yang sudah mendukung pembagian nilai harian, sumatif, formatif, dan capaian kompetensi. |
| **Buku Induk Siswa** | `cijulang` | Struktur tabel siswa memiliki kelengkapan kolom standar Dapodik Kemendikbudristek (NIK, NISN, data detail orang tua & wali). |
| **Sistem Kredit Poin & Disiplin** | `ja-siska` | Alur skor perilaku akumulatif dengan kalkulasi poin pelanggaran dan poin prestasi yang otomatis memicu status SP (Surat Peringatan). |
| **Bimbingan Konseling (BK)** | `ja-siska` | Form sesi konseling BK dengan klasifikasi masalah, jenis intervensi, tindak lanjut, dan flag rahasia `is_confidential`. |
| **Presensi & Validasi** | `cijulang` + `ja-siska` | Skema tabel kehadiran siswa dan guru dari `cijulang`, digabungkan dengan generator QR Code presensi dan validasi dari `ja-siska`. |
| **Tata Usaha & Persuratan** | `ja-sis` | Alur Surat Masuk, Surat Keluar, Disposisi, dan Template Surat dari `ja-sis` diporting ke sub-domain `sch_ops_*`. |
| **OSIS & E-Voting** | `cijulang` + `ja-sis` | Manajemen pengurus & program kerja dari `cijulang`, dilengkapi sistem pemungutan suara (E-Voting) ketua OSIS dari `ja-sis`. |
| **HR, Payroll, & Sarpras** | `cijulang` | Satu-satunya repo yang memiliki implementasi lengkap penggajian guru, shift kerja, dan inventaris barang per ruangan. |

---

## 3. Komponen yang Dikecualikan (Excluded)

- **LMS (Learning Management System)**: Materi pembelajaran, quiz, topik pembahasan video/PDF di dalam repo referensi **dilewati** (*skipped*).
- **CBT (Computer-Based Test)**: Mesin ujian online, bank soal, dan analisis butir soal **dilewati** (*skipped*).
- **Alasan**: Seluruh fungsi evaluasi dan e-learning interaktif didelegasikan ke engine khusus **JA-KataMPI**. EduSIS fokus pada *core data & administration* sekolah.
