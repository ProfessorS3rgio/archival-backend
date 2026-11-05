# Deployment conventions

- Use Git to synchronize application code with the droplet; do not copy code
  files directly with SCP or rsync.
- The R2 integration branch is `codex/r2-storage`. Production runs from
  `/var/www/archival-backend` on `152.42.229.207`.
- Keep `.env` separate for local development and production. Never commit it
  or overwrite the production file with the local file.
- Follow `DEPLOYMENT.md` for deployment commands. Build frontend assets on the
  server; keep dependencies, generated assets, certificates, and runtime data
  outside Git synchronization.
