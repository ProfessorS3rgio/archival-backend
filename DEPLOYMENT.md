# Droplet deployment

Production runs from `/var/www/archival-backend` on branch `codex/r2-storage`.
Use Git to transfer code. Keep `.env` on each machine; never copy the local
`.env` to production. R2 holds private documents under `archival/private/`.

Commit and push changes locally:

```sh
git switch codex/r2-storage
git add <changed-code-files>
git commit -m "Describe the change"
git push origin codex/r2-storage
```

Deploy from the droplet as root:

```sh
cd /var/www/archival-backend
git status --short
git pull --ff-only origin codex/r2-storage
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci
runuser -u www-data -- php artisan down --retry=60
npm run build
runuser -u www-data -- php artisan config:cache
runuser -u www-data -- php artisan view:cache
runuser -u www-data -- php artisan up
curl --fail https://sibugaydevgroup.tech/up
```

Resolve unexpected tracked changes before pulling. If a step fails after entering
maintenance mode, fix that failure before bringing the app up. Run database
migrations only when the deployment requires them; do not import SQL backups
as part of a routine code update.

`public/build` is generated on the server and ignored by Git. Keep runtime
storage, installed dependencies, certificates, and production credentials on
the server. `/root/archival-backend` is an older checkout and is not the live app.
