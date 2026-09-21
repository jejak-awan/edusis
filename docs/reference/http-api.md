# Reference: HTTP / OpenAPI API

Dokumentasi API di JA-EduSIS berpusat pada generator Dedoc Scramble untuk seluruh route API backend.

## 1. Console / route API (Scramble)

| Item | Nilai |
| :--- | :--- |
| Paket | `dedoc/scramble` |
| Config | `backend/config/scramble.php` |
| UI docs | `/docs/api` (middleware `EnsureApiDocsAccess` — selalu butuh login **admin+**, termasuk `APP_ENV=local`) |
| Export default | `api.json` (`export_path` di config) |
| Cakupan | Route di bawah path `api` |

**Cara pakai (dev):**

1. Boot backend (`php artisan serve` atau stack lokal).
2. Login console sebagai operator **admin** ke atas (`Gate::viewApiDocs`).
3. Buka `{APP_URL}/docs/api`.

Guest diarahkan ke `/auth/console-sign-in`. Role di bawah admin mendapat **403**. JSON di `/docs/api.json` juga di-gate (guest → 403).

Ini adalah **generator resmi** untuk permukaan HTTP Laravel yang di-anotasi / diinfer Scramble — bukan salinan manual di markdown.

## 2. ~~Data Model Studio — dynamic OpenAPI~~ (Didekomisi di JA-EduSIS)

> [!WARNING]
> **Fitur ini tidak tersedia di JA-EduSIS.** Ekstensi `data-studio` telah didekomisi dari codebase EduSIS karena tidak relevan dengan domain sistem informasi sekolah (lihat ADR-012 di [ADR Reserved](../adr/README.md)). Endpoint `/api/v1/dynamic/{slug}` dan class `DynamicOpenApiBuilder` tidak aktif di repositori ini. Fitur ini masih tersedia di `ja-core_engine` untuk downstream portal CMS.

## 3. Unified exporter

Satu pintu untuk generator Scramble:

```bash
# dari root repo
npm run docs:openapi
# atau
bash scripts/export-openapi.sh
```

Menulis:

- `docs/api/console.openapi.json` — Scramble
- `docs/api/INDEX.md` — indeks artifact

## 4. Smoke tanpa OpenAPI UI

```bash
bash scripts/api-smoke.sh
bash scripts/member-security-qa.sh   # member auth surface
```

## Related

- CLI index: [cli-commands.md](cli-commands.md)
- Security how-to: [security-checks.md](../guides/security-checks.md)
- Product / OpenAPI SemVer: [release-and-versioning.md](../guides/release-and-versioning.md)
