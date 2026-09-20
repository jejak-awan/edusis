# Strategi Integrasi: Pemisahan SIS dan Mesin CBT/LMS (JA-KataMPI)

Dokumen ini menjelaskan batas arsitektur antara **JA-EduSIS** (Sistem Informasi Sekolah) dan **JA-KataMPI** (CBT Engine & E-Learning Platform).

---

## 1. Rasional Pemisahan Beban (Separation of Concerns)

Ujian Berbasis Komputer (CBT) dan Learning Management System (LMS) memiliki karakteristik beban kerja (*workload*) yang sangat kontras dengan Sistem Informasi Sekolah:

| Karakteristik | JA-EduSIS (SIS) | JA-KataMPI (CBT & LMS) |
|---|---|---|
| **Pola Beban Trafik** | Akses harian merata (input nilai, absensi, data siswa, persuratan). | Lonjakan konkurensi ekstrem saat jam ujian serentak (ribuan request per detik). |
| **Karakteristik Komputasi** | Operasi I/O database relasional (CRUD data terstruktur). | Komputasi tinggi, timer websocket/polling real-time, shuffle soal, dan evaluasi instan. |
| **Infrastruktur Target** | Host aplikasi web standar (`ja-dev` / `ja-srv`). | Host terdedikasi CBT (`ja-aksara` / kluster aksara lab). |
| **Ketergantungan State** | Relasional PostgreSQL (ACID konsisten). | In-memory cache intensif (Valkey / Redis) untuk session ujian dan anti-cheat. |

Mencampurkan mesin CBT ke dalam aplikasi SIS akan membahayakan integritas sistem administratif saat ribuan siswa login bersamaan untuk ujian. Oleh karena itu, arsitektur dipisahkan secara tegas.

---

## 2. Titik Integrasi Masa Depan

Komunikasi antara **JA-EduSIS** dan **JA-KataMPI** akan dibangun melalui:

1. **Sinkronisasi Data Peserta & Kelas (SIS -> KataMPI)**:
   - EduSIS bertindak sebagai *Single Source of Truth* (SSOT) untuk data siswa, guru, kelas/rombel, dan mata pelajaran.
   - KataMPI mengonsumsi data tersebut melalui REST API aman (`/api/v1/integration/school/*`) menggunakan service token atau Webhook sinkronisasi.

2. **Single Sign-On (SSO)**:
   - Siswa dan guru hanya memiliki satu pasang kredensial login (IAM) yang diatur oleh modul `Core/Security` EduSIS (atau Authentik pada host `ja-auth`).
   - Token login digunakan untuk mengakses portal CBT tanpa perlu registrasi ulang.

3. **Tarik Nilai Hasil Ujian (KataMPI -> SIS)**:
   - Setelah ujian selesai di KataMPI, skor akhir dikirimkan kembali ke modul `Academic/Grade` di EduSIS untuk dimasukkan ke komponen nilai harian / UTS / UAS raport.
