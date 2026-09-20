# ADR-029: Arsitektur Domain JA-EduSIS dan Sintesis Multi-Repositori

- **Status:** Accepted
- **Tanggal:** 2026-09-20
- **Scope:** edusis:core
- **Deciders:** jejakawan team

---

## 1. Konteks

`ja-edusis` dibangun sebagai produk turunan (*downstream product*) dari `ja-core_engine`. Berbeda dengan produk CMS generik atau portal marketing, EduSIS ditujukan secara spesifik sebagai **Sistem Informasi Sekolah (SIS)** terpadu untuk kebutuhan institusi pendidikan di Indonesia.

Untuk mempercepat pengembangan dan menjaga standar mutu terbaik, dilakukan kajian mendalam terhadap 3 repositori referensi:
1. `cijulang` (`k2netid/cijulang`): SMKN 1 Cijulang platform.
2. `ja-siska` (`jejak-awan/ja-siska`): Sistem Kesiswaan & Kedisiplinan.
3. `ja-sis` (`jejak-awan/ja-sis`): Sistem Informasi Sekolah Terpadu.

Tantangan utama adalah menyatukan keunggulan domain dari ketiga repositori tersebut tanpa merusak fondasi keamanan `ja-core_engine`, serta menentukan batas pemisahan antara sistem administratif sekolah dan sistem ujian/evaluasi online (CBT/LMS).

---

## 2. Keputusan

1. **Hard Isolation Repo & Selective Patching**:
   - `ja-edusis` berdiri sebagai repositori mandiri.
   - Sinkronisasi dari `ja-core_engine` **TIDAK** menggunakan `git merge upstream/main` untuk mencegah regresi konfigurasi dan masuknya kembali modul-modul CMS yang sudah dirampingkan (seperti newsletter atau theme customizer blog).
   - Patch perbaikan keamanan kernel dari `ja-core_engine` diambil secara selektif menggunakan `git cherry-pick`.

2. **Pengecualian LMS & CBT dari Codebase Internal**:
   - Fitur e-learning interaktif (LMS) dan ujian berbasis komputer (CBT) **dikecualikan** dari cakupan internal `ja-edusis`.
   - Seluruh beban CBT dan LMS dialokasikan ke mesin khusus **`ja-katampi`** yang berjalan pada kluster terdedikasi (`ja-aksara`).
   - EduSIS hanya menyediakan API sinkronisasi data siswa/kelas dan Single Sign-On (SSO).

3. **Adopsi Struktur `Modules/School` dari Cijulang**:
   - Seluruh fungsionalitas sekolah dikemas di dalam modul `Modules/School` (backend) dan `src/modules/School` (frontend).
   - Tabel basis data diisolasi ke dalam skema PostgreSQL `edusis` dengan prefix `sch_*` (`sch_acad_*`, `sch_std_*`, `sch_hr_*`, `sch_ops_*`, `sch_adm_*`, `sch_fac_*`, `sch_osis_*`).

4. **Sintesis Fitur Kesiswaan & TU**:
   - Sistem poin kredit perilaku, ambang batas SP (Surat Peringatan), dan alur Bimbingan Konseling (BK) diadopsi dari `ja-siska`.
   - Alur persuratan Tata Usaha (Surat Masuk, Surat Keluar, Disposisi) dan E-Voting OSIS diadopsi dari `ja-sis`.

---

## 3. Konsekuensi

### Positif:
- Arsitektur bersih: Core engine menangani IAM, keamanan, dan storage; modul `School` menangani domain pendidikan.
- Performa server terjamin karena beban lonjakan trafik CBT tidak mengganggu layanan administrasi harian sekolah.
- Data siswa patuh terhadap standar sinkronisasi Dapodik Kemendikbudristek.
- Menghilangkan duplikasi fitur dan menjaga dependensi tetap ramping.

### Negatif:
- Patching perbaikan dari upstream `ja-core_engine` harus dilakukan manual per commit melalui cherry-pick.
- Memerlukan implementasi kontrak API integrasi terpisah saat nanti menghubungkan sistem nilai raport dengan KataMPI.

---

## 4. Referensi
- [`docs/sis/01-overview-and-domain-map.md`](../sis/01-overview-and-domain-map.md)
- [`docs/sis/02-multi-repo-synthesis.md`](../sis/02-multi-repo-synthesis.md)
- [`docs/sis/03-integration-katampi-cbt.md`](../sis/03-integration-katampi-cbt.md)
- [`docs/sis/04-dapodik-and-data-standards.md`](../sis/04-dapodik-and-data-standards.md)
