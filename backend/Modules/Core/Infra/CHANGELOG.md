# Changelog — Core / Infra (BE)

Data Model Studio, automation, backups, cache/webhook infra under `Modules/Core/app/Infra`.

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

Index: [`../CHANGELOG.md`](../CHANGELOG.md)

## [Unreleased]

### Added
- **Data Model Studio modular extension**: First-party `studio.datamodel` packaging & license gating surface ([ADR-012](../../../../docs/adr/ADR-012-data-model-studio-modular-extension-and-license-gating.md)).
- Dynamic OpenAPI builder + `php artisan dynamic:openapi` / unified `scripts/export-openapi.sh`.

### Changed
- Refactored console maintenance routine to optimize active tables without dynamic content model references.

### Removed
- Removed Data Model Studio & Dynamic Records backend subsystems, controllers, models, OpenAPI exports, seeders, and dropped tables `sys_content_types` and `sys_dynamic_records`.

### Fixed
- Scramble OpenAPI: FileManager upload validation uses inline `max:`/`mimes:` helper calls (VR002).
