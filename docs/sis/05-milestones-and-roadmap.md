# Roadmap & Tahapan Pengembangan JA-EduSIS

Rencana kerja terstruktur untuk implementasi modul **JA-EduSIS** secara bertahap.

---

## Tahap 1: Pondasi Master Data Akademik & Data Siswa (Selesai)
- [x] Scaffold `Modules/School` di backend dan `src/modules/School` di frontend.
- [x] Master Data Akademik: Tahun Ajaran, Semester, Jurusan, Rombel/Kelas, Mapel.
- [x] Data Induk Siswa (Dapodik-compliant) + Manajemen Orang Tua.
- [x] Integrasi Sistem Kredit Poin Perilaku (Pelanggaran & Prestasi) dari `ja-siska`.
- [x] Seeder awal (Tahun Ajaran aktif, Rombel contoh, dan referensi kategori pelanggaran).

## Tahap 2: Manajemen Pendidik (HR) & Sistem Presensi (Fase Aktif Berikutnya)
- [ ] Data Induk Guru & Tenaga Kependidikan (Staff).
- [ ] Presensi Harian Siswa & Staff (H/I/S/A) dengan generator QR Code & validasi lampiran surat sakit.
- [ ] Jurnal KBM Guru (`TeachingJournal`) untuk pencatatan harian materi dan kehadiran siswa di kelas.
- [ ] Portal khusus Guru (`views/teacher/`).

## Tahap 3: Penilaian, Raport, & Bimbingan Konseling (BK)
- [ ] Input Nilai Guru: Formatif, Sumatif (UTS/UAS), Tugas/Portofolio, dan Proyek P5.
- [ ] Perhitungan Nilai Akhir, Predikat, dan Deskripsi Capaian Kompetensi.
- [ ] Cetak / Ekspor Leger Nilai & Raport Siswa.
- [ ] Manajemen Kasus Bimbingan Konseling (BK) dengan status kerahasiaan kasus.

## Tahap 4: Operasional Tata Usaha, PPDB, & Kesiswaan Khusus
- [ ] Administrasi Persuratan TU: Surat Masuk, Surat Keluar berpenomoran otomatis, dan disposisi.
- [ ] PPDB Online: Formulir pendaftaran siswa baru, upload berkas, dan verifikasi panitia.
- [ ] OSIS & E-Voting: Pemilihan ketua OSIS online & pengelolaan kegiatan ekstrakurikuler.
- [ ] Buku Tamu Resepsionis / Pos Keamanan.

## Tahap 5: Manajemen Fasilitas (Sarpras) & Integrasi KataMPI
- [ ] Inventaris Ruangan & Pelaporan Tiket Kerusakan Fasilitas.
- [ ] API Integrasi & SSO dengan engine CBT `ja-katampi`.
- [ ] Notifikasi otomatis WhatsApp Gateway untuk orang tua murid.
