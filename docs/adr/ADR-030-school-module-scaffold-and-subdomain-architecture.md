# ADR-030: Struktur Scaffolding dan Sub-Domain Modul School

- **Status:** Accepted
- **Tanggal:** 2026-09-20
- **Scope:** edusis:school
- **Deciders:** jejakawan team

---

## 1. Konteks

Sebagai tindak lanjut dari [ADR-029](ADR-029-edusis-domain-and-multi-repo-synthesis.md), JA-EduSIS membutuhkan struktur pengorganisasian kode yang kokoh, modular, dan terukur untuk modul inti sekolah (`Modules/School`). Modul ini harus mampu menampung kompleksitas seluruh operasional sekolah (Akademik, Kesiswaan, Kepegawaian, Sarpras, Persuratan, PPDB, dan OSIS) tanpa menjadi monolit kusut (*spaghetti code*).

---

## 2. Keputusan

1. **Struktur Modul Nwidart Tunggal dengan Partisi Sub-Domain**:
   - Seluruh fungsionalitas sekolah dikemas di dalam satu modul besar: `backend/Modules/School` dan `frontend/src/modules/School`.
   - Di dalam modul, kode dipartisi secara ketat ke dalam 7 sub-domain:
     - `Academic`: Kurikulum, Tahun Ajaran, Rombel, Mapel, Jadwal, Nilai, Jurnal KBM.
     - `Student`: Data Siswa Dapodik, Poin Kredit Disiplin, Kasus BK, Alumni.
     - `HR`: Data Guru/Tendik, Cuti, Presensi Pegawai, Payroll.
     - `Operations`: Persuratan TU (Surat Masuk/Keluar/Disposisi), Buku Tamu.
     - `Admission`: PPDB Online dan verifikasi dokumen.
     - `Logistics`: Fasilitas, inventaris ruangan, tiket perbaikan sarpras.
     - `Osis`: Kepengurusan, program kerja, e-voting pemilu ketua OSIS, ekstrakurikuler.

2. **Prefix Tabel `sch_*` dalam Skema PostgreSQL `edusis`**:
   - Seluruh tabel database menggunakan prefix formal `sch_<subdomain>_*`.
   - Menggunakan soft deletes untuk data kritis (siswa, guru, nilai, surat).
   - Foreign key integritas referensial antar entitas sekolah terjaga di level basis data.

3. **Frontend Role-Based Multi-Portal**:
   - Satu SPA modul (`src/modules/School`) melayani berbagai peran pengguna melalui routing dinamis:
     - `/manage/school/admin/*` untuk Admin Sekolah & Staf TU.
     - `/manage/school/teacher/*` untuk Guru (input nilai, absensi, jurnal KBM).
     - `/manage/school/student/*` untuk Siswa & Orang Tua.
     - `/admission/*` untuk portal publik PPDB tanpa autentikasi admin.

4. **Integrasi Capability Registry & Spatie Permissions**:
   - Modul `School` mendaftarkan permission granular (`manage academic`, `manage students`, `manage confidential counseling`, dll.) melalui `manifest.json` yang secara otomatis disinkronkan ke tabel `srv_auth_permissions` Core Engine.

---

## 3. Konsekuensi

### Positif:
- **Keteraturan Arsitektur**: Pengembang dan agen AI memiliki peta lokasi berkas yang seragam dan mudah dipahami.
- **Isolasi Domain**: Perubahan pada sub-domain Sarpras tidak akan mempengaruhi sub-domain Akademik atau Nilai.
- **User Experience Optimal**: Guru dan Siswa mendapatkan antarmuka yang bersih, fokus, dan tidak dibebani menu administratif yang tidak relevan.

### Negatif:
- Modul `School` memiliki jumlah model dan tabel yang cukup banyak (sekitar 30+ tabel), sehingga pengembangannya harus dieksekusi secara bertahap (*phased rollout*).

---

## 4. Referensi
- [Cetak Biru Scaffolding `docs/sis/06-scaffold-and-architecture-blueprint.md`](../sis/06-scaffold-and-architecture-blueprint.md)
- [ADR-029: Arsitektur Domain JA-EduSIS](ADR-029-edusis-domain-and-multi-repo-synthesis.md)
- [Standar Data & Dapodik `docs/sis/04-dapodik-and-data-standards.md`](../sis/04-dapodik-and-data-standards.md)
