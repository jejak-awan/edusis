---
id: 2026-09-21-school-engine-submodule-registry-and-soc-refactor
title: "Refactoring Modul School: Engine Reusability, Sub-Modul Registry, dan Standarisasi SoC Frontend"
status: in_progress
scale: L
repo: ja-edusis
owner: agent
created: 2026-09-21
updated: 2026-09-21
blocks: []
related_adr: [ADR-030, ADR-031, ADR-033]
---

# Task: Refactoring Modul School — Engine Reusability, Sub-Modul Registry, dan Standarisasi SoC Frontend

## 1. Tujuan

Menyelaraskan modul `School` di `ja-edusis` agar memenuhi standar arsitektur terbaik Core `ja-edusis` (mengacu pada modul rujukan `Publishing`):
1. Mengkategorikan kapabilitas inti menjadi **Sharable Capability Engines** (Attendance, Voting/Election, Document/Letter, PointCredit) yang bersih dan polimorfik di dalam lingkup `School/`.
2. Menerapkan mekanisme **Sub-Modul Registry & Feature Toggle** (App Store Ready) dengan dependensi, lisensi, dan proteksi route guard granular.
3. Menata ulang struktur frontend `frontend/src/modules/School/` mengikuti SoC baku: direktori `types/<domain>.ts`, sentralisasi rute API `services/paths.ts`, penamaan store `use*Store.ts` dengan parser standar (`parseResponse()`, `ensureArray()`, `logger`), dekomposisi view ke sub-folder domain (`views/<domain>/`), dan komponen domain yang reusable.
4. Mengadopsi dan mem-fork fitur unggulan lintas-stack dari 4 repositori referensi (`cijulang`, `ja-siska`, `ja-evoting`, `ja-sis`) ke arsitektur native Vue 3 SFC + Laravel 13 Nwidart tanpa duplikasi model data.
5. Menunda implementasi engine penjadwalan (*scheduling*) hingga integrasi terpadu bersama modul LMS & CBT KataMPI berikutnya.

---

## 2. Audit (Current State)

- **Frontend Types**: Belum ada folder `types/`. Seluruh interface bertumpuk di `services/types.ts` dan `services/schoolServiceTypes.ts`.
- **Frontend Paths & Client**: URL endpoint di-hardcode di tiap service class, belum ada registri `services/paths.ts`.
- **Frontend Views**: 17 file antarmuka masih bertumpuk datar (*flat*) langsung di root `views/*.vue` (misal `StudentList.vue`, `DailyAttendance.vue`, `OsisVoting.vue`), hanya sebagian kecil di `views/admin/students/`.
- **Store Conventions**: Store dinamai tanpa prefiks `use` (`academicStore.ts`, `attendanceStore.ts`, dll.) dan belum menggunakan `parseResponse()`, `ensureArray()`, atau `logger`.
- **Sub-Modul Registry**: Hanya ada `extensionSlug: 'school'` biner di `module.ts`. Sub-fitur belum memiliki slug deklaratif, pemeriksaan dependensi, atau proteksi route guard sub-fitur.
- **Cross-Stack Features**: Fitur unik seperti bilik suara kiosk (`ja-evoting` React/TSX) dan rekap presensi bulanan visual (`ja-sis` Vue 2) belum di-fork ke Vue 3 SFC.
- **Area / File Terdampak**:
  - `frontend/src/modules/School/types/*` (baru)
  - `frontend/src/modules/School/services/*` (paths.ts baru, refactor service)
  - `frontend/src/modules/School/stores/*` (rename ke use*Store.ts & standarisasi)
  - `frontend/src/modules/School/views/*` (reorganisasi ke sub-folder domain)
  - `frontend/src/modules/School/components/*` (adopsi & fork komponen baru)
  - `frontend/src/modules/School/module.ts`, `router.ts`, `navigation.ts`

---

## 3. Diskusi / Keputusan

- **Penempatan Engine Attendance & Voting**: Diputuskan tetap berada di dalam `School/` dengan modularitas dan enkapsulasi bersih, memudahkan ekstraksi di masa depan jika platform lain membutuhkan.
- **Engine Penjadwalan (Scheduling)**: Ditunda (*deferred*) karena akan dirancang terpadu dengan modul LMS dan CBT KataMPI.
- **Engine Persuratan & Dokumen**: Dijadikan kapabilitas terpusat lintas-bidang dengan format penomoran otomatis dan verifikasi QR hash.
- **Feature Toggle granular**: Setiap sub-modul memiliki slug tersendiri (`school.academic`, `school.attendance`, `school.osis`, `school.extracurricular`, dll.) dengan validasi dependensi otomatis.
- **Penyelarasan SoC**: Mengadopsi modul `Publishing` sebagai cetak biru (*gold standard*).
- **ADR Terkait**: [ADR-030](../adr/ADR-030-school-module-scaffold-and-subdomain-architecture.md), [ADR-031](../adr/ADR-031-school-module-soc-sot-and-reference-alignment.md), [ADR-033](../adr/ADR-033-school-engine-capabilities-submodule-registry-and-soc-standardization.md).

---

## 4. Plan

### In Scope
- [ ] Pembentukan direktori `types/<domain>.ts` sebagai Single Source of Truth antarmuka TypeScript.
- [ ] Pembuatan registri rute API terpusat `services/paths.ts` (`schoolPaths`).
- [ ] Standarisasi Pinia stores ke konvensi `use<Domain>Store.ts` dengan `parseResponse()`, `ensureArray()`, dan `logger`.
- [ ] Reorganisasi seluruh berkas view ke dalam folder domain semantik (`academic/`, `students/`, `staff/`, `attendance/`, `grades/`, `discipline/`, `operations/`, `osis/`, `extracurricular/`, `graduation/`, `admission/`, `integrations/`, `public/`).
- [ ] Pembaharuan `router.ts` dengan metadata `meta.extension` dan `meta.subFeature`.
- [ ] Pembaruan `navigation.ts` dan `module.ts` dengan deklarasi sub-modul dan filter reaktif.
- [ ] Adopsi komponen form Dapodik 5-tab `StudentForm.vue` dan `StaffForm.vue` dari `cijulang`.
- [ ] Fork komponen e-voting (`KioskTokenModal.vue`, `QrScanner.vue`, `VoteResultChart.vue`, `ElectionResults.vue`) dari `ja-evoting` (React) ke Vue 3 SFC.
- [ ] Fork komponen rekap presensi visual dan alumni dari `ja-sis` ke Vue 3 SFC.
- [ ] Porting manajemen ekstrakurikuler dari logika `ja-siska`.
- [ ] Pembersihan seluruh komentar institusi lama (Pristine Code Mandate).

### Out of Scope
- Scheduling Engine (ditunda untuk integrasi LMS & CBT).
- Boarding School / Hostel Management (tidak dibutuhkan target pengguna).

### Langkah Implementasi

1. **Fase 1: Fondasi Arsitektur**
   - Buat `types/` (`common.ts`, `academic.ts`, `student.ts`, `staff.ts`, `attendance.ts`, `discipline.ts`, `operations.ts`, `osis.ts`, `extracurricular.ts`, `graduation.ts`, `admission.ts`, `index.ts`).
   - Buat `services/paths.ts` dan hubungkan ke service layer.
   - Siapkan sub-modul registry manifest di `module.ts`.
   - *Gate*: `npx vue-tsc -b` lulus 0 error.
2. **Fase 2: Standarisasi Store & Service Layer**
   - Rename store ke `use*Store.ts`.
   - Terapkan `parseResponse()`, `ensureArray()`, dan `@/shared/utils/logger`.
   - Update seluruh import store di views & components.
   - *Gate*: `npx vue-tsc -b` lulus 0 error.
3. **Fase 3: Reorganisasi Views & Router Guard Sub-Feature**
   - Pindahkan views flat ke sub-direktori domain.
   - Update import di `router.ts` dengan `meta.extension` dan `meta.subFeature`.
   - Update navigasi dinamis di `navigation.ts`.
   - *Gate*: `npx vue-tsc -b` lulus 0 error.
4. **Fase 4: Adopsi Komponen Kunci (cijulang)**
   - Pasang `StudentForm.vue`, `StaffForm.vue`, `SalaryStructureDialog.vue`.
   - Pasang view portal guru dan siswa.
   - *Gate*: `npx vue-tsc -b` lulus 0 error.
5. **Fase 5: Fork Mesin E-Voting (ja-evoting → Vue 3)**
   - Implementasi `KioskTokenModal.vue`, `QrScanner.vue`, `VoteResultChart.vue`, `VoterCardPreview.vue`.
   - Implementasi `views/osis/ElectionResults.vue` dan `views/public/OfflineVoting.vue`.
   - *Gate*: `npx vue-tsc -b` lulus 0 error.
6. **Fase 6: Fork UX Rekap Presensi & Portal Alumni (ja-sis → Vue 3)**
   - Implementasi `AttendanceChart.vue`, `ClassRecap.vue`, `StudentRecap.vue`.
   - Implementasi `views/graduation/Alumni.vue` dan `GraduationYearTable.vue`.
   - *Gate*: `npx vue-tsc -b` lulus 0 error.
7. **Fase 7: Ekstrakurikuler & Polish Akhir**
   - Implementasi `views/extracurricular/Index.vue`.
   - Pembersihan komentar institusi lama (*zero dead code*).
   - Update dokumentasi kontrak L1 (`README.md`, `CHANGELOG.md`).
   - *Gate Akhir*: `npx vue-tsc -b` 0 error & test backend pas.

### Acceptance Criteria
- [ ] Seluruh antarmuka TypeScript terpusat di `types/` tanpa error kompilasi.
- [ ] Tidak ada URL endpoint yang di-hardcode di service; seluruhnya merujuk ke `services/paths.ts`.
- [ ] Seluruh store menggunakan konvensi `use*Store.ts` dan helper parsing standar.
- [ ] Direktori `views/` terorganisir per sub-folder domain semantik, tidak ada berkas flat di root `views/`.
- [ ] Seluruh route memiliki `meta.extension` dan `meta.subFeature`.
- [ ] Fitur bilik suara e-voting dan rekap presensi berfungsi native dalam Vue 3.
- [ ] Zero duplikasi model siswa; seluruh data terikat pada satu sumber data.
- [ ] `npx vue-tsc -b` lulus dengan exit code 0.

---

## 5. Log Proses

| Waktu | Agen/sesi | Catatan |
| :--- | :--- | :--- |
| 2026-09-21 | Antigravity | Audit komprehensif 4 repo referensi (`cijulang`, `ja-siska`, `ja-evoting`, `ja-sis`). |
| 2026-09-21 | Antigravity | Diskusi arsitektur: Kategorisasi Engine vs Operasional, Sub-Modul Registry, Penundaan Scheduling. |
| 2026-09-21 | Antigravity | Penerbitan ADR-033 dan penyusunan Task Brief rencana kerja refactoring. |

---

## 6. Hasil

*(Akan diisi setelah implementasi selesai dan diverifikasi)*
