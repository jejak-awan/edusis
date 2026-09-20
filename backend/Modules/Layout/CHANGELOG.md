# Changelog — Layout

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [1.1.10] - 2026-09-20

### Fixed
- Fixed `inject()` warning outside Vue setup context in `useTheme.ts` via `hasInjectionContext()` guard.
- Auto-healed legacy theme requests (`janari`, `layung`, `sareupna`) and stale browser session snapshots to `sarangenge` universal baseline in `themeViewResolver.ts` and `useTheme.ts`.

## [1.1.9] - 2026-09-20

### Changed
- **ADR-032 Sarangenge Unified Baseline Theme**: Set `sarangenge` as the default frontend baseline theme (`Theme::DEFAULT_FRONTEND_SLUG = 'sarangenge'`), protected from deactivation.
- Decommissioned legacy themes (`janari`, `layung`, `sareupna`) and streamlined demo seeding to `SarangengeThemeDemoSeeder`.

## [Unreleased]

### Changed
- Version 1.1.14: Purged obsolete janariPresets, streamlined headerChromeWrapClass for Sarangenge baseline theme, and removed legacy fixed header padding from FrontendLayout.
- Version 1.1.13: Removed Data Models accordion, fetchModels, and models state from menu builder SourcePanel.
- Version 1.1.12: Decommissioned visual builder subsystem, removed BuilderController, BuilderPresetController, builder routes, and cleaned editorial nav.
- Version 1.1.11: Extracted unified colorPresets config, cleaned legacy theme classes from FrontendLayout, and updated customizer preset selector.

### Added
- **ADR-028 Menu Builder Properties, Mega Menu Architecture, and URL Anchor Validation**: Documented architectural decision, allowance of anchor URLs (`#`, `#anchor`) in `MenuItemUrlValidator.php`, and 3-level eager loading (`parentItems.children.children`) in `MenuController.php` ([ADR-028](../../docs/adr/ADR-028-menu-builder-properties-mega-menu-and-multi-theme-parity.md)).
- **Theme Demo Seeder Soft-Deleted Content Restoration**: Enhanced `SarangengeThemeDemoSeeder.php` and `ThemeSampleDataOrchestrator.php` to query `Content::withTrashed()` and automatically `restore()` soft-deleted items, preventing SQL unique constraint collisions during demo seeding.
- Non-destructive theme demo seeders: `JanariThemeDemoSeeder`, `LayungThemeDemoSeeder`, `SarangengeThemeDemoSeeder`, and `SareupnaThemeDemoSeeder` default to non-destructive seeding (`Setting::setIfMissing`, `force: false`) to safeguard downstream client customization from database overwrites.
- `theme:seed` CLI command: added `--force` option to explicitly opt-in to hard-resetting existing settings and sample data.
- Host SEO resolver for theme **Sareupna** (`FrontendLayout` + `sareupnaPublicSeo`) — CMS baseline P0.
- **Site Identity to Theme Settings Sync**: Integrated with `SettingController::updatePlatformIdentity` where `site_name`, `site_logo`, and `site_favicon` are synchronized into active theme settings (`lay_theme_settings`) when smart brand sync is invoked ([ADR-014](../../docs/adr/ADR-014-three-tier-identity-whitelabel-brand-and-smart-sync.md)).
- **Visual Builder Decoupling**: Modularized Visual Builder (`builder.site`) manifest with license gating ([ADR-011](../../docs/adr/ADR-011-visual-builder-modular-extension-and-license-gating.md)).
- **ADR-023 3-Level Theme Pipeline & Quota Metadata**: `ThemeController::index()` returns per-theme flags (`is_pack_enabled`, `is_entitled`, `is_premium`) and response metadata (`meta.quota` and `meta.site_active`).

### Fixed
- Code style formatting and single blank line at EOF per Laravel Pint standards.
- `dynamicSources` no longer logs the full query string.
- ThemeSampleDataOrchestrator and blocks factory strict typing fixes.

### Changed
- ADR-023: Hardened ThemeService activation gates, ThemeDowngradeRemediator integration, and ThemeSeedCommand licensing guard.

### Changed
- Manifest version → `1.1.1` (FE host SEO wiring for sareupna CMS baseline).
- Manifest widget types match runtime (`content_list`, `menu`, `form`).
- Public menus/themes return empty when the layout pack is inactive.
- Widget types register even when an active theme supplies locations.
- Default frontend theme is **Janari** (builder/fork reference); Sarangenge remains optional alternate.
- Menu location assignments sync into active theme `menu_location_{slot}` settings; customization publish preserves those keys; public `GET /layout/menus/{id}` resolves UUID assignments.

## [1.1.0] - P3-3b (themes + visual builder)

- Full Theme API, theme CLI commands, and Janari theme bundle.
- Visual Builder + BlockRenderer wired into Publishing create/edit.
- `lay_builder_presets` migration; builder dynamic-sources API.
- Console menu: Site Editor + Themes under Editorial.

## [1.0.0] - P3-3a (menus / widgets / redirects)

- Optional first-party pack extracted from ja-cms Content/Layout (menus-first slice).
- Console: Menus, Widgets (Editorial); Redirects (Infrastructure).
- Soft Theme model for menu usage only.
