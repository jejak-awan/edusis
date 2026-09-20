# School Module (JA-EduSIS Frontend)

Frontend interface untuk Sistem Informasi Sekolah (SIS / EduSIS) berbasis Vue 3 (`<script setup lang="ts">`), Vite, Tailwind CSS, Lucide Icons, dan Pinia.

## Fitur & Tampilan (Views)

### 1. Administrasi Sekolah & Kesiswaan
- **Buku Induk Siswa (`views/admin/students/`)**:
  - `StudentList.vue`: Data tabel siswa, filter rombel, filter status, catat mutasi poin kilat.
  - `components/StudentForm.vue`: Formulir komprehensif 5-tab (Biodata Pribadi, Alamat & Domisili, Orang Tua/Wali, Rombel/Akademik, Kesejahteraan PIP/KIP).
  - `Create.vue` & `Edit.vue`: Halaman pendaftaran dan pembaruan data siswa.
- **Akademik & Penjadwalan (`views/AcademicManagement.vue`)**: Tahun ajaran, semester, rombel, mata pelajaran.
- **Asesmen & Raport Merdeka (`views/GradeManagement.vue` & `views/ReportCardGenerator.vue`)**: Input nilai capaian pembelajaran TP dan cetak raport PDF.
- **Presensi Harian (`views/DailyAttendance.vue`)**: Presensi per kelas dengan status Hadir, Sakit, Izin, Alpa.
- **Kedisiplinan & Prestasi (`views/DisciplineManagement.vue`)**: Monitoring saldo poin dan riwayat SP.
- **Bimbingan Konseling (`views/CounselingManagement.vue`)**: Manajemen penanganan kasus siswa.
- **Persuratan & Buku Tamu (`views/LetterManagement.vue` & `views/GuestBook.vue`)**: Penomoran otomatis dan log pengunjung.
- **PPDB Admin (`views/PpdbManagement.vue`)**: Verifikasi berkas pendaftar dan penetrasi ke rombel.
- **Sarana & Prasarana (`views/FacilityManagement.vue`)**: Inventaris ruang, barang, dan tiket pemeliharaan.
- **Integrasi Katampi & WhatsApp (`views/KatampiIntegration.vue` & `views/WhatsAppGateway.vue`)**.

### 2. Portal Peran (Role Portals)
- **Portal Pendidik (`views/teacher/`)**:
  - `TeacherDashboard.vue`: Ringkasan agenda harian dan persentase kehadiran kelas.
  - `MyTeachingSchedule.vue`: Matriks jadwal mengajar mingguan.
  - `JournalEntryView.vue`: Pengisian jurnal mengajar harian.
- **Portal Siswa Mandiri (`views/student/`)**:
  - `StudentDashboard.vue`: Ringkasan status kesiswaan, saldo poin, dan kehadiran.
  - `MyReportCardView.vue`: Lembar hasil belajar digital Kurikulum Merdeka.
  - `MyAttendanceView.vue`: Log kalender presensi bulanan.

### 3. Layanan Publik & Kiosk
- **Kiosk E-Voting OSIS (`views/public/VotingKioskView.vue`)**: Bilik suara digital layar penuh dengan autentikasi token 8 karakter.
- **Pendaftaran PPDB Publik (`views/public/PublicPpdbView.vue`)**: Portal pendaftaran jalur zonasi, afirmasi, prestasi, dan mutasi ortu.

## Struktur Layanan & State

- **Domain Types (`services/types.ts`)**: Model data TypeScript baku (`Student`, `Department`, `StudyGroup`, dll.).
- **Domain Services (`services/`)**: `studentService.ts`, `academicService.ts`, `attendanceService.ts`, `counselingService.ts`, `operationsService.ts`, `facilityService.ts`, `votingService.ts`.
- **Pinia Stores (`stores/`)**: `studentStore.ts`, `academicStore.ts`, `votingStore.ts`, `schoolStore.ts`.

## Standarisasi Kualitas
- **i18n Symmetry**: Mematuhi kontrak 100% simetris (10.136 kunci) pada `en.json`, `id.json`, `su.json`.
- **Strict TypeScript**: Lolos audit `npx vue-tsc -b` dengan 0 kesalahan tipe.
