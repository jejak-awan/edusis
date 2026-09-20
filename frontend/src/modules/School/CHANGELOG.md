# Changelog - School Module (Frontend)

All notable changes to this module will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- **Student Form Comprehensive View**: `views/admin/students/components/StudentForm.vue` supporting 5 multi-tab steps (Data Pribadi, Alamat, Orang Tua/Wali, Rombel/Akademik, Bantuan PIP/KIP).
- **Dedicated Student Pages**: `views/admin/students/Create.vue` and `views/admin/students/Edit.vue` with Vue Router integration.
- **Teacher Portal Views**: `views/teacher/TeacherDashboard.vue`, `views/teacher/MyTeachingSchedule.vue`, `views/teacher/JournalEntryView.vue`.
- **Student Portal Views**: `views/student/StudentDashboard.vue`, `views/student/MyReportCardView.vue`, `views/student/MyAttendanceView.vue`.
- **Public & Kiosk Views**: `views/public/VotingKioskView.vue` (fullscreen e-voting kiosk with PIN keypad and live receipt) and `views/public/PublicPpdbView.vue` (public registration and status check).
- **Role Dashboard Widgets**: `components/dashboard/SchoolAdminDashboard.vue`, `TeacherDashboardWidget.vue`, `StudentDashboardWidget.vue`.
- **Domain Services & Types**: `services/types.ts`, `services/studentService.ts`, `services/academicService.ts`, `services/attendanceService.ts`, `services/counselingService.ts`, `services/operationsService.ts`, `services/facilityService.ts`, `services/votingService.ts`.
- **Typed Pinia Stores**: `stores/studentStore.ts`, `stores/academicStore.ts`, `stores/votingStore.ts`.

### Changed
- Connected `views/StudentList.vue` to route-based navigation for student registration and editing.
- Updated `router.ts` to register routes for Teacher Portal, Student Portal, Kiosk Voting, and Public PPDB.

### Removed
- Cleaned up empty scaffolding directories in `views/admin/` and `router/`.
