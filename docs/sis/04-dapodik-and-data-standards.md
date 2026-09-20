# Standar Data & Kepatuhan Dapodik di JA-EduSIS

Dokumen ini mendefinisikan konvensi penamaan tabel basis data, struktur skema, dan standar data Dapodik Kemendikbudristek yang diterapkan di dalam modul `Modules/School`.

---

## 1. Konvensi Tabel: Isolasi Skema & Prefix `sch_*`

Semua tabel untuk modul sekolah diisolasi di dalam skema PostgreSQL `edusis` dan menggunakan prefix sub-domain yang konsisten:

| Prefix Tabel | Sub-Domain | Contoh Tabel |
|---|---|---|
| `sch_acad_*` | Akademik & Kurikulum | `sch_acad_academic_years`, `sch_acad_semesters`, `sch_acad_departments`, `sch_acad_study_groups`, `sch_acad_subjects`, `sch_acad_schedules`, `sch_acad_attendances`, `sch_acad_teaching_journals`, `sch_acad_grades`, `sch_acad_class_histories` |
| `sch_std_*` | Kesiswaan & Disiplin | `sch_std_students`, `sch_std_violations`, `sch_std_achievements`, `sch_std_counseling_records`, `sch_std_credit_points`, `sch_std_point_categories`, `sch_std_alumni`, `sch_std_tracer_studies` |
| `sch_hr_*` | Kepegawaian (Guru/Tendik) | `sch_hr_staff`, `sch_hr_staff_attendances`, `sch_hr_staff_shifts`, `sch_hr_leave_requests`, `sch_hr_salary_structures`, `sch_hr_payrolls` |
| `sch_ops_*` | Operasional & Tata Usaha | `sch_ops_surat_masuk`, `sch_ops_surat_keluar`, `sch_ops_template_surat`, `sch_ops_disposisi`, `sch_ops_visitors`, `sch_ops_guest_logs`, `sch_ops_graduation_results` |
| `sch_adm_*` | PPDB / Penerimaan Baru | `sch_adm_enrollments`, `sch_adm_enrollment_documents`, `sch_adm_document_verifications` |
| `sch_fac_*` | Fasilitas & Sarpras | `sch_fac_buildings`, `sch_fac_rooms`, `sch_fac_assets`, `sch_fac_inventory_categories`, `sch_fac_inventory_items`, `sch_fac_maintenance_tickets` |
| `sch_osis_*` | Organisasi Siswa & Ekskul | `sch_osis_members`, `sch_osis_programs`, `sch_osis_finances`, `sch_osis_evoting_candidates`, `sch_osis_evoting_ballots`, `sch_osis_extracurriculars`, `sch_osis_extra_members` |

---

## 2. Kepatuhan Standar Dapodik untuk Entitas Siswa (`Student`)

Tabel `sch_std_students` mengadopsi field wajib sinkronisasi Dapodik:

- **Identitas Utama**:
  - `nisn`: Nomor Induk Siswa Nasional (10 digit angka unik).
  - `nis`: Nomor Induk Siswa lokal sekolah.
  - `nik`: Nomor Induk Kependudukan (16 digit angka dari KK).
  - `full_name`: Nama lengkap sesuai akta kelahiran.
  - `gender`: Jenis kelamin (`L` / `P`).
  - `place_of_birth` & `date_of_birth`: Tempat dan tanggal lahir.
  - `religion`: Agama (Islam, Kristen Protestan, Katolik, Hindu, Buddha, Khonghucu).
- **Alamat Tempat Tinggal**:
  - `address`, `rt`, `rw`, `dusun`, `desa_kelurahan`, `kecamatan`, `kode_pos`.
  - `phone` & `email`: Kontak siswa.
- **Data Periodik Orang Tua / Wali**:
  - Ayah: `father_name`, `father_nik`, `father_birth_year`, `father_education`, `father_occupation`, `father_income`.
  - Ibu: `mother_name`, `mother_nik`, `mother_birth_year`, `mother_education`, `mother_occupation`, `mother_income`.
  - Wali (opsional): `guardian_name`, `guardian_nik`, `guardian_education`, `guardian_occupation`, `guardian_income`.
- **Status Siswa**:
  - `status`: `active`, `graduated`, `transferred` (mutasi keluar), `dropped_out`.
  - `user_id`: Relasi ke akun portal siswa/wali di IAM `srv_auth_users`.
