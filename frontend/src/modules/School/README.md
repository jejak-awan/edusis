# School Module (JA-EduSIS Frontend)

Frontend interface untuk Sistem Informasi Sekolah (SIS / EduSIS) berbasis Vue 3 (`<script setup lang="ts">`), Vite, Tailwind CSS, Lucide Icons, dan Pinia. Mengikuti standar arsitektur modul Publishing (**Gold Standard**), mematuhi **ADR-031 (Pristine Code Mandate)** dan **ADR-033 (School Engine Capabilities, Sub-Module Registry, & SoC Standardization)**.

---

## Arsitektur & Sub-Modul Registry (App Store Aligned)

Modul `School` memiliki deklarasi sub-modul independen yang dapat diaktifkan/dinonaktifkan melalui `SchoolFeatureRegistry`:

| Sub-Modul Slug | Nama Sub-Modul | Kategori | Dependensi Wajib | Lisensi / Tier |
|---|---|---|---|---|
| `school.core` | Core School & Instansi | Fondasi | - | Basic / Core |
| `school.students` | Kesiswaan & Dapodik | Operasional | `school.core` | Basic / Core |
| `school.staff` | GTK & Kepegawaian | Operasional | `school.core` | Basic / Core |
| `school.academic` | Akademik & Kurikulum | Operasional | `school.core`, `school.students` | Standard |
| `school.attendance` | Presensi Engine | Capability Engine | `school.students` | Standard |
| `school.discipline` | Tata Tertib & BK | Operasional + Engine | `school.students` | Standard |
| `school.operations` | Persuratan & Dokumen | Capability Engine | `school.core` | Standard |
| `school.osis` | OSIS & E-Voting | Operasional + Engine | `school.students`, `school.operations` | Pro / Addon |
| `school.extracurricular` | Ekstrakurikuler | Operasional | `school.students`, `school.attendance` | Standard |
| `school.graduation` | Kelulusan & Alumni | Operasional | `school.students`, `school.operations` | Pro / Addon |
| `school.career` | Bursa Kerja Khusus (BKK) | Operasional | `school.core`, `school.graduation` | Pro / Addon |
| `school.admission` | PPDB Online | Operasional | `school.core` | Pro / Addon |
| `school.integrations` | Gateway WA & KataMPI | Sistem | `school.core` | Enterprise |

---

## Struktur Berkas (Separation of Concerns)

```
frontend/src/modules/School/
├── CHANGELOG.md
├── README.md
├── module.ts                         # AppModule descriptor
├── registry.ts                       # Sub-Module Registry & Feature Toggles
├── router.ts                         # Route table dengan meta.subFeature & meta.permission
├── navigation.ts                     # Dynamic menu dengan filter sub-fitur aktif
│
├── types/                            # Single Source of Truth TypeScript interfaces
│   ├── index.ts
│   ├── common.ts
│   ├── academic.ts
│   ├── student.ts
│   ├── staff.ts
│   ├── attendance.ts
│   ├── discipline.ts
│   ├── operations.ts
│   ├── osis.ts
│   ├── extracurricular.ts
│   ├── graduation.ts
│   ├── admission.ts
│   ├── facility.ts
│   └── integration.ts
│
├── services/                         # HTTP Services & Endpoints Registry
│   ├── paths.ts                      # Centralized schoolPaths
│   ├── academicService.ts
│   ├── studentService.ts
│   ├── hrService.ts                  # StaffService alias
│   ├── attendanceService.ts
│   ├── counselingService.ts
│   ├── operationsService.ts
│   ├── votingService.ts
│   ├── extracurricularService.ts
│   ├── graduationService.ts
│   ├── facilityService.ts
│   └── integrationService.ts
│
├── stores/                           # Pinia Stores (use<Domain>Store.ts pattern)
│   ├── useAcademicStore.ts
│   ├── useStudentStore.ts
│   ├── useStaffStore.ts
│   ├── useAttendanceStore.ts
│   ├── useDisciplineStore.ts
│   ├── useOperationsStore.ts
│   ├── useOsisStore.ts
│   ├── useExtracurricularStore.ts
│   ├── useGraduationStore.ts
│   ├── useFacilityStore.ts
│   └── useSchoolStore.ts
│
├── components/                       # Komponen Domain Spesifik
│   ├── students/StudentForm.vue      # 5-Tab Form Dapodik
│   ├── staff/StaffForm.vue           # Form PTK Dapodik 3-Tab
│   ├── staff/SalaryStructureDialog.vue
│   ├── attendance/AttendanceChart.vue # Visualisasi tren presensi bulanan
│   ├── osis/CandidateCard.vue        # Kartu paslon interaktif
│   ├── osis/KioskTokenModal.vue      # Modal otentikasi token 8-char
│   ├── osis/VoterCardPreview.vue     # Cetak kartu pemilih DPT (85.6mm x 54mm)
│   ├── osis/DptBulkImport.vue        # Sinkronisasi massal DPT
│   ├── osis/VoteResultChart.vue      # Chart perolehan suara paslon
│   ├── graduation/GraduationYearTable.vue # Rekapitulasi angkatan kelulusan
│   └── shared/QrScanner.vue          # Scanner kamera barcode/QR
│
└── views/                            # Terkelompok per sub-domain semantik
    ├── SchoolDashboard.vue
    ├── academic/                     # Index.vue, TeachingJournals.vue
    ├── students/                     # Index.vue, Create.vue, Edit.vue, Show.vue
    ├── staff/                        # Index.vue, Create.vue, Edit.vue, Show.vue
    ├── attendance/                   # Index.vue, ClassRecap.vue, StudentRecap.vue
    ├── grades/                       # Index.vue, ReportCardGenerator.vue
    ├── discipline/                   # Index.vue, Counseling.vue
    ├── operations/                   # LetterManagement.vue, GuestBook.vue
    ├── osis/                         # Index.vue, ElectionResults.vue
    ├── extracurricular/              # Index.vue
    ├── graduation/                   # Index.vue, Alumni.vue
    ├── admission/                    # Index.vue
    ├── facilities/                   # Index.vue
    ├── integrations/                 # WhatsAppGateway.vue, KatampiIntegration.vue
    ├── teacher/                      # TeacherDashboard.vue, MyTeachingSchedule.vue, GradesInput.vue, JournalEntryView.vue
    ├── student/                      # StudentDashboard.vue, MyReportCardView.vue, MyAttendanceView.vue, Grades.vue, Graduation.vue
    └── public/                       # VotingKioskView.vue, OfflineVoting.vue, PublicPpdbView.vue
```

---

## Standarisasi Kualitas & Keamanan

- **Zero TypeScript Errors**: Mematuhi strict type checking melalui `npm run type-check` (`vue-tsc -b`).
- **Pristine Code Mandate (ADR-031)**: Bersih dari nama institusi spesifik, komentar personal, atau data dummy terikat instansi terdahulu.
- **Single Source of Truth**: Semua entitas (Kehadiran, BK, OSIS, Ekskul, Nilai, Alumni) merujuk ke tabel relasi tunggal `sch_students`.
- **E-Voting Anti-Fraud**: Token pemilih di-hash, satu token hanya dapat memberikan 1 suara, pemisahan surat suara dari identitas pemilih demi kerahasiaan (*ballot decoupling*).
