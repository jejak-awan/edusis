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
- Refactored all 21 controllers into Thin Controllers inheriting from `BaseController` with zero inline validation.
- Centralized school tenant resolution via `SchoolContextMiddleware` and `ScopedBySchool` trait.
- Applied Laravel Pint code formatting standards and aligned PHPStan Level 9 analysis baseline.

### Removed
- Eliminated all legacy fallback hacks (`School::first() ?? 1`) across all controllers and services.
- Removed unused empty model directories.
