# 06. Security & Governance Standards — JA-EduSIS

Panduan arsitektur keamanan, mitigasi celah, tata kelola data sekolah, dan kepatuhan sistem di **JA-EduSIS (`ja-edusis`)**.

---

## 🛡️ 1. Lapisan Keamanan Utama

JA-EduSIS menerapkan prinsip *Defense in Depth* di seluruh tingkatan aplikasi:

```
┌─────────────────────────────────────────────────────────────┐
│ 1. HTTP Layer: Strict CSP (Nonce-based) + HSTS + CORS       │
├─────────────────────────────────────────────────────────────┤
│ 2. Network / Firewall: Dynamic IP Rate Limiting + Allowlist │
├─────────────────────────────────────────────────────────────┤
│ 3. IAM Layer: RBAC (Spatie) + ABAC Policies + WebAuthn/2FA  │
├─────────────────────────────────────────────────────────────┤
│ 4. School Governance: PII Protection + Role Segregation     │
├─────────────────────────────────────────────────────────────┤
│ 5. Data Layer: Prepared Statements + File Sanitization      │
├─────────────────────────────────────────────────────────────┤
│ 6. Audit & Observability: Activity Logs + SIEM Log Exporter │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔒 2. Mekanisme Keamanan Khusus

### A. Content Security Policy (CSP Nonce-based)
- Setiap request HTTP menghasilkan cryptographic nonce unik (`ViteNonceMiddleware`).
- Tag `<script>` dan `<style>` yang diinjeksi ke DOM harus memiliki atribut `nonce="..."`.
- Mencegah serangan *Cross-Site Scripting (XSS)* dan *inline script injection*.

### B. Otentikasi Modern (Passkeys WebAuthn & 2FA TOTP)
- Operator dan staf dapat mengamankan akun menggunakan biometric Passkeys (FaceID, TouchID, Windows Hello) via standard WebAuthn API (`@laravel/passkeys`).
- Dukungan autentikasi dua faktor (TOTP) berbasis Google Authenticator / Authy.

### C. Sanitasi Upload & SVG Protection
- **Media Upload**: Berkas unggahan (foto profil siswa/guru, dokumen PPDB, berkas persuratan) divalidasi MIME type dan ekstensi secara ketat.
- **SVG Sanitizer**: Berkas SVG yang diunggah diproses melalui sanitizer (`enshrined/svg-sanitize`) untuk menghapus elemen berbahaya seperti `<script>`, `onload`, `javascript:`, dan XML external entities (XXE).

### D. Rate Limiting & Bot Prevention
- Endpoint otentikasi dan presensi publik dilindungi oleh dynamic sliding-window rate limiter.
- Formulir PPDB publik dilengkapi proteksi bot transparan dengan reCAPTCHA v3 / Cloudflare Turnstile token verification dengan backend signature validation.

### E. ABAC (Attribute-Based Access Control)
- Melengkapi RBAC konvensional, modul keamanan memiliki mesin ABAC untuk menegakkan kebijakan berdasarkan konteks pengguna (misal: pembatasan akses modul rapor hanya pada jam kerja atau verifikasi IP presensi geofencing).

---

## 🎓 3. Perlindungan Data Pribadi Siswa & Guru (PII Governance)

Sesuai dengan regulasi perlindungan data pribadi dan tata kelola Dapodik:

1. **Kerahasiaan Data Sensitif**:
   - Data identitas siswa dan guru (NISN, NIK, No. KK, riwayat medis, nomor telepon pribadi, dan catatan konseling BK) diklasifikasikan sebagai data rahasia (*Confidential*).
   - Dilarang keras mengekspos atribut sensitif ini melalui API publik atau endpoint tanpa autentikasi.
2. **Pemisahan Peran (Role Segregation)**:
   - **Guru Mapel**: Hanya berhak menginput dan melihat nilai mata pelajaran yang diampunya.
   - **Wali Kelas**: Berhak melihat ledger nilai, menginput catatan perkembangan karakter, dan mencetak rapor untuk kelas bimbingannya.
   - **Guru BK**: Memiliki akses eksklusif ke catatan konseling dan poin pelanggaran/prestasi siswa.
   - **Operator TU**: Mengelola data induk siswa/GTK dan surat kedinasan.
3. **Audit Trail Ekspor Data**:
   - Setiap operasi ekspor data siswa atau rekapitulasi nilai (dalam format Excel, CSV, atau PDF rapor) wajib tercatat di audit log sistem dengan identitas operator, timestamp, dan cakupan data.
4. **Isolasi Mesin Ujian (CBT Decoupling)**:
   - Mesin CBT (JA-Katampi) berjalan di host terpisah (`ja-aksara`), sehingga lonjakan beban atau potensi eksploitasi saat ujian online tidak berdampak pada integritas database administratif sekolah (`edusis`).

---

## 📊 4. Monitoring & Audit Keamanan

1. **Security Activity Log**:
   - Setiap aksi administratif, percobaan login gagal, perubahan hak akses, mutasi data siswa, atau pengubahan konfigurasi dicatat ke dalam database log aktivitas.
2. **SIEM Exporter**:
   - Mendukung ekspor log keamanan real-time ke format standar SIEM (Security Information and Event Management).
3. **Automated Security Audit Workflow**:
   - CI pipeline secara berkala menjalankan `composer audit` dan `npm audit` untuk mendeteksi kerentanan dependensi pihak ketiga.

