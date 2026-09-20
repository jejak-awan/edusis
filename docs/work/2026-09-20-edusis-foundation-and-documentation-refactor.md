---
id: 2026-09-20-edusis-foundation-and-documentation-refactor
title: Inisialisasi Fondasi JA-EduSIS & Refactoring Dokumentasi Domain
status: done
scale: M
repo: ja-edusis
owner: agent
created: 2026-09-20
updated: 2026-09-20
blocks: []
related_adr:
  - ADR-029
---

# Task: Inisialisasi Fondasi JA-EduSIS & Refactoring Dokumentasi Domain

## 1. Tujuan
1. Menginisialisasi repositori mandiri `ja-edusis` dari fondasi `ja-core_engine`.
2. Merampingkan modul yang tidak diperlukan untuk SIS (`Newsletter`, `visual-builder`, `data-studio`, `cinematic-nav`) sambil mempertahankan modul penting untuk portal publik sekolah (`CmsAi`, `instagram-feed`, `floating-social-dock`).
3. Menyiapkan database PostgreSQL pada skema mandiri terisolasi `edusis` (`DB_SCHEMA=edusis`).
4. Menata sistem dokumentasi dengan pola *Dual-Layer Documentation* (`docs/sis/` untuk domain sekolah, `docs/architecture/` untuk engine heritage) serta mencatat ADR-029.

## 2. Hasil
- Database PostgreSQL terisolasi pada skema `edusis` dengan 92 tabel ter-migrate dan di-seed akun super admin.
- Kompilasi `vue-tsc -b` dan `i18n:check` lolos 100% tanpa error.
- Dokumentasi `docs/sis/` (overview, multi-repo synthesis, strategi KataMPI, standar data Dapodik, dan roadmap) selesai ditulis.
- ADR-029 resmi dicatat di `docs/adr/ADR-029-edusis-domain-and-multi-repo-synthesis.md`.
