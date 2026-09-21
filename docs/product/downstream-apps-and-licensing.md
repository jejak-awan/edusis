# Downstream apps & licensing — JA-EduSIS Context

## Peran JA-EduSIS

**JA-EduSIS** (`ja-edusis`) adalah produk vertikal sistem informasi sekolah (*hard-isolated*) yang dibangun di atas fondasi Jejakawan Core Engine. Repositori ini mengimplementasikan domain sekolah terpadu (`Modules/School`), portal komunitas (`Modules/Community`), dan integrasi CBT eksternal (`ja-katampi`).

## Hub lisensi (JA-CP)

| Komponen | Repo | Peran |
| :--- | :--- | :--- |
| Licensing & heartbeat | `ja-control-plane` | Aktivasi key, tier institusi sekolah, validasi kuota rombel/siswa |
| Runtime aplikasi | `ja-edusis` | Menjalankan sistem informasi sekolah; sync status via `license:check` |


Env relevan:

- `services.jacp.url` — URL control plane (default produksi Jejakawan)
- Settings `license_key`, `license_type`, `license_status`

UI: **Settings → License** — sync manual ke JA-CP.

## OAuth / SSO

Core engine **bisa** bertindak sebagai **OAuth2 identity provider** untuk app downstream (`ja-platform`, tenant apps, dsb.). Copy lama menyebut "control plane" — yang benar: **instance core engine ini** sebagai IdP.

## Pola membangun app baru

1. Fork atau submodule `ja-core_engine` @ `main`
2. Tambah **optional packs** (Mail contract) — lihat [external-module-packaging.md](../extensions/external-module-packaging.md)
3. Atau scaffold produk: `scripts/bootstrap-downstream-app.sh`
4. Register `modules_statuses.json` + Module Registry activate
5. (Opsional) Aktivasi lisensi via JA-CP untuk tier Pro/Enterprise

CMS domains diekstrak dari `ja-cms` sebagai pack (`publishing`, `media`, `forms`, …) — bukan dimasukkan ke `Modules/Core`.

## Editions (konsep)

| Edition | Isi tipikal |
| :--- | :--- |
| **Community** | Core kernel; public themes = **janari only** (lihat ADR-023) |
| **Starter / Pro** | Extension premium; Pro theme quota = **janari + 1 premium** dari katalog |
| **Enterprise / White-label** | Multi-site, branding console; **semua** first-party themes |

Detail tier = kontrak bisnis di JA-CP; matrix fitur di `LicenseService::getFeaturesMatrix()`.  
Theme packs sebagai Module Registry + kuota: **[ADR-023](../adr/ADR-023-first-party-themes-as-registry-packs-and-license-quotas.md)** (fase 1 implementasi: [`docs/work/archive/core-engine/2026-09-18-theme-registry-packs-phase1.md`](../work/archive/core-engine/2026-09-18-theme-registry-packs-phase1.md)).
