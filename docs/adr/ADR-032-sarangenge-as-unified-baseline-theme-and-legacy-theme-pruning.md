---
status: accepted
scope: theme, core, layout, edusis
date: 2026-09-20
related:
  - ADR-018
  - ADR-022
  - ADR-023
  - ADR-029
  - ADR-030
  - ADR-031
---

# ADR-032: Sarangenge Sebagai Baseline Theme Tunggal & Decommission Tema Lama

**Status:** Accepted — Implemented (2026-09-20)  
**Tanggal:** 2026-09-20  
**Author:** Jejakawan Core & Edusis Architecture Team  
**Scope:** Frontend Theme System, Layout Module, Module Registry (`sys_extensions`), `LicenseService`, Install Profiles, Customizer  
**Related:** ADR-018 (Sarangenge UX & navigation), ADR-022 (seeder architecture), ADR-023 (first-party theme packs & license quotas), ADR-029–031 (Edusis School Module)

---

## 1. Konteks

Platform **Jejakawan Edusis** telah berevolusi dari CMS multi-sektor umum menjadi solusi terintegrasi khusus institusi pendidikan kejuruan dan sekolah (*Vocational & School Management System* / SMK Pusat Keunggulan), didukung oleh kehadiran modul `School` (ADR-030 & ADR-031).

Sebelum keputusan ini:
1. **Multi-Tema Warisan (*Legacy Themes*)**:
   - Platform memelihara 4 tema first-party: `janari` (baseline CMS umum / SaaS), `layung` (ISP / MSP network), `sareupna` (developer / cloud platform), dan `sarangenge` (pendidikan kejuruan).
   - Memelihara 4 tema generik ini menyebabkan redundansi aset, pembengkakan ukuran bundle Vite publik, beban pemeliharaan unit/E2E test suite yang tinggi, dan memudarkan fokus produk edusis.
2. **Ketergantungan Hierarki Semu**:
   - `sarangenge/theme.json` mendeklarasikan `"parent_theme": "janari"`, dan `theme-sarangenge/manifest.json` mewajibkan dependensi `"theme-janari": ">=1.0.0"`.
   - Secara aktual di kode, `sarangenge` sudah 100% mandiri (*self-contained*): memiliki 13 views publik lengkap (`Home`, `About`, `Achievement`, `Blog`, `CareerCenter`, `Contact`, `Facilities`, `Page`, `Post`, `Pricing`, `Programs`, `Search`, `Team`), layout `Header.vue` dan `Footer.vue`, customizer extension terdedikasi, stylesheets independen (`sarangenge.css`), dan bundle data sampel (`sample-data/bundle.json`).
3. **Status Lisensi**:
   - `sarangenge` sebelumnya diperlakukan sebagai tema premium katalog (`license_tier: pro`).

---

## 2. Keputusan

1. **Decommission & Pembersihan Tema Non-Sarangenge**:
   - Menghapus direktori frontend tema `janari`, `layung`, dan `sareupna` di bawah `src/modules/Layout/views/themes/`.
   - Menghapus backend theme packs `theme-janari`, `theme-layung`, dan `theme-sareupna` di bawah `backend/theme-packs/`.
   - Menghapus demo seeder lama (`JanariThemeDemoSeeder.php`, `LayungThemeDemoSeeder.php`, `SareupnaThemeDemoSeeder.php`) dan test E2E spesifik tema lama (`layung-theme-public.spec.ts`).
   - Memindahkan gambar placeholder visual (`avatar-placeholder.png`, `hero-placeholder.png`, `news-placeholder.png`) dari `public/assets/themes/janari/` ke `public/assets/themes/sarangenge/` sebagai aset fallback global `BlockRenderer.vue`.

2. **Transisi Sarangenge Menjadi Root Baseline Theme**:
   - Menghapus `"parent_theme": "janari"` dari `sarangenge/theme.json` dan `"dependencies": { "theme-janari": ... }` dari `theme-sarangenge/manifest.json`.
   - Menetapkan status lisensi `theme-sarangenge` sebagai `license_tier: "free"`, dengan flag `"always_on": true`, `"non_deactivatable": true`, `"premium": false`.
   - Menetapkan `Theme::DEFAULT_FRONTEND_SLUG = 'sarangenge'` di backend Eloquent Model.
   - Memperbarui `ThemeService`, `InstallProfileApplicator`, `ExtensionBootstrapService`, `ExtensionController`, dan `ThemeDowngradeRemediator` agar memperlakukan `sarangenge` sebagai tema default yang dilindungi dari deactivasi / uninstallation.

3. **Penyelarasan Komponen Shared & Resolver**:
   - Mengalihkan import komponen UI pada `MemberPortalTopBar.vue` (seperti `DropdownMenu`) yang sebelumnya meminjam dari `themes/janari/ui` ke `@/shared/components/ui/dropdown-menu`.
   - Menyederhanakan `BUNDLED_FRONTEND_THEME_SLUGS` pada `themeViewResolver.ts` menjadi `['sarangenge']`.
   - Mengarahkan resolver SEO publik `FrontendLayout.vue` ke `resolveSarangengePublicSeo`.
   - Mengarahkan default binding registry ke `getThemeBindingRegistry('sarangenge')`.

4. **Rekonsiliasi Database & Test Suite**:
   - Menghapus data tema yatim piatu di `lay_themes` dan `sys_extensions` lewat scan dan pembersihan DB.
   - Mengubah seluruh suite automated test (PHPUnit, Vitest, Playwright E2E) untuk memverifikasi fungsionalitas baseline `sarangenge`.

---

## 3. Konsekuensi

### Positif (+):
- **Domain Focus**: Antarmuka publik platform selaras 100% dengan identitas Edusis (pendidikan vokasi, TEFA, PPDB, prestasi siswa).
- **Bundle & Performance**: Menghilangkan puluhan berkas Vue/CSS yang tidak terpakai dari bundling Vite frontend, mengurangi ukuran build dan memori cache browser.
- **Maintenance Simplicity**: Pengujian frontend dan backend menjadi terpusat pada satu tema acuan yang solid tanpa risiko regresi antar-tema lama.
- **Arsitektur Mandiri**: Hilangnya dependensi semu ke Janari menyederhanakan siklus rilis dan audit ekstensi.

### Perhatian (-):
- Instalasi yang sebelumnya sengaja menggunakan tampilan generik Janari/Layung/Sareupna akan dialihkan ke antarmuka Sarangenge. Karena Sarangenge mendukung opsi kustomisasi lengkap (skema warna, tata letak, dan builder), tata letak tetap fleksibel disesuaikan melalui Customizer.
