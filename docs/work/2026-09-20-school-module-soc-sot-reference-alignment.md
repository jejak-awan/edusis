---
id: 2026-09-20-school-module-soc-sot-reference-alignment
title: "Restrukturisasi Arsitektur Modul School: SoC, SoT, dan Penyelarasan Aset repo_referensi"
status: proposed
scale: L
repo: ja-edusis
owner: agent
created: 2026-09-20
updated: 2026-09-20
blocks: []
related_adr: ["docs/adr/ADR-031-school-module-soc-sot-and-reference-alignment.md"]
---

# Task: Restrukturisasi Arsitektur Modul School: SoC, SoT, dan Penyelarasan Aset `repo_referensi`

## 1. Tujuan
Mengeliminasi seluruh folder kosong pasca-scaffold, meniadakan kode redundan/duplikasi, serta meningkatkan persentase kelengkapan arsitektur modul `School` hingga 100% mematuhi prinsip **Separation of Concerns (SoC)**, **Single Source of Truth (SoT)**, dan **standar pemrograman enterprise** Jejakawan dengan mengadopsi (*adopt & align*) aset-aset matang yang sudah tersedia di `/home/jejakawan/dev/repo_referensi/` (`cijulang`, `ja-siska`, `ja-sis`).

---

## 2. Audit (Current State)

### A. Temuan Folder Kosong di Frontend `ja-edusis`:
- `frontend/src/modules/School/views/admin/academic/` — **KOSONG**
- `frontend/src/modules/School/views/admin/attendance/` — **KOSONG**
- `frontend/src/modules/School/views/admin/counseling/` — **KOSONG**
- `frontend/src/modules/School/views/admin/dashboard/` — **KOSONG**
- `frontend/src/modules/School/views/admin/discipline/` — **KOSONG**
- `frontend/src/modules/School/views/admin/grades/` — **KOSONG**
- `frontend/src/modules/School/views/admin/students/` — **KOSONG**
- `frontend/src/modules/School/views/teacher/` — **KOSONG**
- `frontend/src/modules/School/views/student/` — **KOSONG**
- `frontend/src/modules/School/views/public/` — **KOSONG**
- `frontend/src/modules/School/components/` — **BELUM ADA**

*Penyebab:* Tampilan antarmuka saat ini ditumpuk sebagai berkas flat tunggal langsung di root `views/*.vue`, mengabaikan struktur folder sub-domain yang disiapkan.

### B. Temuan Layer Backend `ja-edusis`:
- `backend/Modules/School/app/Http/Requests/` — **0 berkas (KOSONG)**. Semua controller memakai `$request->validate()` inline.
- `backend/Modules/School/app/Services/` — **0 berkas (KOSONG)**. Logika bisnis KBM, rapor, presensi, dan kripto bercampur di controller.
- `backend/Modules/School/app/Policies/` — **0 berkas (KOSONG)**. Belum ada policy penjaga otorisasi model (terutama data privat konseling BK & mutasi nilai).
- `backend/Modules/School/database/factories/` — **0 berkas (KOSONG)**. Belum ada factory pendukung unit/feature testing dan load testing.
- `backend/Modules/School/config/school.php` — **BELUM ADA**.
- `backend/Modules/School/manifest.json` — **Defisit Kapabilitas & Permissions**. Hanya mendefinisikan 4 kapabilitas (`academic`, `students`, `discipline`, `counseling`), belum memuat `staff`, `letters`, `dispositions`, `guest_book`, `ppdb`, `facilities`, `voting`, `katampi`, dan `whatsapp`.
- `backend/Modules/School/database/seeders/SchoolDatabaseSeeder.php` — **BELUM ADA**, padahal dideklarasikan pada kontrak manifest `"seeders_on_activate"`.
- `SchoolContextMiddleware` & `ScopedBySchool` trait — **BELUM DIIMPLEMENTASI**, akibatnya controller mengulang pemanggilan manual `$this->resolveSchoolId()`.
- Strategi Storage Isolasi (Public vs Private) — Belum dipisahkan antara berkas publik (foto profil, foto paslon) dan berkas privat terlindungi (dokumen pendaftaran PPDB, rekaman kasus konseling BK, pindaian surat dinas).

### C. Ketersediaan Aset Siap Pakai di `repo_referensi`:
- **`cijulang/backend/Modules/School/`**:
  - `app/Http/Requests/`: `Student/` (`StoreStudentRequest.php` 80 baris validasi Dapodik lengkap, `UpdateStudentRequest.php`, `StoreViolationRequest.php`, `StoreCounselingRecordRequest.php`, `StoreAchievementRequest.php`), `Academic/`, `Operations/`, `HR/`, `Admission/`, `Logistics/`.
  - `app/Services/`: `AcademicService.php` (312 baris), `StudentService.php`, `HRService.php`, `OperationsService.php`, `LogisticsService.php`, `AdmissionService.php`, `Reports/ReportService.php`.
  - `app/Policies/`: `AcademicPolicy.php`, `StudentPolicy.php`, `StaffPolicy.php`, `SarprasResourcePolicy.php`, `SchoolPolicy.php`, `CoursePolicy.php`.
  - `app/Traits/`: `ScopedBySchool.php` (Laravel 11 Context global scope multi-tenancy), `HasVerificationHash.php` (QR code verification URL generator).
  - `database/factories/`: `StudentFactory.php`, `StaffFactory.php`, `AcademicYearFactory.php`, `SchoolFactory.php`, `SchoolLevelFactory.php`.
  - `database/seeders/`: `SchoolRoleSeeder.php` (Spatie permissions & roles), `SchoolDatabaseSeeder.php`.
- **`ja-siska/backend/app/`**:
  - `Services/`: `KreditPoinService.php` (kalkulasi akumulasi poin disiplin & ambang batas SP1/SP2/SP3), `BKService.php` (konseling privat `is_confidential`), `PresensiService.php`, `WhatsAppService.php` (19.2 KB templating & blast pesan ortu), `ImportDataService.php` (18.8 KB engine import Excel Dapodik dengan `PhpSpreadsheet`).
  - `Events/`: `DashboardStatsUpdated.php`, `KreditPoinCreated.php`, `PresensiCreated.php`, `KonselingCreated.php`.
- **`ja-sis/backend/app/`**:
  - `Services/Raport/RaportService.php` (kalkulasi leger & nilai raport Kurikulum Merdeka).
  - `Services/Surat/TemplateSuratService.php` (penomoran otomatis dinas & format variabel surat).
- **`ja-evoting/app/`**:
  - `Http/Controllers/`: `VotingEngineController`, `VoterCardController.php` (generate kartu pemilih PDF + QR), `OfflineVotingController.php`.
  - `Http/Requests/`: `StoreEventRequest.php`, `StoreCandidateRequest.php`, `StoreVoteRequest.php`, `StoreVoterRequest.php`.
- **`cijulang/frontend/src/modules/School/`**:
  - `services/`: `AcademicService.ts`, `StudentService.ts`, `HRService.ts`, `OperationsService.ts`, `LogisticsService.ts`, `AdmissionService.ts`.
  - `stores/`: `academic.ts`, `student.ts`, `hr.ts`, `logistics.ts`, `operations.ts`, `school.ts`.
  - `views/admin/students/`: `Index.vue`, `Create.vue`, `Edit.vue`, `Show.vue`, dan sub-komponen `StudentForm.vue` (14 KB form Dapodik).
  - `views/admin/academic/`: `Index.vue`, `Timetable.vue`.
  - `views/teacher/`: `Dashboard.vue`, `GradesInput.vue`, `Schedule.vue`.
  - `views/student/`: `Dashboard.vue`, `Grades.vue`, `Schedule.vue`.
  - `router/index.ts`: Peta rute multi-portal (Admin, Guru, Siswa, PPDB, Verifikasi Publik).

---

## 3. Rencana Aksi Penyelarasan (Implementation Plan)
*Rujukan Pemetaan Detail Berkas:* [`docs/sis/07-comprehensive-reference-mapping-and-alignment.md`](../sis/07-comprehensive-reference-mapping-and-alignment.md)

### Fase 1: Fondasi Tata Kelola & Kontrak Manifest (Multi-Tenancy & Permissions)
1. **Pembaruan `backend/Modules/School/manifest.json`**:
   - Daftarkan kapabilitas & aksi lengkap: `academic`, `students`, `discipline`, `counseling`, `staff`, `attendance`, `grades`, `letters`, `dispositions`, `guest_book`, `ppdb`, `facilities`, `voting`, `katampi`, `whatsapp`.
   - Daftarkan daftar Spatie permissions lengkap yang dibutuhkan oleh Core Engine IAM.
2. **Implementasi `SchoolRoleSeeder.php` & `SchoolDatabaseSeeder.php`**:
   - Mengadopsi `SchoolRoleSeeder` dari `cijulang` untuk meregistrasikan peran baku (`kepala-sekolah`, `admin-kurikulum`, `admin-kesiswaan`, `admin-sarpras`, `admin-bk`, `guru`, `wali-kelas`, `siswa`, `orang-tua`).
   - Membuat `SchoolDatabaseSeeder.php` sebagai orchestrator yang menjalankan seeder peran dan seeder data bertahap (Phase 1–5 + VotingEngine).
3. **Penyusunan Multi-Tenancy Scoping**:
   - Buat `app/Http/Middleware/SchoolContextMiddleware.php` untuk memvalidasi dan menginjeksi `school_id` ke dalam Laravel 11 Context (`Context::add('school_id', ...)`).
   - Porting trait `app/Traits/ScopedBySchool.php` dari `cijulang` agar seluruh model `sch_*` otomatis mengaplikasikan scope `school_id`.

### Fase 2: Backend SoC (Porting FormRequests, Services, Policies & Factories)
1. **Buat `backend/Modules/School/config/school.php`**:
   - Daftarkan konstanta kuota DPT, toleransi check-in presensi, default threshold SP SISKA, format nomor surat dinas, dan batas waktu voting.
2. **Porting Layer FormRequest dari `cijulang` & `ja-evoting`**:
   - Salin dan selaraskan namespace/rules ke `backend/Modules/School/app/Http/Requests/`:
     - `Student/` (`StoreStudentRequest`, `UpdateStudentRequest`, `StoreViolationRequest`, `StoreCounselingRecordRequest`, `StoreAchievementRequest`).
     - `Academic/` (`StoreAcademicYearRequest`, `StoreScheduleRequest`, `StoreAttendanceRequest`, `StoreJournalRequest`, `StoreAssessmentRequest`).
     - `Operations/` (`CheckInVisitorRequest`, `StoreLetterRequest`, `StoreDispositionRequest`, `StorePpdbRequest`).
     - `Facilities/` (`StoreRoomRequest`, `StoreAssetRequest`, `StoreMaintenanceTicketRequest`).
     - `Voting/` (`StoreVotingEventRequest`, `AddVotingCandidateRequest`, `CastVoteRequest`, `GenerateVotingVotersRequest`).
3. **Porting Layer Services dari `cijulang`, `ja-siska`, dan `ja-sis`**:
   - Salin dan selaraskan ke `backend/Modules/School/app/Services/`:
     - `Academic/AcademicService.php` & `AssessmentCalculationService.php`.
     - `Student/StudentService.php` & `DisciplineService.php` (diadaptasi dari `KreditPoinService.php` `ja-siska`).
     - `Student/CounselingService.php` (diadaptasi dari `BKService.php` `ja-siska`).
     - `Attendance/AttendanceService.php`.
     - `StudentAffairs/VotingEngineService.php`.
     - `Operations/LetterService.php` & `GuestBookService.php`.
     - `Facilities/FacilityService.php`.
     - `Integration/KatampiSyncService.php` & `WhatsAppGatewayService.php`.
     - `Data/ImportDataService.php` (diadaptasi dari `ImportDataService.php` `ja-siska` untuk impor Excel Dapodik).
4. **Porting Model Policies dari `cijulang`**:
   - Buat di `backend/Modules/School/app/Policies/`: `StudentPolicy.php`, `AcademicPolicy.php`, `StaffPolicy.php`, `CounselingPolicy.php`, `SarprasResourcePolicy.php`, `VotingPolicy.php`.
   - Mengunci akses catatan konseling BK sensitif (`is_confidential`) hanya untuk konselor BK dan kepala sekolah.
5. **Porting Database Model Factories dari `cijulang`**:
   - Buat di `backend/Modules/School/database/factories/`: `StudentFactory.php`, `StaffFactory.php`, `AcademicYearFactory.php`, `SchoolFactory.php`.
6. **Porting Domain Validation Rules (`app/Rules/`)**:
   - Adopsi dari `ja-siska/backend/app/Rules/`: `ValidNIK.php` (algoritma NIK 16 digit + offset wanita), `ValidNISN.php` (algoritma NISN 10 digit + checksum), `ValidPhoneNumber.php` (normalisasi nomor HP Indonesia untuk WhatsApp gateway).
7. **Standardisasi Domain Exception (`app/Exceptions/`)**:
   - Adopsi dari `cijulang`: `SchoolModuleException.php` untuk standardisasi exception code (404, 403, 409, 422, 500).
8. **Penyediaan Mesin Cetak Berkas Resmi & Dokumen PDF (`resources/views/reports/`)**:
   - Porting template cetak Blade dari `cijulang`, `ja-sis`, dan `ja-evoting`: `student_profile.blade.php` (biodata Dapodik), `id_card.blade.php` (KTA siswa ber-QR), `graduation_skl.blade.php` (SKL), `raport_merdeka.blade.php` (buku rapor), dan `voter_card.blade.php` (kartu pemilih DPT).
9. **Strategi Storage & Isolasi Media**:
   - Konfigurasi disk privat (`storage/app/private/school_{id}/`) untuk lampiran BK, arsip surat, dan dokumen PPDB.
   - Konfigurasi disk publik (`storage/app/public/school_{id}/`) untuk foto siswa, foto paslon, dan dokumentasi sarpras.
10. **Refactor Controllers menjadi Thin Controllers**:
   - Seluruh method controller mendelegasikan validasi ke FormRequest dan operasi data ke Service.
   - Hapus seluruh validasi inline `$request->validate()`.
11. **Verifikasi Backend**: Jalankan `php artisan test Modules/School` (Wajib 47 tests passed, 0 regression).

### Fase 3: Frontend SoC (Modular Types, Services, Stores, Views & Sub-Components)
1. **Pecah Monolithic Service**:
   - Buat `frontend/src/modules/School/types/` terpisah per domain (`academic.ts`, `student.ts`, `attendance.ts`, `voting.ts`, `facility.ts`, `operations.ts`, `staff.ts`).
   - Buat `frontend/src/modules/School/services/` terpisah mengadopsi `cijulang`: `academicService.ts`, `studentService.ts`, `hrService.ts`, `operationsService.ts`, `facilityService.ts`, `votingService.ts`, `katampiService.ts`, `whatsappService.ts`.
   - Pertahankan `schoolService.ts` sebagai facade re-export agar tidak memutus kode yang ada.
2. **Porting Pinia Stores dari `cijulang`**:
   - Salin dan aktifkan stores di `frontend/src/modules/School/stores/`: `useAcademicStore.ts`, `useStudentStore.ts`, `useHrStore.ts`, `useVotingStore.ts`, `useOperationsStore.ts`.
3. **Isi Folder-Folder Kosong di `views/` & Dekomposisi Komponen**:
   - Migrasikan sub-views & form dari `cijulang`:
     - `views/admin/students/` (`Index.vue`, `Create.vue`, `Edit.vue`, `Show.vue`, `components/StudentForm.vue` 14 KB Dapodik).
     - `views/admin/academic/` (`Index.vue`, `Timetable.vue`).
     - `views/teacher/` (`Dashboard.vue`, `GradesInput.vue`, `Schedule.vue`).
     - `views/student/` (`Dashboard.vue`, `Grades.vue`, `Schedule.vue`).
   - Dekomposisi Dashboard Adaptif Berbasis Peran (`components/dashboard/`):
     - `SchoolAdminDashboard.vue`, `CurriculumDashboard.vue`, `StudentAffairsDashboard.vue`, `TeacherDashboard.vue`, `StudentDashboard.vue`, `ParentDashboard.vue`, `OsisDashboard.vue`.
   - Dekomposisi modal dari `OsisVoting.vue` ke `components/voting/` (`KioskTokenModal.vue`, `VoteReceiptModal.vue`, `DptVoterTable.vue`, `CandidateCard.vue`).
4. **Pendaftaran Rute Multi-Portal (`router.ts`)**:
   - Daftarkan rute eksplisit portal Guru (`/manage/school/teacher/*`), portal Siswa (`/manage/school/student/*`), dan portal PPDB Publik (`/admission/*`) dengan meta permission RBAC.
5. **Verifikasi Frontend**: Jalankan `npm run i18n:check`, `npx vue-tsc -b`, dan `npm run build`.

### Fase 4: Kontrak Dokumentasi Modul (SoT L1)
1. Terbitkan `backend/Modules/School/README.md` & `CHANGELOG.md`.
2. Terbitkan `frontend/src/modules/School/README.md` & `CHANGELOG.md`.

---

## 4. Tiga Mandat Kualitas & Standarisasi Eksekusi

Sesuai instruksi dan keselarasan platform Jejakawan Core Engine:

### A. Anotasi & Komentar Kode Bersih (*Pristine Docblocks & No Stale Comments*)
- **DILARANG** menyalin atau mempertahankan komentar peninggalan (*legacy comments*) dari `repo_referensi`, seperti referensi ke institusi spesifik ("SMKN 1 Cijulang", author asli, TODO basi, atau potongan kode yang di-comment out).
- Seluruh berkas baru maupun refaktor wajib menggunakan anotasi PHPDoc / JSDoc yang bersih, profesional, dan seragam sesuai standar Core Engine (`@param`, `@return`, `@throws`, strict typing).

### B. Pembersihan Menyeluruh Kode Usang & Fallback Hack (*Clean Refactor & Legacy Purge*)
- **ELIMINASI TOTAL** fallback hack darurat seperti `$school = School::first(); return $school ? $school->id : 1;`.
- Pengelolaan tenant wajib sepenuhnya diserahkan ke `SchoolContextMiddleware` dan `ScopedBySchool`.
- Hapus semua *dead code*, import tidak terpakai (*unused imports*), dan method controller usang. Hasil refactor harus murni (*clean codebase*), bukan kode bertumpuk tambal-sulam.

### C. Konsistensi Ketat terhadap Dokumen Standar Core Engine (`docs/`)
- **Backend Standards** ([`docs/architecture/02-backend-standards.md`](../architecture/02-backend-standards.md)):
  - *Thin Controllers*: Tidak ada query query SQL / Eloquent langsung atau validasi inline di controller.
  - *Standard JSON Response*: Format seragam `{ success: true, data: ..., message: ... }` atau `{ success: false, message: ..., errors: ... }`.
  - *FormRequest & Services*: Validasi terpusat di `app/Http/Requests/` dan alur logika bisnis terenkapsulasi di `app/Services/` dengan `DB::transaction()`.
- **Frontend Standards** ([`docs/architecture/03-frontend-standards.md`](../architecture/03-frontend-standards.md)):
  - *Single Unified Console SPA*: Vue 3.5 Composition API `<script setup lang="ts">`.
  - *Strict Typing*: Tidak boleh menggunakan `any`, seluruh payload API wajib memiliki tipe data di `types/<subdomain>.ts`.
  - *Pinia Stores*: State management reaktif terenkapsulasi per sub-domain.
  - *i18n Parity*: Setiap string UI wajib menggunakan kunci translasi yang simetris 100% pada `id.json`, `en.json`, dan `su.json`.
- **Dokumentasi L1** ([`docs/DOCUMENTATION.md`](../DOCUMENTATION.md)):
  - Menghasilkan berkas kontrak `README.md` dan catatan rilis `CHANGELOG.md` pada modul BE dan FE.

---

## 5. Acceptance Criteria
- [ ] 0 folder kosong di dalam `frontend/src/modules/School/views/`.
- [ ] 0 validasi inline `$request->validate()` di dalam API Controller backend.
- [ ] 0 fallback hack `$school = School::first() ?? 1` di seluruh controller (100% via `Context`).
- [ ] 0 komentar usang/spesifik repo lama yang terbawa ke dalam kode `ja-edusis`.
- [ ] Layer `app/Http/Requests/`, `app/Services/`, `app/Rules/`, dan `app/Exceptions/` aktif penuh di backend.
- [ ] `SchoolContextMiddleware` dan `ScopedBySchool` aktif mengisolasi multi-tenancy.
- [ ] `SchoolDatabaseSeeder.php` dan `SchoolRoleSeeder.php` terdaftar dan dapat dieksekusi tanpa error.
- [ ] Seluruh 47 feature test di backend lulus 100% (`php artisan test Modules/School`).
- [ ] `npm run i18n:check` lulus 100% simetris (`id`, `en`, `su`).
- [ ] `npx vue-tsc -b` lulus tanpa galat tipe TypeScript.
- [ ] `npm run build` sukses terkompilasi.
- [ ] Dokumen kontrak L1 (`README.md` & `CHANGELOG.md`) lengkap di BE & FE.

---

## 6. Log Proses
| Waktu | Catatan |
| :--- | :--- |
| 2026-09-20 18:20 | Penyelarasan rencana kerja dengan aset `repo_referensi` berbasis ADR-031. |
| 2026-09-20 18:35 | Pendalaman komprehensif: Manifest Spatie permissions, Master Seeder, SchoolContextMiddleware, ScopedBySchool trait, Policies, Factories, Storage Strategy, dan ImportDataService. |
| 2026-09-20 18:50 | Penambahan aset Rules (`ValidNIK`, `ValidNISN`, `ValidPhoneNumber`), Exceptions (`SchoolModuleException`), Template PDF Blade, dan Role Dashboards. |
| 2026-09-20 18:55 | Penegasan 3 Mandat Eksekusi: Pembersihan komentar repo lama, pembersihan total fallback hack, dan kepatuhan penuh terhadap standar Core Engine (`02-backend-standards`, `03-frontend-standards`, `DOCUMENTATION.md`). |


