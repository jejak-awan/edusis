# Dokumentasi Resmi JA-EduSIS (`ja-edusis`)

Pusat navigasi arsitektur, standar domain sekolah, panduan teknis, dan referensi sistem **JA-EduSIS** (*Jejakawan Educational School Information System*).

> **Identitas Proyek:** JA-EduSIS adalah sistem informasi manajemen sekolah terpadu berstandar enterprise yang dibangun di atas fondasi **Jejakawan Core Engine** (`ja-core_engine`).  
> **Kebijakan Sinkronisasi Upstream:** Hard-isolation dengan *Selective Upstream Patching* (cherry-pick perbaikan keamanan saja; tanpa global merge). Baca [ADR-029](adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md).

---

## 🎓 1. Domain Sekolah (JA-EduSIS Core)

Seluruh spesifikasi domain operasional sekolah didokumentasikan pada folder [`docs/sis/`](sis/):

| Dokumen | Deskripsi |
| :--- | :--- |
| [**01. Overview & Peta Domain**](sis/01-overview-and-domain-map.md) | Ruang lingkup 7 sub-domain: Akademik, Siswa, HR, Persuratan TU, PPDB, Sarpras, & OSIS |
| [**02. Sintesis Multi-Repositori**](sis/02-multi-repo-synthesis.md) | Landasan sintesis fitur dari `cijulang` (skeleton & Dapodik), `ja-siska` (kredit poin & BK), dan `ja-sis` (persuratan & evoting) |
| [**03. Pemisahan CBT/LMS (KataMPI)**](sis/03-integration-katampi-cbt.md) | Batas arsitektur: mesin CBT dan LMS dialokasikan ke `ja-katampi` (Aksara) terpisah dari SIS |
| [**04. Standar Data & Dapodik**](sis/04-dapodik-and-data-standards.md) | Konvensi tabel `sch_*`, skema PostgreSQL `edusis`, dan kepatuhan atribut Dapodik Kemendikbud |
| [**05. Roadmap & Milestones**](sis/05-milestones-and-roadmap.md) | Rencana tahapan pengerjaan (Fase 1 s/d Fase 5) |
| [**06. Blueprint Scaffold & Sub-Domain**](sis/06-scaffold-and-architecture-blueprint.md) | Cetak biru teknis struktur backend `Modules/School` & frontend `modules/School` |
| [**ADR-029: EduSIS Domain & Synthesis**](adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md) | Keputusan arsitektur resmi lahirnya JA-EduSIS |
| [**ADR-030: School Module Scaffolding**](adr/ADR-030-school-module-scaffold-and-subdomain-architecture.md) | Keputusan struktur sub-domain, tabel `sch_*`, dan multi-portal UI |

---

## 🧭 2. Panduan Alur Kerja Pengembang & Agen

| Dokumen | Deskripsi |
| :--- | :--- |
| [**AGENT_START_HERE**](AGENT_START_HERE.md) | Konteks proyek, aturan agent, dan batasan upstream |
| [**WORKFLOW**](WORKFLOW.md) | Alur kerja terstruktur: audit → diskusi → rencana → implement → verify → catat |
| [**Work Log Aktif**](work/README.md) | Daftar tugas aktif dan riwayat task selesai |
| [**Branching**](branching.md) | Konvensi branch `main` + `integrate/*` + `feat`/`fix` |
| [**Guides (How-To)**](guides/README.md) | Prosedur teknis: verify gates, RBAC sync, add module, i18n |
| [**Reference**](reference/README.md) | Katalog modul terpasang, CLI commands, HTTP OpenAPI contract |

---

## 🛡️ 3. Fondasi Teknis Platform (Jejakawan Core Engine)

JA-EduSIS mewarisi standar arsitektur dan sistem keamanan enterprise dari Core Engine:

| Aspek | Penjelasan Arsitektur | Panduan Teknis / Reference |
| :--- | :--- | :--- |
| **Keamanan & Governance** | [06. Security & Governance](architecture/06-security-and-governance.md) | ABAC, RBAC, KYC, SIEM audit log, Bot Shield, [Security Checks](guides/security-checks.md) |
| **Standar Backend** | [02. Backend Standards](architecture/02-backend-standards.md) | Laravel 13, FormRequest validation, Service layer, tests |
| **Standar Frontend** | [03. Frontend Standards](architecture/03-frontend-standards.md) | Vue 3 SPA, Pinia state stores, TypeScript, TanStack Table |
| **Internasionalisasi** | [05. i18n Guidelines](architecture/05-i18n-guidelines.md) | Paritas multibahasa: Indonesia (`id`), Sunda (`su`), Inggris (`en`) |
| **Modul & Ekstensi** | [Extensions & Modul](extensions/README.md) · [Contract](extensions/module-contract.md) · [Lifecycle](extensions/lifecycle.md) | Nwidart modularity, manifest schema, capability registry |
| **Penyimpanan Berkas** | [Media Library](reference/modules.md) | Unggah foto siswa/guru, dokumen PPDB, kompresi aset |
| **Layanan Surel** | [JA-Mail](extensions/ja-mail.md) | Transaksional email, reset kata sandi, notifikasi |
| **ADRs Platform** | [Indeks ADR Core (ADR-001 s/d ADR-028)](adr/README.md) | Riwayat keputusan teknis platform dasar |

---

## 🗄️ 4. Repositori Referensi Lokal

Bahan komparasi dan blueprint domain tersimpan di [`/home/jejakawan/dev/repo_referensi/`](file:///home/jejakawan/dev/repo_referensi/):
- **`cijulang/`**: Basis arsitektur `Modules/School` & entitas Dapodik.
- **`ja-siska/`**: Spesialisasi poin kredit kesiswaan, alur BK, & notifikasi WhatsApp.
- **`ja-sis/`**: Spesialisasi persuratan tata usaha, e-voting OSIS, & ekstrakurikuler.
