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
| Dekomposisi dari `OsisVoting.vue` | `components/voting/KioskTokenModal.vue`, `DptTable.vue`, `CandidateCard.vue` | Membentuk folder `components/` |

---

## 6. Jaminan Mutu & Rencana Verifikasi
Setiap tahapan porting dan penyelarasan wajib melalui pintu uji otomatis:
1. **Backend Tests**: `php artisan test Modules/School` (47/47 tests wajib passed, zero regressions).
2. **TypeScript Checker**: `npx vue-tsc -b` (0 errors).
3. **i18n Symmetry**: `npm run i18n:check` (100% parity across `id`, `en`, `su`).
4. **Vite Production Build**: `npm run build` (sukses bundle tanpa chunk error).
5. **Zero Empty Folders Validation**: `find frontend/src/modules/School/views/ -type d -empty` harus mengembalikan 0 baris.
