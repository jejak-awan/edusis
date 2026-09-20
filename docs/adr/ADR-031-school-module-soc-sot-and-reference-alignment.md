---
status: accepted
scope: edusis:school
date: 2026-09-20
supersedes: []
---

# ADR-031: Standarisasi Pemrograman, Separation of Concerns (SoC), Single Source of Truth (SoT), dan Penyelarasan Aset `repo_referensi`

- **Status:** Accepted
- **Tanggal:** 2026-09-20
- **Scope:** edusis:school
- **Deciders:** jejakawan team

---

## 1. Konteks

Setelah selesainya fase awal scaffolding dan pengujian 5 milestone inti beserta engine e-voting, dilakukan audit menyeluruh terhadap arsitektur dan kelengkapan berkas pada modul sekolah (`Modules/School`).

### Temuan Audit & Kesenjangan Struktural:
1. **Banyak Folder Kosong Pasca-Scaffold (*Orphan Empty Folders*)**:
   - Pada frontend `frontend/src/modules/School/views/`, ditemukan sejumlah direktori sub-domain yang masih kosong melompong:
     - `admin/academic/`, `admin/attendance/`, `admin/counseling/`, `admin/dashboard/`, `admin/discipline/`, `grades/`, `students/`.
     - `teacher/` (kosong), `student/` (kosong), `public/` (kosong).
     - Folder `components/` belum dibentuk.
   - Hal ini disebabkan karena pada tahap awal, tampilan antarmuka diimplementasikan sebagai berkas flat tunggal langsung di root `views/*.vue` (misal: `StudentList.vue`, `DailyAttendance.vue`, `OsisVoting.vue`), sementara struktur folder multi-portal yang sudah disiapkan belum diisi.
2. **Ketiadaan Layer FormRequest & Service di Backend**:
   - Seluruh Controller di `backend/Modules/School/app/Http/Controllers/Api/` bertindak sebagai *Fat Controllers*: memuat aturan validasi inline `$request->validate()`, logika query database, formula matematika leger raport, DPT token generator, dan kripto HMAC SSO.
   - Folder `app/Http/Requests/` dan `app/Services/` masih belum ada (0 berkas), melanggar standar arsitektur di [`docs/architecture/02-backend-standards.md`](../architecture/02-backend-standards.md).
3. **Monolithic Service & Types di Frontend**:
   - Seluruh interface TypeScript dan 50+ method Axios ditumpuk ke dalam satu berkas raksasa [`schoolService.ts`](../../frontend/src/modules/School/services/schoolService.ts) (>1.000 baris) tanpa pemisahan sub-domain.
4. **Kekayaan Berkas di `repo_referensi` yang Belum Diadopsi (*Unleveraged Reference Assets*)**:
   - Di direktori `/home/jejakawan/dev/repo_referensi/` sebenarnya telah tersedia aset yang matang dan siap pakai:
     - **`cijulang/backend/Modules/School/`**: Memiliki layer `Requests/` lengkap (Academic, Student dengan aturan Dapodik 80 baris, Operations, HR, Admission, Logistics) dan layer `Services/` lengkap (AcademicService 312 baris, StudentService, HRService, OperationsService, dll.).
     - **`ja-siska/backend/app/Services/`**: Memiliki service *battle-tested* untuk kredit poin akumulatif (`KreditPoinService.php`), penanganan konseling privat (`BKService.php`), dan presensi (`PresensiService.php`).
     - **`cijulang/frontend/src/modules/School/`**: Memiliki modular services (`AcademicService.ts`, `StudentService.ts`, dll.), Pinia stores (`academic.ts`, `student.ts`, `hr.ts`, dll.), serta sub-views admin/teacher/student beserta komponennya (misal `StudentForm.vue` 14 KB).

Membangun kembali hal-hal tersebut dari nol (*reinventing the wheel*) berisiko tinggi menimbulkan halusinasi kode, redudansi, duplikasi kerja, dan menurunkan persentase kelengkapan modul.

---

## 2. Keputusan Arsitektur

Ditetapkan keputusan strategis untuk melakukan **"Adopt, Align & Refactor"** dari `repo_referensi` guna mewujudkan 100% kepatuhan terhadap SoC, SoT, dan standar pemrograman Jejakawan:

### A. Strategi Penyelarasan (*Alignment Strategy*) dari `repo_referensi`
1. **Tidak Menulis Ulang Validasi & Bisnis Logik yang Sudah Matang**:
   - Mengadopsi kelas `FormRequest` dari `cijulang/backend/Modules/School/app/Http/Requests/` ke dalam `ja-edusis`.
   - Mengadopsi kelas `Services` dari `cijulang` dan `ja-siska` (`KreditPoinService`, `BKService`, `PresensiService`).
   - Melakukan *alignment*: menyesuaikan namespace ke `Modules\School\...` dan menyelaraskan nama tabel ke skema PostgreSQL `sch_*`.
2. **Mengisi Folder Kosong di Frontend**:
   - Memindahkan struktur domain views dan sub-komponen dari `cijulang/frontend/src/modules/School/` untuk mengisi folder `views/admin/`, `views/teacher/`, `views/student/`, dan `components/`.

### B. Penegakan Separation of Concerns (SoC)
1. **Backend**:
   - **Thin Controllers**: Controller hanya bertugas memvalidasi autorisasi via Policy/Middleware, memanggil Service, dan mengembalikan format respons JSON standar. Dilarang keras menaruh query berat atau validasi inline di controller.
   - **FormRequest Layer**: Setiap mutasi `POST/PUT/PATCH/DELETE` wajib menggunakan FormRequest class di `app/Http/Requests/<SubDomain>/`.
   - **Pure Services Layer**: Seluruh logika domain (kalkulasi nilai, agregasi absensi, token bilik suara, SSO crypto) diisolasi di `app/Services/<SubDomain>/`.
   - **Configuration (`config/school.php`)**: Menyimpan seluruh konstanta konfigurasi sekolah.
2. **Frontend**:
   - **Domain Types (`types/<subdomain>.ts`)**: Memecah interface dari `schoolService.ts` ke tipe data terisolasi per domain.
   - **Domain Services (`services/<subdomain>Service.ts`)**: Memecah client HTTP per sub-domain dengan mempertahankan facade `schoolService.ts` untuk backward-compatibility.
   - **Pinia Domain Stores (`stores/use*Store.ts`)**: Mengenkapsulasi state reaktif per sub-domain.
   - **Component Decomposition (`components/<subdomain>/`)**: Memisahkan form modal, tabel kompleks, dan kartu visual dari berkas view utama.

### C. Penegakan Single Source of Truth (SoT) Dokumentasi L1
Sesuai kontrak [`docs/DOCUMENTATION.md`](../DOCUMENTATION.md) Bab 2:
1. Menerbitkan kontrak modul backend: `backend/Modules/School/README.md` dan `backend/Modules/School/CHANGELOG.md`.
2. Menerbitkan kontrak modul frontend: `frontend/src/modules/School/README.md` dan `frontend/src/modules/School/CHANGELOG.md`.

---

## 3. Konsekuensi & Batasan

### Positif:
- **Efisiensi Maksimal**: Menghemat waktu pengerjaan dengan memanfaatkan ratusan baris kode validasi Dapodik dan kalkulasi yang sudah teruji di `repo_referensi`.
- **Zero Empty Folders**: Menghapus seluruh folder kosong pasca-scaffold dan menatanya ke dalam hierarki modular yang utuh.
- **Kepatuhan Enterprise**: Memenuhi standar arsitektur `ja-core_engine` (Thin Controller, FormRequest, Services, Pinia, dan L1 Docs).

### Batasan Mutlak:
- **Zero Regression**: Proses penyelarasan dan refactoring **wajib mempertahankan kelulusan 100% dari 47 feature test yang ada** (`php artisan test Modules/School`).
- **Data Integrity**: Tidak mengubah struktur tabel migrasi `sch_*` yang sudah termigrasi rapi di PostgreSQL.

---

## 4. Referensi
- [Repositori Referensi Cijulang `repo_referensi/cijulang/`](file:///home/jejakawan/dev/repo_referensi/cijulang/)
- [Repositori Referensi JA-SISKA `repo_referensi/ja-siska/`](file:///home/jejakawan/dev/repo_referensi/ja-siska/)
- [Repositori Referensi JA-SIS `repo_referensi/ja-sis/`](file:///home/jejakawan/dev/repo_referensi/ja-sis/)
- [Kebijakan Dokumentasi & SoT Layer `docs/DOCUMENTATION.md`](../DOCUMENTATION.md)
- [Standar Backend Laravel `docs/architecture/02-backend-standards.md`](../architecture/02-backend-standards.md)
- [Standar Frontend Vue `docs/architecture/03-frontend-standards.md`](../architecture/03-frontend-standards.md)
- [Cetak Biru Scaffolding `docs/sis/06-scaffold-and-architecture-blueprint.md`](../sis/06-scaffold-and-architecture-blueprint.md)
