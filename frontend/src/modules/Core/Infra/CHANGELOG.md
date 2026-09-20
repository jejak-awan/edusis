# Changelog — Core / Infra (FE)

Infra console surfaces under `modules/Core/Infra` (Data Studio UI hooks, infra dashboards when present).

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

Index: [`../CHANGELOG.md`](../CHANGELOG.md)

## [Unreleased]

### Added
- Data Model Studio / infra console wiring tracked with BE ADR-012 when FE surfaces change.

### Changed
- Refactored `infraNavigation` and router to only mount active File Manager surfaces.

### Removed
- Removed Data Model Studio and Dynamic Record frontend views, components, services, routes, and console search/sidebar integrations.

### Fixed
