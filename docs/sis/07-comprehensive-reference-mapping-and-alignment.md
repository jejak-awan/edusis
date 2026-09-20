# Pemetaan Komprehensif Aset `repo_referensi`: Sistem, Relasi, Boundary, dan Lokalisasi

Dokumen ini adalah cetak biru teknis resmi yang melacak seluruh detail arsitektural dari 4 repositori referensi di [`/home/jejakawan/dev/repo_referensi/`](file:///home/jejakawan/dev/repo_referensi/) untuk memastikan proses **alignment (penyelarasan)** ke dalam **JA-EduSIS** (`ja-edusis`) berjalan 100% presisi, tanpa duplikasi, tanpa halusinasi, dan tanpa ada komponen yang terlewat.

---

## 1. Matriks 4 Repositori Referensi

| Repositori | Basis Teknologi | Domain Keunggulan Utama | Lokasi di Dev Host |
|---|---|---|---|
| **`cijulang`** | Laravel 11 / Vue 3 Pinia (Nwidart Modular) | Skeleton Modul Sekolah, Kepatuhan Dapodik, Akademik, Jadwal, HR, Sarpras | `/home/jejakawan/dev/repo_referensi/cijulang` |
| **`ja-siska`** | Laravel 11 / Livewire & Blade | Sistem Kredit Poin Disiplin Siswa (SP1-SP3), Bimbingan Konseling (BK), Presensi QR | `/home/jejakawan/dev/repo_referensi/ja-siska` |
| **`ja-sis`** | Laravel 12 / Inertia Vue | Persuratan Tata Usaha & Disposisi, Raport Kurikulum Merdeka, Ekstrakurikuler | `/home/jejakawan/dev/repo_referensi/ja-sis` |
| **`ja-evoting`** | Laravel 12 / Inertia React | Engine Pemilihan Digital, Token Kiosk Bilik Suara 8-Karakter, Secret Ballot Box | `/home/jejakawan/dev/repo_referensi/ja-evoting` |

---

## 2. Detail Sistem, Relasi Entitas & Skema Basis Data

### A. Sub-Domain Akademik & Kurikulum (Sumber: `cijulang` + `ja-sis`)
- **Entitas Utama**:
  - `sch_acad_academic_years`: Tahun ajaran (contoh `2026/2027`), status `is_active`.
  - `sch_acad_semesters`: Semester ganjil/genap berelasi ke tahun ajaran.
  - `sch_acad_departments`: Program keahlian/jurusan vokasi (PPLG, TJKT, DKV, dll.).
  - `sch_acad_study_groups`: Rombongan belajar (Rombel/Kelas) berelasi ke jurusan, tingkat, dan wali kelas (`staff_id`).
  - `sch_acad_subjects`: Mata pelajaran dengan kode mapel, kelompok (A, B, C Kejuruan), dan KKM/KKTP.
  - `sch_acad_schedules`: Matriks jadwal KBM mingguan (alokasi hari, jam mulai-selesai, rombel, mapel, guru, ruangan).
  - `sch_acad_teaching_journals`: Jurnal KBM harian guru (materi diajarkan, catatan refleksi, bukti foto, absensi saat jam mengajar).
  - `sch_acad_assessments` & `sch_acad_grades`: Asesmen Kurikulum Merdeka (formatif, sumatif, pembobotan, dan capaian pembelajaran TP).
  - `sch_acad_report_cards`: Lembar raport siswa, kalkulasi nilai akhir per mapel, predikat (A/B/C/D), dan deskripsi capaian otomatis.
- **Relasi Inti**:
  - `StudyGroup` ➔ `1:N` ➔ `Student` (via pivot `sch_acad_class_histories` untuk mencatat riwayat kenaikan/mutasi kelas).
  - `Schedule` ➔ `1:N` ➔ `TeachingJournal` (guru mengisi jurnal per jadwal KBM).
  - `Assessment` ➔ `1:N` ➔ `Grade` ➔ `N:1` ➔ `Student`.

### B. Sub-Domain Kesiswaan, Disiplin & BK (Sumber: `cijulang` + `ja-siska`)
- **Entitas Utama**:
  - `sch_std_students`: Buku induk siswa sesuai format Dapodik Kemendikbudristek (NISN, NIS, NIK, nama lengkap, TTL, agama, alamat detail RT/RW/Kelurahan/Kecamatan/Kode Pos, transportasi, penerima KIP/PIP).
  - `sch_std_parents`: Data orang tua/wali (nama ayah, nama ibu, nama wali, NIK, pekerjaan, penghasilan, pendidikan).
  - `sch_std_point_categories`: Master kategori poin pelanggaran (ringan, sedang, berat) dan prestasi.
  - `sch_std_credit_points`: Buku catatan kredit poin perilaku siswa (poin negatif/positif, pelapor, status verifikasi/approval).
  - `sch_std_violations`: Rekap kasus pelanggaran siswa dengan status eskalasi Surat Peringatan (`sp_level`: SP1, SP2, SP3) dan panggilan orang tua.
  - `sch_std_achievements`: Rekap prestasi akademik/non-akademik, tingkat kejuaraan (sekolah, kota, provinsi, nasional, internasional).
  - `sch_std_counseling_records`: Sesi Bimbingan Konseling (BK) dengan atribut kerahasiaan `is_confidential`, klasifikasi masalah, jenis intervensi, dan tindak lanjut home visit.
- **Relasi Inti**:
  - `Student` ➔ `1:1` ➔ `Parent`
  - `Student` ➔ `1:N` ➔ `CreditPoint`
  - `Student` ➔ `1:N` ➔ `CounselingRecord` (hanya dapat diakses oleh role Guru BK & Kepala Sekolah).

### C. Sub-Domain Kepegawaian / HR (Sumber: `cijulang`)
- **Entitas Utama**:
  - `sch_hr_staff`: Data induk guru dan tenaga kependidikan (NIP, NUPTK, NIK, jabatan, status kepegawaian PNS/PPPK/GTY/PTY/Honorer, status sertifikasi pendidik, relasi user login).
  - `sch_hr_shifts`: Pengaturan shift kerja dan jam toleransi check-in/out.
  - `sch_hr_attendances`: Log presensi harian pegawai (waktu scan, status hadir/terlambat/izin/sakit, koordinat/IP, foto selfie).
  - `sch_hr_leaves`: Pengajuan cuti/izin pegawai dan alur persetujuan pimpinan TU/kepala sekolah.

### D. Sub-Domain Tata Usaha & Logistik (Sumber: `cijulang` + `ja-sis`)
- **Entitas Utama**:
  - `sch_adm_letters`: Agenda persuratan TU (surat masuk dan surat keluar berpenomoran otomatis klasifikasi dinas, kode berkas, perihal, file pindaian PDF).
  - `sch_adm_dispositions`: Lembar arahan disposisi surat dari kepala sekolah/kepala TU kepada staf penerima tugas.
  - `sch_adm_guest_books`: Buku tamu resepsionis/satpam (nama tamu, instansi, nomor tanda pengenal/KTP, ID badge tamu, tujuan kunjungan, jam masuk & keluar).
  - `sch_adm_ppdb_registrations`: Pendaftaran calon siswa baru online (jalur zonasi, prestasi, afirmasi), verifikasi kelayakan dokumen unggahan, dan konversi menjadi siswa aktif Dapodik.
  - `sch_fac_rooms` & `sch_fac_assets`: Inventaris gedung/ruangan dan aset sarpras sekolah (kondisi baik/rusak ringan/rusak berat, sumber dana BOS/DAK).
  - `sch_fac_maintenance_tickets`: Tiket perbaikan kerusakan fasilitas, tingkat urgensi, teknisi penanggung jawab, dan biaya perbaikan.

### E. Sub-Domain Organisasi & E-Voting Engine (Sumber: `ja-evoting` + `cijulang` + `ja-sis`)
- **Entitas Utama**:
  - `sch_vote_events`: Agenda pemilihan/polling generik (OSIS, MPK, Guru Favorit, Ekskul, Komite).
  - `sch_vote_candidates`: Daftar kandidat/paslon dengan nomor urut, visi, misi, dan referensi siswa/guru.
  - `sch_vote_voters`: Daftar Pemilih Tetap (DPT) dengan token unik 8-karakter acak sekali pakai.
  - `sch_vote_ballots`: Kotak suara terpisah (*Secret Ballot Box*) tanpa relasi voter ID (*Luber Jurdil*), menyimpan token hash, IP, dan timestamp.
  - `sch_std_extracurriculars`: Ekstrakurikuler sekolah, pembina guru, jadwal kegiatan, dan lokasi.

---

## 3. Batasan Sistem (*System Boundaries*)

Untuk mencegah kebocoran lingkup (*scope creep*) dan redundansi kode, batas sistem ditetapkan secara ketat:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                     JA-EDUSIS                                          │
│                                (Modules/School)                                        │
│                                                                                        │
│  [Akademik & Rapor]       [Kesiswaan & Disiplin]     [Pendidik & Tenaga GTK]          │
│  - Kurikulum Merdeka      - Profil Siswa Dapodik     - Data Induk Guru & Tendik       │
│  - Jadwal & Jurnal KBM    - Poin Kredit SISKA        - Presensi Harian GTK            │
│  - Raport & Leger         - Bimbingan Konseling      - Pengajuan Cuti & Izin          │
│                                                                                        │
│  [Operasional TU & PPDB]  [Sarpras & Fasilitas]      [E-Voting & Ekskul Engine]       │
│  - Surat Masuk/Keluar     - Ruangan & Aset Sekolah   - Multi-Agenda Voting & Polling   │
│  - Disposisi Pimpinan     - Tiket Kerusakan Sarpras  - Bilik Suara Kiosk Token         │
│  - PPDB & Buku Tamu       - Logistik Pemeliharaan    - Secret Ballot & Quick Count     │
└───────────────────────────────────────────┬────────────────────────────────────────────┘
                                            │
               ┌────────────────────────────┼────────────────────────────┐
               ▼                            ▼                            ▼
  ┌─────────────────────────┐  ┌─────────────────────────┐  ┌─────────────────────────┐
  │  JA-KATAMPI CBT ENGINE  │  │   CORE ENGINE KERNEL    │  │   WHATSAPP GATEWAY API  │
  │    (Cluster Aksara)     │  │   (IAM, Sec, Storage)   │  │   (Baileys / Wablas)    │
  │                         │  │                         │  │                         │
  │ - Mesin Ujian CBT       │  │ - Autentikasi Sanctum   │  │ - Pengiriman pesan WA   │
  │ - Bank Soal & Butir     │  │ - RBAC / Spatie Perms   │  │   otomatis ke HP ortu   │
  │ - Timer Sesi Ujian      │  │ - Audit Log / SIEM      │  │ - Notifikasi ketidakhadiran│
  │ - Koreksi Otomatis      │  │ - Media Upload Storage  │  │   harian siswa           │
  │                         │  │                         │  │                         │
  │ Boundary:               │  │ Boundary:               │  │ Boundary:               │
  │ EduSIS HANYA integrasi  │  │ EduSIS HANYA memakai    │  │ EduSIS HANYA mencatat   │
  │ via SSO Token Signer,   │  │ guard & middleware      │  │ log antrean di          │
  │ Roster Sync, dan Grade  │  │ yang disediakan         │  │ `sch_comm_wa_logs`      │
  │ Ingestion API.          │  │ platform Core Engine.   │  │                         │
  └─────────────────────────┘  └─────────────────────────┘  └─────────────────────────┘
```

---

## 4. Standar Lokalisasi (i18n) & Terminologi Baku

Sistem informasi sekolah Indonesia memiliki istilah baku Kemendikbudristek yang tidak boleh diterjemahkan sembarangan:

| Konsep Domain | Bahasa Indonesia (Baku) | English | Basa Sunda |
|---|---|---|---|
| Identitas Siswa | Nomor Induk Siswa Nasional (NISN) | National Student ID (NISN) | Nomer Induk Murid Nasional (NISN) |
| Nomor Induk Kependudukan | NIK (KTP / KK) | National Identity Number (NIK) | Nomer Induk Kependudukan (NIK) |
| Guru | Pendidik & Tenaga Kependidikan (GTK) | Teachers & School Staff (GTK) | Guru & Tenaga Kependidikan (GTK) |
| Kelas Belajar | Rombongan Belajar (Rombel) | Study Group / Class | Rombongan Diajar (Rombel) |
| Jadwal Mengajar | Kegiatan Belajar Mengajar (KBM) | Teaching & Learning Activities | Kagiatan Diajar Ngajar (KBM) |
| Penilaian Baru | Capaian Pembelajaran (CP / TP) | Learning Objectives (CP / TP) | Capaian Pangajaran (CP / TP) |
| Proyek Karakter | Profil Pelajar Pancasila (P5) | Pancasila Student Profile (P5) | Profil Pelajar Pancasila (P5) |
| Disiplin Siswa | Surat Peringatan (SP1, SP2, SP3) | Warning Letter (SP1, SP2, SP3) | Surat Peringetan (SP1, SP2, SP3) |
| Konseling | Bimbingan Konseling (BK) | Guidance & Counseling (BK) | Bimbingan Konseling (BK) |
| Persuratan | Lembar Disposisi Surat | Letter Disposition Sheet | Lambaran Disposisi Surat |
| Pendaftaran Baru | Penerimaan Peserta Didik Baru (PPDB) | New Student Admission (PPDB) | Panarimaan Murid Anyar (PPDB) |
| Pemilihan | Daftar Pemilih Tetap (DPT) | Registered Voters List (DPT) | Daptar Pamilih Tetep (DPT) |
| Bilik Suara | Bilik Suara Kiosk (Token Sekali Pakai) | Kiosk Voting Booth (Single-Use Token) | Bilik Sora Kiosk (Token Sakali Paké) |
| Asas Pemilu | Langsung, Umum, Bebas, Rahasia (Luber Jurdil) | Secret Ballot / Direct & Fair Election | Langsung, Umum, Bebas, Rusiah (Luber Jurdil) |

Peta kunci translasi di `frontend/src/modules/School/locales/` (`id.json`, `en.json`, `su.json`) wajib mempertahankan **100% simetri struktur JSON** (saat ini 10.136 kunci).

---

## 5. Peta Konkret Penyelarasan Berkas (*Adopt & Align File Mapping*)

Berikut adalah pemetaan berkas sumber dari `repo_referensi` ke target berkas di `ja-edusis`:

### A. Backend FormRequest (`backend/Modules/School/app/Http/Requests/`)
| Berkas Sumber di `repo_referensi` | Berkas Target di `ja-edusis` | Adaptasi yang Diperlukan |
|---|---|---|
| `cijulang/.../Requests/Student/StoreStudentRequest.php` | `app/Http/Requests/Student/StoreStudentRequest.php` | Namespace `Modules\School\Http\Requests\Student`, table `sch_std_students` |
| `cijulang/.../Requests/Student/UpdateStudentRequest.php` | `app/Http/Requests/Student/UpdateStudentRequest.php` | Namespace & tabel `sch_std_students` |
| `cijulang/.../Requests/Student/StoreViolationRequest.php` | `app/Http/Requests/Student/StoreViolationRequest.php` | Tabel `sch_std_violations` |
| `cijulang/.../Requests/Student/StoreCounselingRecordRequest.php` | `app/Http/Requests/Student/StoreCounselingRecordRequest.php` | Tabel `sch_csl_cases` |
| `cijulang/.../Requests/Academic/StoreScheduleRequest.php` | `app/Http/Requests/Academic/StoreScheduleRequest.php` | Tabel `sch_acad_schedules` |
| `cijulang/.../Requests/Academic/StoreAttendanceRequest.php` | `app/Http/Requests/Academic/StoreAttendanceRequest.php` | Tabel `sch_acad_attendances` |
| `cijulang/.../Requests/Academic/StoreJournalRequest.php` | `app/Http/Requests/Academic/StoreJournalRequest.php` | Tabel `sch_acad_teaching_journals` |
| `cijulang/.../Requests/Operations/CheckInVisitorRequest.php` | `app/Http/Requests/Operations/CheckInVisitorRequest.php` | Tabel `sch_adm_guest_books` |
| `ja-evoting/.../Requests/StoreEventRequest.php` | `app/Http/Requests/Voting/StoreVotingEventRequest.php` | Tabel `sch_vote_events` |
| `ja-evoting/.../Requests/StoreCandidateRequest.php` | `app/Http/Requests/Voting/AddVotingCandidateRequest.php` | Tabel `sch_vote_candidates` |
| `ja-evoting/.../Requests/StoreVoteRequest.php` | `app/Http/Requests/Voting/CastVoteRequest.php` | Tabel `sch_vote_ballots`, validasi token |

### B. Backend Services Layer (`backend/Modules/School/app/Services/`)
| Berkas Sumber di `repo_referensi` | Berkas Target di `ja-edusis` | Logika Bisnis yang Diadopsi |
|---|---|---|
| `cijulang/.../Services/Academic/AcademicService.php` | `app/Services/Academic/AcademicService.php` | Master data tahun ajaran, semester, aktivasi rombel, jadwal KBM |
| `ja-sis/.../Services/Raport/RaportService.php` | `app/Services/Academic/AssessmentCalculationService.php` | Perhitungan agregat nilai raport Kurikulum Merdeka, predikat, leger nilai kelas |
| `cijulang/.../Services/Student/StudentService.php` | `app/Services/Student/StudentService.php` | Direktori siswa, validasi NISN/NIK Dapodik, mutasi rombel |
| `ja-siska/.../Services/KreditPoinService.php` | `app/Services/Student/DisciplineService.php` | Akumulasi skor kredit poin perilaku, threshold pemicu SP1/SP2/SP3 |
| `ja-siska/.../Services/BKService.php` | `app/Services/Student/CounselingService.php` | Manajemen kasus konseling siswa terlindungi (`is_confidential`) |
| `cijulang` + `ja-siska/.../PresensiService.php` | `app/Services/Attendance/AttendanceService.php` | Batch insert presensi siswa rombel, check-in staf GTK, deteksi absensi |
| `ja-sis/.../Services/Surat/TemplateSuratService.php` | `app/Services/Operations/LetterService.php` | Format penomoran otomatis surat dinas & alur disposisi |
| `cijulang/.../Services/Logistics/LogisticsService.php` | `app/Services/Facilities/FacilityService.php` | Pendataan aset ruangan & alur penyelesaian tiket kerusakan sarpras |
| `ja-evoting/.../Controllers/VotingEngineController` | `app/Services/StudentAffairs/VotingEngineService.php` | Generator token DPT 8-karakter unik, verifikasi bilik suara, secret ballot decoupling |
| `ja-siska/.../Services/WhatsAppService.php` | `app/Services/Integration/WhatsAppGatewayService.php` | Normalisasi nomor HP, template alert presensi orang tua |

### C. Frontend Domain Services & Stores (`frontend/src/modules/School/`)
| Berkas Sumber di `repo_referensi` | Berkas Target di `ja-edusis` | Peran |
|---|---|---|
| `cijulang/.../services/AcademicService.ts` | `services/academicService.ts` & `types/academic.ts` | Klien API akademik & tipe data terisolasi |
| `cijulang/.../services/StudentService.ts` | `services/studentService.ts` & `types/student.ts` | Klien API siswa, profil Dapodik |
| `cijulang/.../services/HRService.ts` | `services/hrService.ts` & `types/hr.ts` | Klien API GTK & presensi pegawai |
| `cijulang/.../services/LogisticsService.ts` | `services/facilityService.ts` & `types/facility.ts` | Klien API sarpras ruangan & aset |
| `cijulang/.../stores/academic.ts` | `stores/useAcademicStore.ts` | Pinia store tahun ajaran, semester, rombel |
| `cijulang/.../stores/student.ts` | `stores/useStudentStore.ts` | Pinia store siswa & filter kelas |
| `cijulang/.../stores/hr.ts` | `stores/useHrStore.ts` | Pinia store guru & jadwal mengajar |

### D. Frontend Views & Sub-Components (Mengeliminasi Folder Kosong)
| Berkas Sumber di `repo_referensi` | Berkas Target di `ja-edusis` | Solusi Folder Kosong |
|---|---|---|
| `cijulang/.../views/admin/students/components/StudentForm.vue` | `views/admin/students/components/StudentForm.vue` | Form Dapodik lengkap 14 KB |
| `cijulang/.../views/admin/students/Index.vue` | `views/admin/students/Index.vue` | Mengisi folder `views/admin/students/` |
| `cijulang/.../views/admin/students/Create.vue` & `Edit.vue` | `views/admin/students/Create.vue` & `Edit.vue` | Halaman pendaftaran & edit siswa |
| `cijulang/.../views/admin/academic/Index.vue` & `Timetable.vue` | `views/admin/academic/Index.vue` & `Timetable.vue` | Mengisi folder `views/admin/academic/` |
| `cijulang/.../views/teacher/Dashboard.vue` & `GradesInput.vue` | `views/teacher/Dashboard.vue` & `GradesInput.vue` | Mengisi folder `views/teacher/` |
| `cijulang/.../views/student/Dashboard.vue` & `Grades.vue` | `views/student/Dashboard.vue` & `Grades.vue` | Mengisi folder `views/student/` |
### E. Database Factories & Model Policies
| Berkas Sumber di `repo_referensi` | Berkas Target di `ja-edusis` | Peran |
|---|---|---|
| `cijulang/.../factories/StudentFactory.php` | `database/factories/StudentFactory.php` | Mocking data Dapodik untuk unit/feature test |
| `cijulang/.../factories/StaffFactory.php` | `database/factories/StaffFactory.php` | Mocking data GTK & guru |
| `cijulang/.../factories/AcademicYearFactory.php` | `database/factories/AcademicYearFactory.php` | Mocking tahun ajaran |
| `cijulang/.../Policies/StudentPolicy.php` | `app/Policies/StudentPolicy.php` | Otorisasi modifikasi profil & mutasi siswa |
| `cijulang/.../Policies/AcademicPolicy.php` | `app/Policies/AcademicPolicy.php` | Otorisasi penguncian nilai raport & KBM |
| `cijulang/.../Policies/StaffPolicy.php` | `app/Policies/StaffPolicy.php` | Otorisasi presensi & tugas guru |
| `cijulang/.../Policies/SarprasResourcePolicy.php` | `app/Policies/SarprasResourcePolicy.php` | Otorisasi peminjaman & mutasi aset |

---

## 7. Tata Kelola Multi-Tenancy (`SchoolContextMiddleware` & `ScopedBySchool`)

### A. Pola Isolasi Data Multi-Tenant
Setiap sekolah di JA-EduSIS beroperasi di atas skema basis data PostgreSQL bersama (`edusis`) dengan pemisahan logis berbasis kolom `school_id`. Untuk mencegah *data leakage*, diimplementasikan dua pilar:
1. **`SchoolContextMiddleware` (`app/Http/Middleware/SchoolContextMiddleware.php`)**:
   - Mengekstrak identitas sekolah dari header HTTP `X-School-ID` atau dari relasi user terautentikasi (`$request->user()?->school_id`).
   - Menyimpan `school_id` ke dalam memori eksekusi request via Laravel 11 Context: `Context::add('school_id', (int) $schoolId)`.
2. **`ScopedBySchool` Trait (`app/Traits/ScopedBySchool.php`)**:
   - Diadaptasi dari `cijulang/backend/Modules/School/app/Traits/ScopedBySchool.php`.
   - Menginjeksikan Global Scope ke setiap kueri Eloquent: `builder->where($table . '.school_id', Context::get('school_id'))`.
   - Menginjeksi otomatis `school_id` saat `Model::creating()`, sehingga developer tidak perlu mengeset `school_id` secara manual saat `Model::create()`.

---

## 8. Model Policies & Matriks Otorisasi RBAC Spatie

### A. Matriks Hak Akses Peran Baku (Seeded by `SchoolRoleSeeder.php`)

| Hak Akses / Permission | Kepala Sekolah | Admin Kurikulum | Admin Kesiswaan | Admin Sarpras | Admin BK | Guru / Wali Kelas | Siswa | Orang Tua |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `view academic` / `manage academic` | ✅ | ✅ | ❌ | ❌ | ❌ | View | ❌ | ❌ |
| `view students` / `manage students` | ✅ | View | ✅ | ❌ | View | View | ❌ | View |
| `view discipline` / `manage discipline` | ✅ | ❌ | ✅ | ❌ | ✅ | View | ❌ | ❌ |
| `view counseling` / `manage counseling` | ✅ | ❌ | ❌ | ❌ | ✅ (Privat) | ❌ | ❌ | ❌ |
| `view attendance` / `manage attendance` | ✅ | ❌ | ✅ | ❌ | View | ✅ (Kelas) | View (Sendiri) | View (Anak) |
| `view grades` / `manage grades` | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ (Mapel) | View (Sendiri) | View (Anak) |
| `view letters` / `manage letters` | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `view sarpras` / `manage sarpras` | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `view voting` / `manage voting` | ✅ | ❌ | ✅ | ❌ | ❌ | View | ✅ (Bilik) | ❌ |

> [!CAUTION]
> **Kerahasiaan Catatan Konseling BK (`is_confidential`)**:
> Kasus konseling berkategori privat HANYA diizinkan diakses oleh pembuat catatan (Guru BK) dan Kepala Sekolah via `CounselingPolicy@view`. Guru umum, wali kelas, siswa, dan orang tua DILARANG KERAS melihat detail konseling privat demi mematuhi etika profesi konselor dan UU PDP.

---

## 9. Strategi Storage & Isolasi Media (Public vs Private Disk)

Mengadopsi pola dari `cijulang/backend/docs/storage_strategy.md`:

```
storage/
├── app/
│   ├── public/                 <-- DISK PUBLIK (Diakses via symlink /storage/...)
│   │   └── school_{id}/
│   │       ├── students/avatars/
│   │       ├── staff/avatars/
│   │       ├── voting/candidates/
│   │       └── facilities/photos/
│   │
│   └── private/                <-- DISK PRIVAT (Hanya via route proxy terlindung)
│       └── school_{id}/
│           ├── counseling/evidence/     (Surat panggilan, bukti kasus)
│           ├── letters/scans/           (Pindaian disposisi & surat dinas)
│           ├── ppdb/documents/          (Akta kelahiran, KK, KIP/PIP)
│           └── journals/evidence/       (Bukti KBM guru)
```

**Rute Akses File Terlindung**:
`GET /api/v1/school/files/private/{path}` wajib melewati `auth:sanctum` dan policy cek kepemilikan sekolah serta hak akses berkas sebelum berkas dialirkan via `Storage::disk('local')->response($path)`.

---

## 10. Engine Impor Data Siswa Dapodik (`ImportDataService`)

Diadaptasi dari `ja-siska/backend/app/Services/ImportDataService.php`:
1. **Fitur Unggulan**:
   - Download template Excel standar (`.xlsx`) untuk data Siswa, Guru/GTK, dan Rombel.
   - Validasi ketat format Dapodik: NISN wajib 10 digit numerik unik, NIK 16 digit, gender `L/P`, format tanggal ISO `YYYY-MM-DD`.
   - Batch insert dengan transaksi basis data (`DB::beginTransaction() / rollBack()`) untuk menjamin tidak terjadi inkonsistensi data jika ada baris Excel yang korup.
   - Menghasilkan rekap baris sukses vs baris gagal lengkap dengan nomor baris dan deskripsi galat validasi.

---

## 11. Master Seeder Orchestrator & Lifecycle Activation (`manifest.json`)

Kontrak modul pada `backend/Modules/School/manifest.json`:
```json
"lifecycle": {
  "preserve_data_on_deactivate": true,
  "seeders_on_activate": [
    "Modules\\School\\Database\\Seeders\\SchoolDatabaseSeeder"
  ]
}
```

**Struktur Eksekusi `SchoolDatabaseSeeder.php`**:
```php
public function run(): void
{
    $this->call([
        SchoolRoleSeeder::class,           // 1. Spatie Roles & Permissions
        SchoolPhase1Seeder::class,         // 2. Tahun Ajaran, Rombel, Master Siswa
        SchoolPhase2Seeder::class,         // 3. GTK, Jadwal KBM, Presensi
        SchoolPhase3Seeder::class,         // 4. Asesmen, Rapor, P5, Konseling BK
        SchoolPhase4Seeder::class,         // 5. Surat TU, PPDB, Buku Tamu, OSIS
        SchoolPhase5Seeder::class,         // 6. Sarpras, Katampi Sync, WA Gateway
        SchoolVotingEngineSeeder::class,   // 7. Engine Voting OSIS & Token Kiosk
    ]);
}
```

---

## 12. Peta Rute Multi-Portal Frontend (`router.ts`)

Mengadopsi pola routing terpisah dari `cijulang/frontend/src/modules/School/router/index.ts`:

| Portal | Prefix URL | Peruntukan & Route Guard |
|---|---|---|
| **Portal Tata Usaha & Admin** | `/manage/school/*` | Akses staf TU, Waka, dan Kepala Sekolah (Guard: `manage school`) |
| **Portal Pendidik (Guru)** | `/manage/school/teacher/*` | Dashboard guru, input nilai raport, absensi kelas, agenda mengajar (Guard: `guru` / `wali-kelas`) |
| **Portal Peserta Didik (Siswa)** | `/manage/school/student/*` | Jadwal kelas, histori absensi, nilai e-raport, kartu pemilih (Guard: `siswa`) |
| **Portal Bilik Suara Kiosk** | `/manage/school/voting/kiosk` | Mode layar penuh bilik suara OSIS (Guard: Token DPT 8-karakter, no auth) |
| **Portal PPDB Publik** | `/admission/*` | Pendaftaran mandiri calon siswa baru & pengunggahan berkas (Guard: Publik) |
| **Portal Verifikasi Dokumen** | `/public/verify/:hash` | Verifikasi keaslian ijazah/raport/surat dinas via QR Code publik (Guard: Publik) |

---

## 13. Aturan Validasi Domain Baku Indonesia (`app/Rules/`)

Diadaptasi dari `ja-siska/backend/app/Rules/` untuk menjamin kepatuhan data terhadap standar Kemendikbudristek & Dukcapil:

| Kelas Rule | Sumber Asal | Algoritma & Validasi |
|---|---|---|
| **`ValidNIK`** | `ja-siska/app/Rules/ValidNIK.php` | Validasi 16-digit nomor induk kependudukan: pengecekan 2-digit kode provinsi (01-94), kode kabupaten, kode kecamatan, dan penyesuaian tanggal lahir perempuan (offset +40). |
| **`ValidNISN`** | `ja-siska/app/Rules/ValidNISN.php` | Validasi 10-digit NISN Dapodik dengan verifikasi checksum modulus 10. |
| **`ValidPhoneNumber`** | `ja-siska/app/Rules/ValidPhoneNumber.php` | Pembersihan karakter non-digit & normalisasi nomor seluler Indonesia (`08`, `628`, `+628`) agar siap dikirim via WhatsApp Gateway API. |

---

## 14. Standardisasi Penanganan Error Domain (`app/Exceptions/`)

Diadaptasi dari `cijulang/backend/Modules/School/app/Exceptions/SchoolModuleException.php`:

Untuk mencegah controller mengembalikan respons error ad-hoc atau status code yang tidak seragam:
```php
namespace Modules\School\Exceptions;

class SchoolModuleException extends \Exception
{
    public static function notFound(string $entity, int|string $id): self { ... 404 }
    public static function unauthorized(string $action = ''): self { ... 403 }
    public static function validation(string $message): self { ... 422 }
    public static function businessRule(string $message): self { ... 409 } // misal: siswa sudah memilih di voting
    public static function dataIntegrity(string $message): self { ... 500 }
}
```

---

## 15. Mesin Cetak Berkas Resmi & Dokumen PDF (`resources/views/reports/`)

Mengadopsi template Blade cetak dari `cijulang`, `ja-sis`, dan `ja-evoting`:

| Berkas Template | Sumber Asal | Format & Penggunaan |
|---|---|---|
| `reports/student_profile.blade.php` | `cijulang` | Cetak Biodata Lengkap Siswa Buku Induk (format A4 Dapodik). |
| `reports/id_card.blade.php` | `cijulang` | Cetak Kartu Pelajar (KTA Siswa) berbarcode/QR code verifikasi. |
| `reports/graduation_skl.blade.php` | `cijulang` | Cetak Surat Keterangan Lulus (SKL) Resmi bertanda tangan digital. |
| `reports/raport_merdeka.blade.php` | `ja-sis` (`raport/pdf.blade.php`) | Cetak Buku Rapor Kurikulum Merdeka (Capaian Pembelajaran, Leger Nilai, Catatan Wali Kelas). |
| `reports/voter_card.blade.php` | `ja-evoting` (`voter-card.blade.php`) | Cetak Kartu Akses Bilik Suara DPT OSIS (format A6 dengan QR Token). |

---

## 16. Dekomposisi Dashboard Adaptif Berbasis Peran (*Role-Tailored Dashboards*)

Diadaptasi dari `cijulang/frontend/src/modules/School/components/dashboard/`:

Struktur folder `frontend/src/modules/School/views/admin/dashboard/` dan `components/dashboard/` bertugas merender ringkasan metrik secara personal sesuai peran user yang login:

| Komponen Dashboard | Peran Pengguna | Metrik Utama yang Ditampilkan |
|---|---|---|
| **`SchoolAdminDashboard.vue`** | Kepala Sekolah / Admin | Ringkasan KPI sekolah: total rombel, rasio guru-siswa, kehadiran hari ini, status perbaikan sarpras. |
| **`CurriculumDashboard.vue`** | Waka Kurikulum | Progress pengisian jurnal KBM guru, kelengkapan nilai raport per rombel, jadwal bentrok. |
| **`StudentAffairsDashboard.vue`**| Waka Kesiswaan & BK | Statistik kredit poin disiplin, kasus eskalasi SP1-SP3, sesi bimbingan konseling berjalan. |
| **`TeacherDashboard.vue`** | Guru Pengajar | Jadwal mengajar hari ini, form jurnal KBM cepat, absensi jam pelajaran berjalan. |
| **`StudentDashboard.vue`** | Siswa | Jadwal pelajaran kelas, riwayat absensi mandiri, status pemilihan OSIS, nilai e-rapor. |
| **`ParentDashboard.vue`** | Orang Tua Siswa | Notifikasi kehadiran harian anak, rekap poin perilaku, pengumuman sekolah. |
| **`OsisDashboard.vue`** | Panitia / Pengurus OSIS | Monitoring partisipasi DPT, hitung cepat perolehan suara (quick count) bilik suara. |

---

## 17. Jaminan Mutu & Rencana Verifikasi
Setiap tahapan porting dan penyelarasan wajib melalui pintu uji otomatis:
1. **Backend Tests**: `php artisan test Modules/School` (47/47 tests wajib passed, zero regressions).
2. **TypeScript Checker**: `npx vue-tsc -b` (0 errors).
3. **i18n Symmetry**: `npm run i18n:check` (100% parity across `id`, `en`, `su` - 10.136 keys).
4. **Vite Production Build**: `npm run build` (sukses bundle tanpa chunk error).
5. **Zero Empty Folders Validation**: `find frontend/src/modules/School/views/ -type d -empty` harus mengembalikan 0 baris.

