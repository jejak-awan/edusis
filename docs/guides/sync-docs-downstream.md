# How-to: Sync dokumentasi ke instansi deployment sekolah

> [!NOTE]
> **Konteks JA-EduSIS:** Guide ini berbeda dari panduan sync `ja-core_engine`. JA-EduSIS adalah produk SIS mandiri — **tidak** memiliki downstream portal CMS (`k2net-portal`, `smkn6-portal`, dsb). Docs dari repositori ini tidak di-mirror ke portal CMS.

## Deployment ke `ja-srv`

Deploy artefak build ke server produksi EduSIS dilakukan via publish script dari `ja-dev`:

```bash
bash /home/jejakawan/dev/docs/configs/publish-from-jadev.sh
```

Atau sync manual sesuai mandat workspace:

```bash
rsync -avz /home/jejakawan/dev/ja-edusis/ ja-srv:/home/jejakawan/www/edusis/
```

## Sync docs ops (multi-host)

Ikuti [AGENTS.md](../../AGENTS.md) bagian 1. `ja-srv` menerima pohon `docs/` lengkap. `ja-aksara` hanya menerima `docs/ja-aksara/`, `docs/ja-labaksara/`, `docs/README.md`, dan topologi di `docs/platform/` (tanpa `jejakawan_cred.md` dan tanpa skrip publish `ja-srv`). Jangan `rsync` seluruh `docs/` ke `ja-aksara`.

## Related

- Kebijakan: [DOCUMENTATION.md](../DOCUMENTATION.md)
- Completeness: [COMPLETENESS.md](../COMPLETENESS.md)
- Branching multi-repo: [branching.md](../branching.md)
