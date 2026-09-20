# Agent Start Here — JA-EduSIS

## 1. Cerita Produk

**`ja-edusis`** (*Jejakawan Educational School Information System*) adalah produk vertikal sistem manajemen sekolah terpadu berstandar enterprise yang dibangun di atas fondasi **`ja-core_engine`**.

- **Identitas Produk**: JA-EduSIS (bukan CMS, bukan generic portal).
- **Domain Inti**: Sistem Informasi Sekolah mencakup Akademik, Kesiswaan, Kepegawaian (HR), Tata Usaha, PPDB, Sarpras, dan OSIS.
- **Pemisahan CBT/LMS**: EduSIS **TIDAK** menjalankan mesin ujian CBT atau LMS interaktif di codebase ini; fungsi evaluasi CBT didelegasikan ke **`ja-katampi`** (kluster Aksara).
- **Hubungan dengan Upstream (`ja-core_engine`)**: Hard-isolated repository. **Dilarang melakukan `git merge upstream/main`**. Patch perbaikan keamanan atau bugfix kernel Core Engine hanya diambil secara selektif via `git cherry-pick`.

---

## 2. Repositori Referensi Domain

Selalu rujuk ke [`/home/jejakawan/dev/repo_referensi/`](file:///home/jejakawan/dev/repo_referensi/):
- **`cijulang/`**: Skeleton modular `Modules/School`, kepatuhan format Dapodik, dan model Akademik/HR/Sarpras.
- **`ja-siska/`**: Sistem kredit poin disiplin akumulatif (SP1-SP3), alur Bimbingan Konseling (BK), dan presensi QR.
- **`ja-sis/`**: Sistem persuratan TU (Surat Masuk/Keluar/Disposisi), E-Voting OSIS, dan ekstrakurikuler.

---

## 3. Struktur Wajib Dibaca

1. [docs/sis/01-overview-and-domain-map.md](sis/01-overview-and-domain-map.md) — Peta sub-domain EduSIS
2. [docs/sis/02-multi-repo-synthesis.md](sis/02-multi-repo-synthesis.md) — Sintesis 3 repo referensi
3. [docs/sis/03-integration-katampi-cbt.md](sis/03-integration-katampi-cbt.md) — Batasan arsitektur SIS vs KataMPI CBT
4. [docs/sis/04-dapodik-and-data-standards.md](sis/04-dapodik-and-data-standards.md) — Skema tabel `sch_*` & standar Dapodik
5. [docs/sis/05-milestones-and-roadmap.md](sis/05-milestones-and-roadmap.md) — Roadmap tahapan pengembangan
6. [docs/adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md](adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md) — ADR arsitektur EduSIS
7. [docs/WORKFLOW.md](WORKFLOW.md) — Alur kerja agen (audit → diskusi → rencana → implement → verify → catat)
8. [docs/DOCUMENTATION.md](DOCUMENTATION.md) — SoT layers, Diátaxis, aturan ADR
9. [docs/architecture/01-overview-and-tier-design.md](architecture/01-overview-and-tier-design.md) · [02 Backend standards](architecture/02-backend-standards.md) · [03 Frontend standards](architecture/03-frontend-standards.md) · [06 Security](architecture/06-security-and-governance.md)
10. [AGENTS.md](../AGENTS.md) — Aturan workspace Jejakawan Dev (multi-host sync docs & commit `feat(...)`)

---

## 4. Quality Gate Sebelum Selesai

```bash
# Backend verification
cd backend && php artisan test

# Frontend verification
cd frontend && npm run i18n:check && npx vue-tsc -b
```

---

## 5. Aturan Penting

- Sebut produk ini sebagai **JA-EduSIS** (bukan `ja-core_engine`, bukan `ja-cms`).
- Jangan mencampurkan kode mesin CBT/LMS ke dalam modul `School` (arah integrasi ke KataMPI).
- Prefix tabel modul sekolah wajib menggunakan `sch_*` di dalam skema PostgreSQL `edusis`.
- Gunakan konvensi commit `feat(...)` sesuai mandat workspace Jejakawan Dev.
- Jangan pernah commit kredensial atau file `.env`.
