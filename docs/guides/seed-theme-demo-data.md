# How-to: Seed theme demo data

Isi **data demo generik** untuk tema resmi (bukan seeder deployment klien).

## Prerequisites

- DB migrated & kernel seed sudah jalan
- Modul Layout aktif
- **Jangan** taruh identitas legal klien di seeder core — lihat ADR-022 / `AGENTS.md`

## Steps

1. Seed tema aktif (default **`sarangenge`** di JA-EduSIS per ADR-032):

```bash
cd backend
php artisan theme:seed
```

2. Seed slug spesifik:

```bash
php artisan theme:seed sarangenge
```

3. Seed seluruh tema resmi terpasang:

```bash
php artisan theme:seed --all
```

Perintah ini memanggil `*ThemeDemoSeeder` di `Modules/Layout` dan menjalankan `ThemeService::scanThemes()` terlebih dahulu.

## Bundle contract (`sample-data/bundle.json`)

SoT detail: [`theme-host-contract.md`](../../frontend/src/modules/Layout/views/themes/theme-host-contract.md) (section sample-data).

Ringkas:

- `settings` — defaults tema
- `menus` — **object keyed by location** (`header`, `footer`, …), bukan array
- `pages` / `posts` — konten Publishing demo (slug, title, body, `theme_page` untuk pages)

## Verify

- Tema terdaftar di console Appearance / theme list
- Halaman publik tema menampilkan konten demo generik (bukan brand klien)

## Related

- Theme architecture: [04-theme-system.md](../architecture/04-theme-system.md)
- Decision: [ADR-022](../adr/ADR-022-upstream-core-curation-and-generic-theme-seeder-architecture.md)
- Themes index: [themes/README.md](../themes/README.md)
