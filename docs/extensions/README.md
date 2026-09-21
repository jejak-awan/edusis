# Extensions & Modules — Indeks Dokumentasi

Dokumentasi arsitektur modular, kontrak paket (*first-party packs*), skema manifest, siklus hidup (*lifecycle*), profil instalasi, dan integrasi fitur di **JA-EduSIS**.

> **SoT Modul:**  
> Kode & konfigurasi modul berada di `backend/Modules/<Name>/` (backend) dan `frontend/src/modules/<Name>/` (frontend).  
> Ekstensi modular ringan berada di `backend/extensions/<slug>/`.  
> Paket tema berada di `backend/theme-packs/<slug>/`.

---

## Indeks Dokumen

| Dokumen | Kategori | Deskripsi |
| :--- | :--- | :--- |
| [**Module Contract**](module-contract.md) | Kontrak L1 | Aturan wajib dual-enablement (nwidart boot vs product active), struktur berkas, dan checklist gating |
| [**Module Manifest Schema**](module-manifest.schema.json) | JSON Schema | Skema validasi JSON untuk `manifest.json` (mendukung module, plugin, theme, dan family `sis`) |
| [**Extension Lifecycle**](lifecycle.md) | Arsitektur | Alur discover → activate → deactivate → uninstall serta aturan data preservation |
| [**Install Profiles**](install-profiles.md) | Konfigurasi | Panduan profil instalasi otomatis (`core`, `cms`, `cms_site`) dan penegakan baseline tema Sarangenge |
| [**External Module Packaging**](external-module-packaging.md) | Panduan | Pengemasan dan linking modul eksternal atau domain vertikal (seperti `Modules/School`) |
| [**RBAC & Lifecycle Seeders**](rbac-and-lifecycle-seeders.md) | Arsitektur / RFC | Strategi sinkronisasi Spatie roles, permission discovery, dan orchestrator seeder aktivasi |
| [**Member Area**](member-area.md) | Spesifikasi / RFC | Arsitektur portal reader/anggota publik, akun mandiri, dan integrasi chrome tema Sarangenge |
| [**JA-Mail**](ja-mail.md) | Modul | Dokumentasi teknis modul surel transaksional, webmail console, dan AI drafting copilot |

---

## Modul dan Ekstensi Aktif di JA-EduSIS

### Modul Inti & Domain (`backend/Modules/`)
- **`Core`**: Fondasi kernel platform (IAM, System, Security, Infra) — *always active*.
- **`School`**: Sistem Informasi Sekolah JA-EduSIS (Akademik, Siswa, GTK, Presensi, Rapor, Persuratan, PPDB, OSIS).
- **CMS Family**: `Publishing`, `Media`, `Library`, `Layout`, `Forms`, `Site`, `Member`.
- **Intelligence & Utilities**: `CmsAi`, `Analytics`, `Search`, `Mail`, `Newsletter`.

### Ekstensi Modular (`backend/extensions/`)
- **`floating-social-dock`**: Tombol interaktif media sosial dengan animasi GSAP.
- **`instagram-feed`**: Integrasi feed media sosial publik.

### Theme Pack (`backend/theme-packs/`)
- **`theme-sarangenge`**: Baseline theme tunggal JA-EduSIS untuk institusi sekolah kejuruan dan pendidikan.

---

## Kebijakan & Referensi Terkait
- Kebijakan Dokumentasi: [`docs/DOCUMENTATION.md`](../DOCUMENTATION.md)
- Katalog Modul: [`docs/reference/modules.md`](../reference/modules.md)
- Indeks Kontrak: [`docs/reference/contracts.md`](../reference/contracts.md)
