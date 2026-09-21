# Changelog - School Module (Frontend)

All notable changes to this module will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [1.2.0] - 2026-09-22

### Added
- **Bursa Kerja Khusus (BKK / Career & Job Vacancies)**:
  - Sub-module `school.career` registered in `registry.ts`, `router.ts`, and `navigation.ts`.
  - Type definitions: `types/career.ts` (`JobVacancyData`, `JobApplicationData`, `CareerStats`).
  - Service: `services/careerService.ts` for vacancies & application review workflows.
  - View: `views/career/JobPortal.vue` with 4 KPI summary cards, multi-filter, vacancy cards, and applicant drawer.
  - Dialog: `components/career/VacancyDialog.vue` for partner company vacancy publishing.
- **Backend Graduation Batches & Alumni Tracer**:
  - Eloquent models: `GraduationBatch`, `Alumni`, `JobVacancy`, `JobApplication`.
  - Database migration: `2026_09_20_000014_create_school_alumni_and_career_tables.php`.
  - Controllers: `GraduationController`, `AlumniController`, `CareerController`.
  - Automated feature test: `SchoolAlumniAndCareerApiTest.php` (100% pass).

## [1.1.0] - 2026-09-22

### Added
- **Sub-Module Registry & Feature Toggles**: Implemented `SchoolFeatureRegistry` (`registry.ts`) and defined sub-feature metadata for all 12 operational sub-modules and capability engines (`school.core`, `school.students`, `school.staff`, `school.academic`, `school.attendance`, `school.discipline`, `school.operations`, `school.osis`, `school.extracurricular`, `school.graduation`, `school.admission`, `school.integrations`).
- **Domain Types Layer (`types/`)**: Created 13 domain interface modules (`common.ts`, `academic.ts`, `student.ts`, `staff.ts`, `facility.ts`, `attendance.ts`, `discipline.ts`, `operations.ts`, `osis.ts`, `extracurricular.ts`, `graduation.ts`, `admission.ts`, `integration.ts`).
- **Centralized API Paths Registry (`services/paths.ts`)**: Single Source of Truth for all endpoint paths (`schoolPaths`).
- **Domain Pinia Stores (`stores/`)**: Re-architected into `use<Domain>Store.ts` pattern with `@/shared/utils/logger` and `ensureArray()` normalizers.
- **Student Details Page (`views/students/Show.vue`)**: Full student profile viewer with tabs for biodata, academic history, attendance summary, and discipline notes.
- **Staff Management Suite (`views/staff/`)**:
  - `views/staff/Create.vue`, `Edit.vue`, and `Show.vue`.
  - `components/staff/StaffForm.vue` (3-tab PTK Dapodik form).
  - `components/staff/SalaryStructureDialog.vue` (remuneration components editor).
- **Teacher Grading View (`views/teacher/GradesInput.vue`)**: Bulk formative, midterm, and final assessment scoring table with auto predicate calculation.
- **Student Portal Views (`views/student/`)**:
  - `views/student/Grades.vue`: Transkrip e-rapor dan capaian pembelajaran.
  - `views/student/Graduation.vue`: Surat Keterangan Lulus (SKL) digital viewer & downloader.
- **E-Voting Capabilities Engine (Fork `ja-evoting`)**:
  - `components/osis/CandidateCard.vue`: Interactive candidate selection card with vision/mission modal.
  - `components/osis/KioskTokenModal.vue`: 8-character voter token authentication modal.
  - `components/osis/VoterCardPreview.vue`: ISO ID-1 printable voter card with QR Code.
  - `components/osis/DptBulkImport.vue`: Bulk DPT registration directly from student roster.
  - `components/osis/VoteResultChart.vue`: Real-time candidate bar chart & leader indicator.
  - `components/shared/QrScanner.vue`: Native camera QR scanner with fallback manual input.
  - `views/osis/ElectionResults.vue`: Live Quick Count monitor with automatic polling.
  - `views/public/OfflineVoting.vue`: Offline kiosk ballot system with local queueing.
- **Attendance & Graduation UX (Fork `ja-sis`)**:
  - `components/attendance/AttendanceChart.vue`: Monthly stacked rate chart.
  - `views/attendance/ClassRecap.vue`: Class attendance ledger with per-student breakdown.
  - `views/attendance/StudentRecap.vue`: Individual student timeline & excuse letter log.
  - `components/graduation/GraduationYearTable.vue`: Graduation cohort absorption tracer.
  - `views/graduation/Alumni.vue`: Alumni directory and career tracking table.
  - `views/graduation/Index.vue`: Graduation batch SKL publisher.
- **Extracurricular Management (`views/extracurricular/Index.vue`)**: Mandatory & elective branches, advisors, practice schedule, and member student rosters.

### Changed
- Refactored `views/` directory structure from flat files into dedicated domain folders (`academic/`, `students/`, `staff/`, `attendance/`, `grades/`, `discipline/`, `operations/`, `osis/`, `extracurricular/`, `graduation/`, `admission/`, `facilities/`, `integrations/`, `teacher/`, `student/`, `public/`).
- Updated `router.ts` routes with `meta.extension: 'school'`, `meta.subFeature`, and `meta.permission`.
- Enhanced `navigation.ts` with sub-feature filtering and new menu entries.

### Removed
- Fully purged all legacy institution references, placeholder addresses, and hardcoded school identifiers in compliance with **ADR-031 Pristine Code Mandate**.
