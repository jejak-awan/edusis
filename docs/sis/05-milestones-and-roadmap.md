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

## Tahap 3: Penilaian, Raport, & Bimbingan Konseling (BK) (Selesai)
- [x] Struktur Data Nilai & Asesmen Kurikulum Merdeka (`sch_acad_learning_objectives`, `sch_acad_assessments`, `sch_acad_grades`).
- [x] Proyek Penguatan Profil Pelajar Pancasila (`sch_acad_p5_projects`, `sch_acad_p5_assessments` skala MB/SB/BSH/SAB).
- [x] Kalkulasi Otomatis Nilai Akhir Terbobot, Predikat, & Deskripsi Capaian Kompetensi (`sch_acad_report_cards`).
- [x] Matriks Leger Nilai Rombel & Lembar Raport Kurikulum Merdeka Siswa (`GradeManagement.vue`, `ReportCardGenerator.vue`).
- [x] Layanan Kasus Bimbingan Konseling BK terintegrasi dengan poin disiplin SISKA & proteksi kerahasiaan (`sch_csl_cases`, `CounselingManagement.vue`).
- [x] Seeder Fase 3 (`SchoolPhase3Seeder`) & Automated Feature Tests (18 tests, 124 assertions passed).

## Tahap 4: Operasional Tata Usaha, PPDB, & Kesiswaan Khusus (Fase Aktif Berikutnya)
- [ ] Administrasi Persuratan TU: Surat Masuk, Surat Keluar berpenomoran otomatis, dan disposisi.
- [ ] PPDB Online: Formulir pendaftaran siswa baru, upload berkas, dan verifikasi panitia.
- [ ] OSIS & E-Voting: Pemilihan ketua OSIS online & pengelolaan kegiatan ekstrakurikuler.
- [ ] Buku Tamu Resepsionis / Pos Keamanan.

## Tahap 5: Manajemen Fasilitas (Sarpras) & Integrasi KataMPI
- [ ] Inventaris Ruangan & Pelaporan Tiket Kerusakan Fasilitas.
- [ ] API Integrasi & SSO dengan engine CBT `ja-katampi`.
- [ ] Notifikasi otomatis WhatsApp Gateway untuk orang tua murid.
