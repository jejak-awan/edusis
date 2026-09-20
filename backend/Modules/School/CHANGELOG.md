# Changelog - School Module (Backend)

All notable changes to this module will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- **FormRequest Architecture**: 25 dedicated FormRequest classes in `app/Http/Requests/` covering Academic, Attendance, Student, Counseling, Administration, Facilities, HR, and Voting.
- **Pure Service Layer**: 11 pure domain service classes in `app/Services/` with database transactions (`AcademicService`, `AssessmentCalculationService`, `AttendanceService`, `StudentService`, `DisciplineService`, `CounselingService`, `LetterService`, `GuestBookService`, `PpdbService`, `FacilityService`, `VotingEngineService`, `KatampiSyncService`, `WhatsAppGatewayService`, `ImportDataService`).
- **Domain Rules & Exceptions**: `ValidNISN`, `ValidNIK`, `ValidPhoneNumber`, and `SchoolModuleException`.
- **Model Policies**: `StudentPolicy`, `AcademicPolicy`, `CounselingPolicy`, `SarprasResourcePolicy`, `VotingPolicy`, `StaffPolicy`.
- **Database Model Factories**: `StudentFactory`, `StaffFactory`, `SchoolFactory`, `AcademicYearFactory`.
- **Blade Report Templates**: 5 print templates in `resources/views/reports/` (`student_profile`, `id_card`, `graduation_skl`, `raport_merdeka`, `voter_card`).
- **Comprehensive Unit & Feature Tests**: 50 automated tests (501 assertions) verifying all API endpoints and validation rules.

### Changed
- Sanitized client-specific hardcoded entities in Letter Management and demo seeders to ensure generic platform agnosticism.
- Refactored all 21 controllers into Thin Controllers inheriting from `BaseController` with zero inline validation.
- Centralized school tenant resolution via `SchoolContextMiddleware` and `ScopedBySchool` trait.
- Applied Laravel Pint code formatting standards and aligned PHPStan Level 9 analysis baseline.

### Fixed
- Hardened API route parameters with `whereNumber('id')` to eliminate route collision between `{id}` and named actions (`applicants`, `checkout`, `maintenance`).
- Added backward-compatible aliases for `/school/ppdb/applicants`, `/school/guests`, and `/school/facilities/maintenance` to eliminate 500 and HTML-fallback errors.
- Aligned `operationsService.ts` and `facilityService.ts` endpoints with canonical backend route contracts.

### Removed
- Eliminated all legacy fallback hacks (`School::first() ?? 1`) across all controllers and services.
- Removed unused empty model directories.
