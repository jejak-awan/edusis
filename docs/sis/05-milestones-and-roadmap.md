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

## Tahap 4: Operasional Tata Usaha, PPDB, & Kesiswaan Khusus (Selesai)
- [x] Administrasi Persuratan TU: Surat Masuk, Surat Keluar berpenomoran otomatis klasifikasi dinas, dan lembar arahan disposisi (`sch_adm_letters`, `LetterController`, `LetterManagement.vue`).
- [x] PPDB Online: Multi-jalur seleksi (zonasi, afirmasi, prestasi, perpindahan), verifikasi berkas, dan konversi ke data induk siswa aktif Dapodik (`sch_adm_ppdb_registrations`, `PpdbController`, `PpdbManagement.vue`).
- [x] OSIS & E-Voting: Bilik suara digital pemilihan ketua OSIS dengan validasi anti-double voting (1 siswa = 1 suara), quick count realtime visual, serta kegiatan ekstrakurikuler (`sch_std_osis_elections`, `sch_std_osis_candidates`, `sch_std_osis_votes`, `sch_std_extracurriculars`, `OsisVoting.vue`).
- [x] Buku Tamu Resepsionis / Pos Keamanan: Check-in realtime, badge ID card tamu, dan pencatatan check-out 1-klik (`sch_adm_guest_books`, `GuestBookController`, `GuestBook.vue`).
- [x] Seeder Fase 4 (`SchoolPhase4Seeder`) & Automated Feature Tests (28 tests, 251 assertions passed).

## Tahap 5: Manajemen Fasilitas (Sarpras), Integrasi KataMPI & WhatsApp Gateway (Selesai)
- [x] Inventaris Ruangan & Pelaporan Tiket Kerusakan Fasilitas (`sch_fac_rooms`, `sch_fac_assets`, `sch_fac_maintenance_tickets`, `FacilityController`, `FacilityManagement.vue`).
- [x] API Integrasi, Roster Sync & SSO dengan engine CBT `ja-katampi` (`sch_int_katampi_configs`, `KatampiIntegrationController`, `KatampiIntegration.vue`).
- [x] Notifikasi otomatis WhatsApp Gateway untuk presensi & orang tua murid (`sch_comm_wa_logs`, `WhatsAppController`, `WhatsAppGateway.vue`).
- [x] Seeder Fase 5 (`SchoolPhase5Seeder`) & Automated Feature Tests (39 tests, 391 assertions passed).

