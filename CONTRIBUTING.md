# Contributing — JA-EduSIS

Panduan kontribusi untuk **`ja-edusis`** (Education Management Information System / Sistem Informasi Akademik Sekolah). Berdiri di atas Jejakawan Core Engine dan terintegrasi dengan ekosistem Jejakawan (termasuk KataMPI untuk CBT Engine).

## Mulai di sini

1. [`docs/WORKFLOW.md`](docs/WORKFLOW.md) — **Audit → Diskusi → Rencana → Implement → Verify → Catat**
2. [`docs/DOCUMENTATION.md`](docs/DOCUMENTATION.md) — SoT / SoC / Diátaxis
3. [`docs/AGENT_START_HERE.md`](docs/AGENT_START_HERE.md) · [`AGENTS.md`](AGENTS.md)
4. [`.cursor/rules/school-governance.mdc`](.cursor/rules/school-governance.mdc) — Tata Kelola Modul Sekolah & Kurikulum Merdeka
5. Task M+: brief dari [`docs/templates/task-brief.md`](docs/templates/task-brief.md) → [`docs/work/`](docs/work/README.md)

## Definition of Done (ringkas)

- **SoC Domain Arsitektur**: Core (`core_*`), School (`sch_*`), Community (`comm_*`), Extensions (`ext_*`).
- **Standar Sekolah & Kurikulum Merdeka**: Wajib mematuhi skema Dapodik dan format pelaporan resmi (rapor, ledger).
- **Decoupled CBT**: Seluruh fitur ujian terkomputerisasi didelegasikan ke KataMPI via token/SSO; dilarang membuat CBT engine monolitik di EduSIS.
- **Perlindungan Privasi (PII)**: Data sensitif (NISN, NIK, kontak wali, nilai) wajib dilindungi oleh otorisasi ketat RBAC dan audit log.
- `npm run agent:verify` (termasuk `docs:links` + SemVer checks)
- BE runtime: `npm run test:backend` (atau CI) — lihat [reference/testing.md](docs/reference/testing.md)
- FE runtime: `npm run type-check` dan build
- Changelog dual-layer: [guides/update-changelog.md](docs/guides/update-changelog.md)
- Release / SemVer: [guides/release-and-versioning.md](docs/guides/release-and-versioning.md) (`npm run versions:check`, `npm run modules:versions:check`)
- E2E UX kritis: [guides/run-e2e-playwright.md](docs/guides/run-e2e-playwright.md)
- Modul `README.md` / `CHANGELOG.md` tetap akurat
- Konvensi pesan commit wajib menggunakan format Conventional Commits `feat(...)`

## Branch & Kebijakan Repositori

- Trunk: **`main`** (`git@github.com:jejak-awan/edusis.git`)
- Kebijakan arsitektur repo: [`docs/branching.md`](docs/branching.md)

## Dilarang

- Menyimpan data pribadi siswa/guru nyata (PII) atau kredensial produksi di repositori
- Menggabungkan business logic CBT langsung ke dalam modul EduSIS (gunakan integrasi KataMPI)
- Mengubah penomoran ADR
- Melemahkan security gate atau bypass RBAC permission "supaya lulus lokal"

