# Roadmap & Tahapan Pengembangan JA-EduSIS

Rencana kerja terstruktur untuk implementasi modul **JA-EduSIS** secara bertahap.

---

## Tahap 1: Pondasi Master Data Akademik & Data Siswa (Selesai)
- [x] Scaffold `Modules/School` di backend dan `src/modules/School` di frontend.
- [x] Master Data Akademik: Tahun Ajaran, Semester, Jurusan, Rombel/Kelas, Mapel.
- [x] Data Induk Siswa (Dapodik-compliant) + Manajemen Orang Tua.
- [x] Integrasi Sistem Kredit Poin Perilaku (Pelanggaran & Prestasi) dari `ja-siska`.
- [x] Seeder awal (Tahun Ajaran aktif, Rombel contoh, dan referensi kategori pelanggaran).

## Tahap 2: Manajemen Pendidik (HR), Jadwal Pelajaran & Sistem Presensi (Selesai)
- [x] Data Induk Guru & Tenaga Kependidikan (`sch_hr_staff`, `StaffController`, view `StaffManagement.vue`).
- [x] Shift Kerja & Presensi Tenaga GTK (`sch_hr_shifts`, `sch_hr_attendances`, check-in log).
- [x] Jadwal Pembelajaran Mingguan (`sch_acad_schedules`, `ScheduleController`).
- [x] Presensi Harian Siswa Rombel (`sch_acad_attendances`, batch update H/I/S/A, view `DailyAttendance.vue`).
- [x] Jurnal Harian KBM Guru (`sch_acad_teaching_journals`, `TeachingJournalController`, view `TeachingJournals.vue`).
- [x] Seeder Fase 2 (`SchoolPhase2Seeder`) & Automated Feature Tests (12 tests, 64 assertions passed).

## Tahap 3: Penilaian, Raport, & Bimbingan Konseling (BK) (Fase Aktif Berikutnya)
- [ ] Struktur Data Nilai: Formatif, Sumatif (UTS/UAS), Tugas/Portofolio, dan Proyek P5.
- [ ] Perhitungan Bobot Nilai Akhir, Predikat, dan Deskripsi Capaian Pembelajaran (CP).
- [ ] Cetak / Ekspor Leger Nilai & Raport Siswa (PDF / Excel).
- [ ] Layanan Kasus Bimbingan Konseling (BK) terintegrasi dengan poin kredit SISKA & tingkat kerahasiaan.

## Tahap 4: Operasional Tata Usaha, PPDB, & Kesiswaan Khusus
- [ ] Administrasi Persuratan TU: Surat Masuk, Surat Keluar berpenomoran otomatis, dan disposisi.
- [ ] PPDB Online: Formulir pendaftaran siswa baru, upload berkas, dan verifikasi panitia.
- [ ] OSIS & E-Voting: Pemilihan ketua OSIS online & pengelolaan kegiatan ekstrakurikuler.
- [ ] Buku Tamu Resepsionis / Pos Keamanan.

## Tahap 5: Manajemen Fasilitas (Sarpras) & Integrasi KataMPI
- [ ] Inventaris Ruangan & Pelaporan Tiket Kerusakan Fasilitas.
- [ ] API Integrasi & SSO dengan engine CBT `ja-katampi`.
- [ ] Notifikasi otomatis WhatsApp Gateway untuk orang tua murid.
