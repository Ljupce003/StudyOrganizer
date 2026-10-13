# Changelog

## v1.0.3 — 2026-10-13

### Added

- Interactive `php artisan admin:create` with hidden password confirmation,
  explicit admin role assignment, hashing, input validation, and duplicate-email
  protection. Optional name/email flags retain interactive password entry.
- Developer setup, release publishing, Compose explanations, and operational
  troubleshooting documentation using example domains and blank secret templates.
- Regression coverage for admin creation, production panel authorization,
  trusted proxy headers, and pre-bootstrap HTTP kernel resolution.

### Fixed

- Production Filament access for admin-role accounts through the `FilamentUser`
  contract; students and professors remain forbidden from the admin panel.
- HTTP startup no longer resolves Laravel configuration before the kernel has
  bootstrapped. Proxy addresses are read through cached `trustedproxy` config.
- Tinker configuration/history uses writable private storage in the image.
- README data-transfer guidance now reflects the destructive behavior of the
  legacy PostgreSQL transition script instead of claiming it transfers rows.

### Changed

- Production PostgreSQL host access defaults to `127.0.0.1:3062`, configurable
  with `STUDYORGANIZER_DB_PORT`. Containers continue using `postgres:5432`.
- Public environment examples use reserved example domains and release `1.0.3`.
- Git ignores real environment variants, SQLite data, backups, and private-key
  formats while retaining the intended public environment templates.
