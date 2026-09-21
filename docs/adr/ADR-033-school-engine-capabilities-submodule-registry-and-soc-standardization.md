---
status: accepted
scope: edusis:school
date: 2026-09-21
supersedes: []
---

# ADR-033: Arsitektur Kapabilitas Engine Sekolah, Sub-Modul Registry (App Store Aligned), dan Standarisasi SoC Frontend

- **Status:** Accepted
- **Tanggal:** 2026-09-21
- **Scope:** edusis:school
- **Deciders:** jejakawan team

---

## 1. Konteks

Melanjutkan fondasi awal modul sekolah pada [ADR-030](./ADR-030-school-module-scaffold-and-subdomain-architecture.md) dan [ADR-031](./ADR-031-school-module-soc-sot-and-reference-alignment.md), evaluasi mendalam terhadap 4 repositori referensi (`cijulang`, `ja-siska`, `ja-evoting`, `ja-sis`) mengungkap kebutuhan penyelarasan tingkat lanjut:

1. **Ambiguitas Batas Engine vs Operasional**:
   Beberapa fitur esensial seperti Presensi (*Attendance*), Pemilihan Suara (*E-Voting*), dan Persuratan/Penomoran Dokumen (*Document Engine*) selama ini diperlakukan sebagai fitur operasional kesiswaan yang terisolasi. Padahal, kapabilitas tersebut bersifat *cross-cutting* (dibutuhkan oleh GTK, ekstrakurikuler, agenda OSIS, tamu, dan mutasi).
2. **Ketiadaan Mekanisme Feature Toggle Per Sub-Modul**:
   Modul `School` saat ini diperlakukan sebagai satu monolit biner (`extensionSlug: 'school'`). Tidak semua instansi sekolah membutuhkan seluruh fitur sekaligus (misal: sekolah dasar belum tentu membutuhkan e-voting OSIS atau bursa kerja alumni). Dibutuhkan mekanisme granular di mana sub-modul dapat di-enable/disable layaknya *App Store / Extensions* pada platform Core `ja-edusis`, lengkap dengan pemetaan lisensi dan resolusi dependensi.
3. **Inkonsistensi Struktur Frontend terhadap Standar Baku (Publishing Standard)**:
   Audit frontend pada `frontend/src/modules/School/` menunjukkan defisit kepatuhan terhadap modul rujukan yang sudah stabil (`Publishing`):
   - Seluruh *interface* TypeScript masih bertumpuk di dalam `services/types.ts` (tidak ada folder `types/`).
   - Berkas *views* masih diletakkan datar (*flat*) di root `views/*.vue` dan baru sebagian kecil di `views/admin/students/`.
   - Endpoint API di-hardcode di dalam service alih-alih melalui registri terpusat `paths.ts`.
   - Pola store masih menggunakan nama tanpa prefiks standar (`academicStore.ts` alih-alih `useAcademicStore.ts`) serta belum menggunakan `parseResponse()`, `ensureArray()`, dan logger terintegrasi.
4. **Potensi Duplikasi Data & Fragmentasi Lintas-Stack**:
   Repo referensi memiliki variasi stack teknologi: `cijulang` (Vue 3 + TS), `ja-siska` (Vanilla JS), `ja-evoting` (React / Inertia TSX), dan `ja-sis` (Vue 2). Tanpa arsitektur fork yang tegas, adopsi parsial berisiko menduplikasi model data siswa dan menghasilkan *spaghetti code*.

---

## 2. Keputusan Arsitektur

Ditetapkan keputusan arsitektur strategis sebagai pedoman baku pengembangan:

### 2.1 Taksonomi Dua Lapis: Capability Engines vs Modul Operasional

Modul sekolah distrukturkan menjadi dua lapisan di dalam namespace `School`:

1. **Sharable Capability Engines (Mesin Terenkapsulasi & Polimorfik)**:
   - **Attendance Engine (`attendance`)**: Mesin pencatat presensi multi-domain yang menangani sesi harian siswa, absensi mengajar guru/GTK, kehadiran ekstrakurikuler, dan log tamu secara polimorfik.
   - **Voting & Election Engine (`osis/voting`)**: Mesin pemilihan suara (fork dari `ja-evoting`) dengan fitur bilik suara (*kiosk mode*), token 8-karakter sekali pakai, verifikasi HMAC anti-fraud, dan rekapitulasi suara real-time.
   - **Document & Letter Engine (`operations/documents`)**: Mesin generator surat dinas dan dokumen resmi dengan format penomoran otomatis dinamis (misal: `001/OSIS/IX/2026`, `.../EKSKUL/...`), QR verification hash, dan render PDF berbasis `mPDF`.
   - **Point & Credit Engine (`discipline/points`)**: Mesin tata tertib (fork dari `ja-siska`) untuk akumulasi skor pelanggaran/penghargaan, kalkulasi ambang batas sanksi (SP1, SP2, SP3), dan eskalasi otomatis ke Bimbingan Konseling (BK).
2. **Operational Sub-Modules (Domain Bisnis Sekolah)**:
   - `Academic` (Kurikulum Merdeka, Mapel, Rombel, Jurnal Mengajar, Raport)
   - `Students` (Siklus hidup siswa berbasis Dapodik)
   - `Staff` (GTK, Kepegawaian, Struktur Gaji, Shift)
   - `Discipline` (Konseling privat BK, rekap kasus)
   - `Extracurricular` (Ekskul, kegiatan, penilaian karakter P3)
   - `Graduation` (Kelulusan, alumni portal, penelusuran tamatan)
   - `Admission` (PPDB Online, seleksi pendaftar)
   - `Integrations` (WhatsApp Gateway notifikasi ortu & SSO KataMPI)
3. **Penundaan Scheduling Engine**:
   Engine penjadwalan (*scheduling*) ditunda sementara hingga fase implementasi terpadu bersama modul **LMS** dan **CBT KataMPI** berikutnya.

### 2.2 Sub-Modul Registry & Feature Toggle (App Store Aligned)

Mengadopsi pola ekstensi Core `ja-edusis` untuk sub-fitur di dalam modul `School`:

1. **Identifikasi Slug & Dependensi**:
   Setiap sub-modul memiliki slug deklaratif (`school.core`, `school.students`, `school.staff`, `school.academic`, `school.attendance`, `school.discipline`, `school.operations`, `school.osis`, `school.extracurricular`, `school.graduation`, `school.admission`).
2. **Matriks Dependensi Otomatis**:
   Sub-modul turunan memvalidasi ketersediaan modul induk (contoh: `school.extracurricular` mewajibkan `school.students` dan `school.attendance`). Jika dependensi belum aktif, sub-fitur dicegah untuk aktif.
3. **Route Guard & Navigation Filtering**:
   Setiap rute di `router.ts` dilindungi oleh `meta.extension: 'school'` dan `meta.subFeature: '<slug>'`. Menu navigasi di `navigation.ts` dan widget dashboard di `components/dashboard/` hanya dirender bila sub-fitur terkait berstatus aktif pada tenant/instansi.

### 2.3 Standarisasi Separation of Concerns (SoC) Frontend

Mengikuti standar baku modul `Publishing`:

1. **`types/<domain>.ts`**: Memisahkan antarmuka per domain sebagai Single Source of Truth (SoT). File lama `services/types.ts` dan `services/schoolServiceTypes.ts` didekomisi.
2. **`services/paths.ts`**: Seluruh URI endpoint API didefinisikan secara deklaratif di `schoolPaths`.
3. **`stores/use<Domain>Store.ts`**: Seluruh Pinia store menggunakan konvensi penamaan `use*Store`, memanfaatkan `parseResponse()`, `ensureArray()`, dan logger terintegrasi `@/shared/utils/logger`.
4. **`views/<domain>/`**: Seluruh berkas antarmuka dikelompokkan ke dalam direktori domain semantik. Menghilangkan seluruh berkas *flat* di root folder `views/`.
5. **`components/<domain>/`**: Seluruh modal, dialog, tabel detail, dan scanner dipisahkan menjadi komponen yang dapat digunakan ulang.

### 2.4 Strategi Fork Lintas-Stack Tanpa Duplikasi

1. **ja-evoting (React/TSX → Vue 3 SFC)**:
   Bilik suara kiosk, modal token, preview kartu pemilih, visualisasi grafik suara (Chart.js), dan QR scanner kamera di-fork dan diimplementasikan ulang secara native ke Vue 3 SFC dan TailwindCSS.
2. **ja-sis (Vue 2 → Vue 3 SFC)**:
   Logika rekapitulasi presensi bulanan/siswa dan portal alumni diadaptasi ke Vue 3 Composition API.
3. **ja-siska (Laravel Service & Logika Dapodik)**:
   Logika bisnis `KreditPoinService`, `BKService`, dan `ImportDataService` diintegrasikan ke backend `Modules/School/app/Services/` dengan API PhpSpreadsheet modern dan skema database `sch_*`.
4. **Zero Data Redundancy**:
   Semua fitur merujuk pada model tunggal `Student` (`sch_students`). Tidak ada duplikasi entitas peserta didik pada voting, ekskul, maupun alumni.

---

## 3. Konsekuensi & Mitigasi Risiko

### Konsekuensi Positif:
- **Modularitas Tinggi**: Sekolah dapat menyesuaikan paket fitur sesuai kapasitas operasional dan tier lisensi instansi.
- **Konsistensi Arsitektural**: Struktur kode FE dan BE 100% selaras dengan standar Core `ja-edusis`.
- **Ekstensibilitas**: Engine yang bersih memudahkan ekstraksi menjadi modul independen level-atas di masa mendatang jika dibutuhkan platform lain.
- **Integritas Data & Bebas Duplikasi**: Struktur relasi terpusat menjamin keutuhan data Dapodik dan riwayat siswa dari pendaftaran hingga alumni.

### Risiko Teknis & Mitigasi:
1. **Kompatibilitas PhpSpreadsheet API**:
   Parser Dapodik dari ja-siska menggunakan API lama; diadaptasi langsung ke standar PhpSpreadsheet modern di Laravel 13.
2. **Driver PDF Terpadu**:
   Generator kartu pemilih e-voting dan surat dinas diseragamkan menggunakan driver `mPDF` terpasang, menggantikan dependensi `dompdf` lawas.
3. **Pristine Code Mandate**:
   Seluruh referensi institusi lama ("SMKN 1 Cijulang", author repositori asal) wajib dibersihkan secara mutlak.
