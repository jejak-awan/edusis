# Audit & Evidence — JA-EduSIS

Direktori ini mencatat hasil audit berkala, investigasi teknis, analisis keamanan, dan laporan kepatuhan (*governance*) pada proyek **JA-EduSIS** (`ja-edusis`).

## Standar Penulisan Laporan Audit

Setiap laporan audit baru wajib mengikuti konvensi penamaan:
- Format: `AUDIT-YYYY-MM-DD-<topik-audit>.md`
- Komponen laporan:
  1. **Metadata**: Tanggal pelaksanaan, auditor (manusia / agen AI), ruang lingkup (*scope*).
  2. **Temuan (Findings)**: Diklasifikasikan berdasarkan tingkat urgensi (P0 Kritis, P1 Tinggi, P2 Sedang, P3 Rendah/Peningkatan).
  3. **Rekomendasi Tindakan**: Langkah perbaikan teknis.
  4. **Resolusi & Verifikasi**: Log commit atau verifikasi setelah audit diselesaikan.
