---
id: 2026-09-20-theme-sarangenge-baseline-unification
title: "Transisi Tema Sarangenge Sebagai Default Baseline & Decommission Tema Lama"
status: completed
scale: M
repo: ja-edusis
owner: agent
created: 2026-09-20
updated: 2026-09-20
blocks: []
related_adr: ["docs/adr/ADR-032-sarangenge-as-unified-baseline-theme-and-legacy-theme-pruning.md"]
---

# Task: Transisi Tema Sarangenge Sebagai Default Baseline & Decommission Tema Lama

## 1. Tujuan
Menjadikan tema **`sarangenge`** sebagai tema *default* (*baseline / always-on*) utama platform Jejakawan menggantikan tema `janari`, serta menghapus seluruh sisa tema legacy (`janari`, `layung`, `sareupna`) dari lapisan frontend (`views/themes/`) dan backend (`theme-packs/`).

---

## 2. Rincian Perubahan

### A. Frontend:
1. Pindahkan gambar placeholder visual (`avatar-placeholder.png`, `hero-placeholder.png`, `news-placeholder.png`) dari `public/assets/themes/janari/` ke `public/assets/themes/sarangenge/`.
2. Hapus direktori `src/modules/Layout/views/themes/{janari,layung,sareupna}/`.
3. Hapus folder `public/assets/themes/janari/`.
4. Hapus script `frontend/scripts/{merge,split}-janari-theme-schema.mjs`.
5. Perbarui `sarangenge/theme.json`: hapus `"parent_theme": "janari"`.
6. Perbarui `themeViewResolver.ts`: jadikan `BUNDLED_FRONTEND_THEME_SLUGS = ['sarangenge']` dan fallback `['sarangenge']`.
7. Perbarui `useTheme.ts`: fallback active theme `{ name: 'Sarangenge', slug: 'sarangenge', type: 'frontend' }`.
8. Perbarui `FrontendLayout.vue`: gunakan `resolveSarangengePublicSeo` langsung; hapus import SEO janari, layung, sareupna.
9. Decouple `MemberPortalTopBar.vue`: gunakan `@/shared/components/ui/dropdown-menu`.
10. Perbarui `themeBindingsRegistry.ts`, `resolveThemeCustomizerExtension.ts`, `getThemePreviewTargets.ts`, `moduleLocales.ts`, `Index.vue`, `ThemePreview.vue`, `BlockRenderer.vue`, `builder` views, dan Tailwind config.

### B. Backend:
1. Hapus direktori `backend/theme-packs/{theme-janari,theme-layung,theme-sareupna}/`.
2. Hapus seeder `JanariThemeDemoSeeder.php`, `LayungThemeDemoSeeder.php`, `SareupnaThemeDemoSeeder.php`.
3. Perbarui `backend/theme-packs/theme-sarangenge/manifest.json`: hapus dependensi `theme-janari`, set `always_on: true`, `non_deactivatable: true`, `premium: false`, `license_tier: "free"`.
4. Perbarui `Theme.php`: `DEFAULT_FRONTEND_SLUG = 'sarangenge'`.
5. Perbarui `ThemeService.php`, `InstallProfileApplicator.php`, `ExtensionFamilyCatalog.php`, `LicenseService.php`, `ExtensionController.php`, `ExtensionBootstrapService.php`, `ThemeDowngradeRemediator.php`, `ThemeController.php`, `ThemeSeedCommand.php`, `SarangengeThemeDemoSeeder.php`.
6. Bump versi `backend/Modules/Layout/manifest.json` ke `1.1.9` dan catat di `CHANGELOG.md`.

### C. Test Suite & Database:
1. Perbarui Vitest unit test `themeViewResolver.spec.ts` dan `mergeThemeSettingsSchema.spec.ts`.
2. Perbarui PHPUnit feature test `DefaultFrontendThemeTest.php`, `ThemeSampleDataInstallTest.php`, `ThemePackageLifecycleTest.php`, `MenuThemeSlotSyncTest.php`, `ThemePackRegistryTest.php`, `ExtensionControllerTest.php`.
3. Hapus `frontend/tests/e2e/layung-theme-public.spec.ts`.
4. Sesuaikan Playwright E2E `theme-registry-3level-flow.spec.ts` dan `honesty-pass-browser.spec.ts`.
5. Jalankan rekonsiliasi DB via artisan scan & tinker.

---

## 3. Checklist Eksekusi

- [x] Dokumentasi ADR-032 & work document dibuat
- [x] Placeholder visual disalin ke `public/assets/themes/sarangenge/`
- [x] Folder tema legacy FE dihapus (`janari`, `layung`, `sareupna`)
- [x] Folder theme-packs legacy BE dihapus (`theme-janari`, `theme-layung`, `theme-sareupna`)
- [x] Seeder demo legacy dihapus
- [x] Manifest Sarangenge FE & BE distandarkan sebagai root baseline
- [x] Backend core & layout services dimodifikasi
- [x] Frontend resolver, layout, customizer, builder, locales dimodifikasi
- [x] Automated tests (Vitest, PHPUnit, Playwright E2E) disesuaikan
- [x] Database runtime disinkronkan ke Sarangenge
- [x] Seluruh quality gate (`agent:verify`) berstatus PASS
- [x] Dokumentasi `docs/themes/README.md` dan `docs/extensions/install-profiles.md` diperbarui
- [x] Sinkronisasi multi-host `docs/` ke `ja-srv` dan `ja-aksara` via `rsync`
- [x] Git commit dilakukan dengan format `feat(...)`
