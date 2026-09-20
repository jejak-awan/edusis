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
- `backend/Modules/School/config/school.php` — **BELUM ADA**.

### C. Ketersediaan Aset Siap Pakai di `repo_referensi`:
- **`cijulang/backend/Modules/School/app/Http/Requests/`**:
  - `Student/` (`StoreStudentRequest.php` 80 baris validasi Dapodik lengkap, `UpdateStudentRequest.php`, `StoreViolationRequest.php`, `StoreCounselingRecordRequest.php`, `StoreAchievementRequest.php`).
  - `Academic/` (`StoreAttendanceRequest.php`, `StoreJournalRequest.php`, `StoreScheduleRequest.php`).
  - `Operations/` (`CheckInVisitorRequest.php`, `StoreAlumniRequest.php`, `StoreGuestLogRequest.php`, `StoreTracerStudyRequest.php`).
  - `HR/`, `Admission/`, `Logistics/`.
- **`cijulang/backend/Modules/School/app/Services/`**:
  - `AcademicService.php` (312 baris), `StudentService.php`, `HRService.php`, `OperationsService.php`, `LogisticsService.php`, `AdmissionService.php`.
- **`ja-siska/backend/app/Services/`**:
  - `KreditPoinService.php` (kalkulasi poin disiplin & ambang batas SP1/SP2/SP3).
  - `BKService.php` (konseling privat terlindungi).
  - `PresensiService.php` & `WhatsAppService.php`.
- **`cijulang/frontend/src/modules/School/`**:
  - `services/`: `AcademicService.ts`, `StudentService.ts`, `HRService.ts`, `OperationsService.ts`, `LogisticsService.ts`, `AdmissionService.ts`.
  - `stores/`: `academic.ts`, `student.ts`, `hr.ts`, `logistics.ts`, `operations.ts`, `school.ts`.
  - `views/admin/students/`: `Index.vue`, `Create.vue`, `Edit.vue`, `Show.vue`, dan sub-komponen `StudentForm.vue` (14 KB form Dapodik).
  - `views/admin/academic/`: `Index.vue`, `Timetable.vue`.
  - `views/teacher/`: `Dashboard.vue`, `GradesInput.vue`, `Schedule.vue`.
  - `views/student/`: `Dashboard.vue`, `Grades.vue`, `Schedule.vue`.

---

## 3. Rencana Aksi Penyelarasan (Implementation Plan)

### Fase 1: Backend SoC (Porting & Alignment Requests & Services)
1. **Buat `backend/Modules/School/config/school.php`**:
   - Daftarkan konstanta kuota DPT, toleransi check-in presensi, default threshold SP SISKA, dan batas waktu voting.
2. **Porting Layer FormRequest dari `cijulang`**:
   - Salin dan selaraskan namespace/rules ke `backend/Modules/School/app/Http/Requests/`:
     - `Student/` (`StoreStudentRequest`, `UpdateStudentRequest`, `StoreViolationRequest`, `StoreCounselingRecordRequest`).
     - `Academic/` (`StoreAcademicYearRequest`, `StoreScheduleRequest`, `StoreAttendanceRequest`, `StoreJournalRequest`, `StoreAssessmentRequest`).
     - `Operations/` (`CheckInVisitorRequest`, `StoreLetterRequest`, `StorePpdbRequest`).
     - `Facilities/` (`StoreRoomRequest`, `StoreAssetRequest`, `StoreMaintenanceTicketRequest`).
     - `Voting/` (`StoreVotingEventRequest`, `CastVoteRequest`, `GenerateVotingVotersRequest`).
3. **Porting Layer Services dari `cijulang` & `ja-siska`**:
   - Salin dan selaraskan ke `backend/Modules/School/app/Services/`:
     - `Academic/AcademicService.php` & `AssessmentCalculationService.php`.
     - `Student/StudentService.php` & `DisciplineService.php` (diadaptasi dari `KreditPoinService.php` `ja-siska`).
     - `Student/CounselingService.php` (diadaptasi dari `BKService.php` `ja-siska`).
     - `Attendance/AttendanceService.php`.
     - `StudentAffairs/VotingEngineService.php`.
     - `Operations/LetterService.php` & `GuestBookService.php`.
     - `Facilities/FacilityService.php`.
     - `Integration/KatampiSyncService.php` & `WhatsAppGatewayService.php`.
4. **Refactor Controllers menjadi Thin Controllers**:
   - Seluruh method controller mendelegasikan validasi ke FormRequest dan operasi data ke Service.
   - Hapus seluruh validasi inline `$request->validate()`.
5. **Verifikasi Backend**: Jalankan `php artisan test Modules/School` (Wajib 47 tests passed).

### Fase 2: Frontend SoC (Porting Services, Stores, Views & Sub-Components)
1. **Pecah Monolithic Service**:
   - Buat `frontend/src/modules/School/types/` terpisah per domain (`academic.ts`, `student.ts`, `attendance.ts`, `voting.ts`, `facility.ts`, `operations.ts`).
   - Buat `frontend/src/modules/School/services/` terpisah mengadopsi `cijulang`: `academicService.ts`, `studentService.ts`, `hrService.ts`, `operationsService.ts`, `facilityService.ts`, `votingService.ts`.
   - Pertahankan `schoolService.ts` sebagai facade re-export agar tidak memutus kode yang ada.
2. **Porting Pinia Stores dari `cijulang`**:
   - Salin dan aktifkan stores di `frontend/src/modules/School/stores/`: `useAcademicStore.ts`, `useStudentStore.ts`, `useHrStore.ts`, `useVotingStore.ts`, `useOperationsStore.ts`.
3. **Isi Folder-Folder Kosong di `views/` & Dekomposisi Komponen**:
   - Migrasikan sub-views & form dari `cijulang`:
     - `views/admin/students/` (`Index.vue`, `Create.vue`, `Edit.vue`, `Show.vue`, `components/StudentForm.vue`).
     - `views/admin/academic/` (`Index.vue`, `Timetable.vue`).
     - `views/teacher/` (`Dashboard.vue`, `GradesInput.vue`, `Schedule.vue`).
     - `views/student/` (`Dashboard.vue`, `Grades.vue`, `Schedule.vue`).
   - Dekomposisi modal dari `OsisVoting.vue` ke `components/voting/` (`KioskTokenModal.vue`, `VoteReceiptModal.vue`, `DptVoterTable.vue`, `CandidateCard.vue`).
4. **Verifikasi Frontend**: Jalankan `npm run i18n:check`, `npx vue-tsc -b`, dan `npm run build`.

### Fase 3: Kontrak Dokumentasi Modul (SoT L1)
1. Terbitkan `backend/Modules/School/README.md` & `CHANGELOG.md`.
2. Terbitkan `frontend/src/modules/School/README.md` & `CHANGELOG.md`.

---

## 4. Acceptance Criteria
- [ ] 0 folder kosong di dalam `frontend/src/modules/School/views/`.
- [ ] 0 validasi inline `$request->validate()` di dalam API Controller backend.
- [ ] Layer `app/Http/Requests/` dan `app/Services/` aktif penuh di backend.
- [ ] Seluruh 47 feature test di backend lulus 100% (`php artisan test Modules/School`).
- [ ] `npm run i18n:check` lulus 100% simetris (`id`, `en`, `su`).
- [ ] `npx vue-tsc -b` lulus tanpa galat tipe TypeScript.
- [ ] `npm run build` sukses terkompilasi.
- [ ] Dokumen kontrak L1 (`README.md` & `CHANGELOG.md`) lengkap di BE & FE.

---

## 5. Log Proses
| Waktu | Catatan |
| :--- | :--- |
| 2026-09-20 18:20 | Penyelarasan rencana kerja dengan aset `repo_referensi` berbasis ADR-031. |
