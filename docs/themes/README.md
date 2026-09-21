# Themes — Indeks Dokumentasi

Dokumentasi tema publik di JA-EduSIS.

> **SoT UI/paket tema** tetap di kode:  
> `frontend/src/modules/Layout/views/themes/<slug>/readme.md`  
> Folder ini hanya **indeks + ADR theme-scoped**. Jangan menduplikasi README tema.

## Paket tema

| Tema | Status | Fokus | SoT di kode |
| :--- | :--- | :--- | :--- |
| [Sarangenge](sarangenge/README.md) | **Active (Default Baseline Tunggal)** | Sekolah / vokasi / kampus | [`.../sarangenge/readme.md`](../../frontend/src/modules/Layout/views/themes/sarangenge/readme.md) |

> [!NOTE]
> **Pembersihan Tema Legacy:** Berdasarkan [ADR-032](../adr/ADR-032-sarangenge-as-unified-baseline-theme-and-legacy-theme-pruning.md), paket tema lama (`janari`, `layung`, dan `sareupna`) telah didekomisi dan dihapus dari repositori JA-EduSIS untuk memfokuskan platform sepenuhnya pada ekosistem pendidikan kejuruan dan sekolah.


## Cross-cutting (bukan per-tema)

- [Architecture: Theme System](../architecture/04-theme-system.md) — host vs theme, SoC/SoT customizer
- [ADR-022](../adr/ADR-022-upstream-core-curation-and-generic-theme-seeder-architecture.md) — generic theme demo seeders

Kebijakan: [`DOCUMENTATION.md`](../DOCUMENTATION.md).

## CMS baseline (alignment tema)

Kontrak fitur yang **harus sama** vs yang **tetap khas** per tema:  
[`docs/work/archive/core-engine/2026-09-18-theme-cms-baseline-alignment.md`](../work/archive/core-engine/2026-09-18-theme-cms-baseline-alignment.md)

### Checklist kontrak (P0)

Setiap paket tema publik harus punya:

| Item | Catatan |
| :--- | :--- |
| Own `pages/About.vue` bila `enable_about=true` | Jangan andalkan fallback parent chrome |
| Contact → Reach via `contact_form_slug` | Jangan hardcode `/public/forms/contact/submit` |
| `menus` di `theme.json` | Minimal: header, footer (+ opsional col/sidebar) |
| `*PublicSeo` + identity composable | Dipakai host `FrontendLayout` / chrome |
| sample-data `pages` + `posts` | Demo seed Publishing |
| `disabled_page_behavior` | Enum: `message` \| `redirect` saja |
| Customizer schema theme-only | Platform identity / floating dock / colors dari host `global.settings.schema.json` |
| Instagram | Extension `instagram-feed` + slots — **bukan** `social_instagram_feed_*` di tema |
| PluginSlot | `after_hero` on CMS pages; `before_footer` host-only — see [theme-host-contract](../../frontend/src/modules/Layout/views/themes/theme-host-contract.md) |
| sample-data menus | Dict keyed by location (list form is ignored by seeder) |
| Page filenames | English **and domain-native** (school `Programs`, `PPDB`, dll.) |

Ringkas: About/Contact(Reach slug)/Blog/Search/menus/SEO/sample-data/disabled enums align; section archetype sekolah (PPDB, jurusan/program, dll.) tetap SoC paket.
