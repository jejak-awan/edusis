# Blueprint Arsitektur & Scaffolding Modul School (`Modules/School`)

Dokumen ini adalah cetak biru teknis resmi untuk struktur modul **`School`** pada **JA-EduSIS**, yang menjadi panduan baku bagi pengembang dan agen AI dalam mengimplementasikan kode backend dan antarmuka frontend.

---

## 1. Prinsip Desain

1. **Modular Monolith Terisolasi**:
   - Seluruh logika domain sekolah berada di dalam `backend/Modules/School` (Nwidart module) dan `frontend/src/modules/School` (Vue 3 Pinia pack).
   - Berinteraksi dengan kernel `Modules/Core` hanya melalui kontrak IAM (Users & Roles), Security (ABAC/Audit), Media (Storage upload), dan Mail (Notifikasi).
2. **Sub-Domain Partitioning**:
   - Model, controller, migrasi, dan views dipartisi ke dalam 7 sub-domain independen: `Academic`, `Student`, `HR`, `Operations`, `Admission`, `Logistics`, dan `Osis`.
3. **Standar Penamaan Tabel Basis Data (`sch_*`)**:
   - Seluruh tabel sekolah menggunakan prefix `sch_<subdomain>_*` dan hidup di dalam skema PostgreSQL `edusis` (terisolasi dari skema platform `core_engine`).
4. **Pemisahan Beban CBT & LMS**:
   - Mesin evaluasi ujian online (CBT) dan LMS interaktif **dikecualikan** dari modul ini dan didelegasikan ke engine terpisah `ja-katampi`.
5. **Multi-Portal Berbasis Peran (RBAC)**:
   - Frontend menyediakan dashboard dan antarmuka khusus sesuai peran: **Admin Sekolah**, **Guru**, **Wali Kelas**, **Konselor BK**, **Tata Usaha**, serta **Siswa & Orang Tua**.

---

## 2. Struktur Backend (`backend/Modules/School/`)

```
backend/Modules/School/
├── module.json                      # Registrasi modul: "School", active: true, priority: 10
├── manifest.json                    # Capability registry, Spatie permissions, & navigasi
├── routes/
│   └── api.php                      # Rute API RESTful (/api/v1/school/*)
│
├── app/
│   ├── Providers/
│   │   ├── SchoolServiceProvider.php
│   │   └── RouteServiceProvider.php
│   │
│   ├── Models/                      # Sub-Domain Entities
│   │   ├── Academic/                # 🎓 Master Akademik & KBM
│   │   │   ├── AcademicYear.php     # Tahun Ajaran (Aktif / Non-aktif)
│   │   │   ├── Semester.php         # Semester (Ganjil / Genap)
│   │   │   ├── Department.php       # Jurusan / Konsentrasi Keahlian
│   │   │   ├── StudyGroup.php       # Rombongan Belajar (Rombel / Kelas)
│   │   │   ├── Subject.php          # Mata Pelajaran (Umum / Kejuruan / Mulok)
│   │   │   ├── Schedule.php         # Matriks Jadwal KBM Mingguan
│   │   │   ├── Attendance.php       # Presensi Harian Siswa (H/I/S/A + lampiran)
│   │   │   ├── TeachingJournal.php  # Jurnal Guru (Materi diajarkan, absensi, bukti KBM)
│   │   │   ├── Grade.php            # Nilai (Formatif, Sumatif, Proyek P5, Capaian)
│   │   │   └── ClassHistory.php     # Jejak Historis Kenaikan & Mutasi Rombel
│   │   │
│   │   ├── Student/                 # 👥 Kesiswaan, Disiplin & BK (Sintesis SISKA)
│   │   │   ├── Student.php          # Buku Induk Siswa (Dapodik: NISN, NIK, Ortu)
│   │   │   ├── StudentParent.php    # Data Ayah, Ibu, dan Wali murid
│   │   │   ├── CreditPoint.php      # Akumulasi Skor Poin Disiplin (dari JA-SISKA)
│   │   │   ├── PointCategory.php    # Kategori Poin Positif / Negatif
│   │   │   ├── Violation.php        # Pelanggaran Siswa & Pemicu SP1/SP2/SP3
│   │   │   ├── Achievement.php      # Prestasi & Penghargaan Siswa
│   │   │   ├── CounselingRecord.php # Sesi Konseling BK Privat (is_confidential)
│   │   │   ├── Alumni.php           # Data Kelulusan Siswa
│   │   │   └── TracerStudy.php      # Penelusuran Tamatan (Kuliah, Kerja, Wirausaha)
│   │   │
│   │   ├── HR/                      # 👨‍🏫 Pendidik & Tenaga Kependidikan
│   │   │   ├── Staff.php            # Data Guru & Tendik (NIP, NUPTK, Pangkat)
│   │   │   ├── StaffAttendance.php  # Presensi Harian Jam Kerja Guru/Tendik
│   │   │   ├── StaffShift.php       # Pengaturan Shift / Jam Mengajar
│   │   │   ├── LeaveRequest.php     # Pengajuan Cuti & Approval Pimpinan
│   │   │   ├── SalaryStructure.php  # Komponen Gaji & Tunjangan
│   │   │   └── Payroll.php          # Penerbitan Slip Gaji Pegawai
│   │   │
│   │   ├── Operations/              # 🏛️ Tata Usaha & Pelayanan (Sintesis JA-SIS)
│   │   │   ├── SuratMasuk.php       # Buku Agenda Surat Masuk TU
│   │   │   ├── SuratKeluar.php      # Surat Keluar, Penomoran Otomatis & Approval
│   │   │   ├── TemplateSurat.php    # Template Blanko (Keterangan Aktif, Panggilan)
│   │   │   ├── VisitorLog.php       # Buku Tamu Satpam / Resepsionis Lobi
│   │   │   └── GraduationResult.php # Penetapan SKL & Kelulusan Akhir
│   │   │
│   │   ├── Admission/               # 📝 PPDB / Penerimaan Siswa Baru
│   │   │   ├── Enrollment.php       # Formulir Registrasi Calon Siswa
│   │   │   ├── EnrollmentDoc.php    # Dokumen Unggahan (KK, Ijazah, Akta)
│   │   │   └── DocVerification.php  # Validasi Status Berkas Panitia
│   │   │
│   │   ├── Logistics/               # 🏢 Sarana Prasarana (Sarpras)
│   │   │   ├── Building.php         # Gedung Sekolah
│   │   │   ├── Room.php             # Ruangan Kelas / Lab / Bengkel
│   │   │   ├── SchoolAsset.php      # Aset Sekolah
│   │   │   ├── InventoryItem.php    # Barang Inventaris per Ruangan
│   │   │   └── MaintenanceTicket.php# Pelaporan Kerusakan & Perbaikan Sarpras
│   │   │
│   │   └── Osis/                    # 🗳️ Organisasi Siswa & Ekskul (Sintesis JA-SIS)
│   │       ├── OsisMember.php       # Pengurus & Anggota OSIS
│   │       ├── OsisProgram.php      # Program Kerja & Anggaran OSIS
│   │       ├── OsisEvotingCandidate # Kandidat Ketua OSIS
│   │       ├── OsisEvotingBallot    # Surat Suara E-Voting Online Siswa
│   │       ├── Extracurricular.php  # Ekstrakurikuler (Pramuka, PMR, Olahraga)
│   │       └── ExtraMember.php      # Pendaftaran Anggota Ekskul
│   │
│   ├── Http/Controllers/Api/        # RESTful API Controllers
│   │   ├── Academic/
│   │   ├── Student/
│   │   ├── HR/
│   │   ├── Operations/
│   │   ├── Admission/
│   │   ├── Logistics/
│   │   └── Osis/
│   │
│   └── Database/
│       ├── Migrations/              # Migrasi tabel sch_*
│       └── Seeders/                 # Seeder data referensi
```

---

## 3. Struktur Frontend (`frontend/src/modules/School/`)

```
frontend/src/modules/School/
├── index.ts                         # Entry point pack & export SchoolModule
├── module.ts                        # Definisi AppModule, metadata, izin akses
├── navigation.ts                    # Registrasi menu navigasi sidebar admin
├── router/
│   └── index.ts                     # Definisi rute: /manage/school/*
│
├── stores/                          # State management Pinia
│   ├── academicStore.ts             # Tahun ajaran, rombel, mapel, jadwal
│   ├── studentStore.ts              # Data siswa, filter rombel, mutasi
│   ├── disciplineStore.ts           # Poin kredit, rekap pelanggaran, SP
│   ├── counselingStore.ts           # Sesi BK & catatan privat
│   ├── attendanceStore.ts           # Presensi harian & QR scanner
│   ├── gradeStore.ts                # Ledger nilai & raport
│   ├── staffStore.ts                # Data guru/tendik & kepegawaian
│   └── operationsStore.ts           # Persuratan & PPDB
│
├── services/                        # HTTP client connector
│   ├── academicService.ts
│   ├── studentService.ts
│   ├── disciplineService.ts
│   └── ...
│
├── locales/                         # Multibahasa paritas id/en/su
│   ├── id.json
│   ├── en.json
│   └── su.json
│
└── views/                           # Antarmuka Pengguna
    ├── admin/                       # 🎛️ PORTAL ADMINISTRATOR SEKOLAH
    │   ├── dashboard/               # SchoolAdminDashboard.vue
    │   ├── academic/                # Tahun Ajaran, Rombel, Mapel, Jadwal, Jurnal KBM
    │   ├── students/                # Direktori Siswa (Dapodik), Mutasi, Detail Profil
    │   ├── discipline/              # Rekap Poin Kredit, Pelanggaran, Prestasi, Cetak SP
    │   ├── counseling/              # Manajemen Sesi Bimbingan Konseling (BK)
    │   ├── attendance/              # Rekap Kehadiran, Scanner QR Code Presensi
    │   ├── grades/                  # Rekap Nilai, Deskripsi Capaian, Cetak Raport
    │   ├── hr/                      # Direktori Guru/Tendik, Cuti, Penggajian
    │   ├── operations/              # Persuratan TU (Surat Masuk/Keluar), Buku Tamu
    │   ├── admission/               # Verifikasi Berkas Pendaftaran PPDB
    │   ├── logistics/               # Inventaris Ruangan & Tiket Kerusakan
    │   └── osis/                    # Program Kerja OSIS & Pemilu E-Voting
    │
    ├── teacher/                     # 👨‍🏫 PORTAL GURU
    │   ├── TeacherDashboard.vue     # Jadwal mengajar hari ini
    │   ├── GradeEntry.vue           # Form input nilai per rombel & mapel
    │   ├── TeachingJournal.vue      # Pengisian jurnal KBM harian
    │   └── ClassAttendance.vue      # Presensi siswa di kelas yang diajar
    │
    ├── student/                     # 🎓 PORTAL SISWA & ORANG TUA
    │   ├── StudentDashboard.vue     # Kartu pelajar digital, rekap presensi
    │   ├── MyGrades.vue             # Transkrip nilai harian & raport
    │   ├── MyDiscipline.vue         # Catatan poin prestasi & pelanggaran
    │   └── MySchedule.vue           # Jadwal pelajaran kelas
    │
    └── public/                      # 🌐 PORTAL PUBLIK
        ├── PpdbRegistration.vue     # Formulir pendaftaran PPDB online
        └── PpdbStatus.vue           # Pelacakan status seleksi & berkas
```

---

## 4. Matriks Tabel Basis Data (`sch_*`)

| Tabel | Sub-Domain | Fungsi & Relasi Utama |
|---|---|---|
| `sch_acad_academic_years` | Academic | Tahun pelajaran (misal: 2026/2027) dengan status `is_active`. |
| `sch_acad_semesters` | Academic | Semester 1 (Ganjil) atau 2 (Genap). |
| `sch_acad_departments` | Academic | Program keahlian / jurusan (RPL, TKJ, Akuntansi, dll.). |
| `sch_acad_study_groups` | Academic | Rombongan belajar (X RPL 1, XI TKJ 2). Relasi ke wali kelas & jurusan. |
| `sch_acad_subjects` | Academic | Mata pelajaran dan bobot SKS / jam pelajaran. |
| `sch_acad_schedules` | Academic | Alokasi hari, jam mulai-selesai, rombel, mapel, dan guru pengampu. |
| `sch_acad_attendances` | Academic | Presensi per siswa harian (H/I/S/A) + URL lampiran bukti surat. |
| `sch_acad_teaching_journals` | Academic | Catatan materi KBM harian guru, bukti foto, dan siswa absen di jam tsb. |
| `sch_acad_grades` | Academic | Nilai harian, UTS, UAS, tugas, proyek P5, nilai akhir, & deskripsi capaian. |
| `sch_acad_class_histories` | Academic | Riwayat kenaikan kelas siswa dari tahun ke tahun. |
| `sch_std_students` | Student | Profil lengkap siswa standar Dapodik, NISN, NIK, alamat, kontak. |
| `sch_std_parents` | Student | Data detail ayah, ibu, dan wali murid. |
| `sch_std_point_categories` | Student | Referensi kategori poin pelanggaran (ringan/sedang/berat) & prestasi. |
| `sch_std_credit_points` | Student | Ledger poin perilaku siswa (akumulasi minus/plus). |
| `sch_std_violations` | Student | Catatan kasus pelanggaran spesifik & surat panggilan orang tua / SP. |
| `sch_std_achievements` | Student | Catatan piagam penghargaan / prestasi lomba siswa. |
| `sch_std_counseling_records` | Student | Catatan sesi konseling BK (dengan flag `is_confidential`). |
| `sch_std_alumni` | Student | Data kelulusan tahun akhir. |
| `sch_std_tracer_studies` | Student | Kuesioner penelusuran tamatan (bekerja, lanjut studi, wirausaha). |
| `sch_hr_staff` | HR | Data induk guru & tenaga kependidikan (NIP, NUPTK, jabatan, relasi user). |
| `sch_hr_staff_attendances` | HR | Log presensi harian staff (jam masuk & jam pulang). |
| `sch_hr_leave_requests` | HR | Pengajuan cuti & persetujuan kepala sekolah. |
| `sch_hr_salary_structures` | HR | Master komponen gaji (pokok, transport, fungsional). |
| `sch_hr_payrolls` | HR | Rekap pembayaran gaji bulanan staff. |
| `sch_ops_surat_masuk` | Operations | Register buku agenda surat masuk & file scan PDF. |
| `sch_ops_surat_keluar` | Operations | Register surat keluar, penomoran otomatis, & approval pimpinan. |
| `sch_ops_template_surat` | Operations | Format blanko surat keterangan aktif, surat izin, dll. |
| `sch_ops_visitors` | Operations | Pencatatan tamu sekolah di pos satpam / resepsionis. |
| `sch_adm_enrollments` | Admission | Data calon siswa pendaftar PPDB. |
| `sch_adm_enrollment_documents`| Admission | Berkas persyaratan pendaftaran (KK, Akta, Ijazah). |
| `sch_fac_rooms` | Logistics | Ruangan kelas, lab, bengkel, kantor. |
| `sch_fac_inventory_items` | Logistics | Barang aset per ruangan (meja, proyektor, PC) & kondisinya. |
| `sch_fac_maintenance_tickets` | Logistics | Laporan kerusakan fasilitas & progres perbaikan. |
| `sch_osis_programs` | Osis | Agenda kerja dan anggaran kegiatan OSIS. |
| `sch_osis_evoting_ballots` | Osis | Suara pemilih pada pemilu ketua OSIS digital. |
| `sch_osis_extracurriculars` | Osis | Daftar klub ekskul sekolah & pembinanya. |

---

## 5. Matriks Hak Akses & Peran (RBAC)

Modul `School` mendaftarkan permissions ke sistem keamanan Core Engine Spatie:

```json
[
  "view academic", "manage academic",
  "view students", "create students", "edit students", "delete students", "manage students",
  "view discipline", "manage discipline",
  "view counseling", "manage counseling", "manage confidential counseling",
  "view staff", "manage staff",
  "view attendance", "manage attendance", "manage staff attendance",
  "view grades", "manage grades", "approve report cards",
  "view operations", "manage operations", "manage letters",
  "view admission", "manage admission",
  "view logistics", "manage logistics",
  "view osis", "manage osis"
]
```
