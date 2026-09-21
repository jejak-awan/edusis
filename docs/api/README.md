# Generated OpenAPI artifacts

Folder ini menampung **output** unified exporter.

```bash
npm run docs:openapi
# = bash scripts/export-openapi.sh
```

| Artifact | Generator |
| :--- | :--- |
| `console.openapi.json` | Dedoc Scramble (`php artisan scramble:export`) |
| `INDEX.md` | Ditulis ulang tiap export |

SoT API = kode + Scramble annotations / field definitions — **bukan** file JSON di sini.  
Jangan edit JSON tangan. Setelah ubah route/model: regenerate.

Cara pakai: [reference/http-api.md](../reference/http-api.md).  
UI live Scramble: `{APP_URL}/docs/api` (akses terbatas).
