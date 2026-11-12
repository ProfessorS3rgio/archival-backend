# Droplet deployment

Production runs from `/var/www/archival-backend` on branch `codex/r2-storage`.
Use Git to transfer code. Keep `.env` on each machine; never copy the local
`.env` to production. R2 holds private documents under `archival/private/`.

The Staff access update requires no database migration. Rebuild frontend assets
and refresh cached configuration using the normal deployment steps below.
Document APIs now require a signed-in session or a Sanctum bearer token with
`documents:read`; configure external integrations accordingly before deployment.
Staff accounts can review and download all records, while edit and delete
actions remain limited to records they created. Administrative and log pages are
hidden and protected on the server.

The Sangguniang public website integration requires the `api_clients` migration.
After pulling the code, run the migration before issuing its read token:

```sh
runuser -u www-data -- php artisan migrate --force --path=database/migrations/2026_11_12_010000_create_api_clients_table.php
runuser -u www-data -- php artisan archival:issue-api-token archive-client@YOUR_DOMAIN --name="Sangguniang Panlalawigan" --expires=90
```

Place the printed token only in the other system's backend environment. See
`API_INTEGRATION.md` for requests, rotation, and visibility checks. Do not run
the issuer until the code is deployed and the migration succeeds.

Apply the additive API filter indexes after pulling the code:

```sh
runuser -u www-data -- php artisan migrate --force --path=database/migrations/2026_11_12_020000_add_api_filter_indexes.php
```

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
install -d -m 755 public/vendor/log-viewer
chown www-data:www-data public/vendor/log-viewer
runuser -u www-data -- php artisan log-viewer:publish
npm ci
python3 -m venv scripts/.venv
scripts/.venv/bin/python -m pip install --upgrade pip
scripts/.venv/bin/python -m pip install -r scripts/requirements.txt
runuser -u www-data -- php artisan down --retry=60
npm run build
runuser -u www-data -- php artisan config:cache
runuser -u www-data -- php artisan view:cache
runuser -u www-data -- php artisan up
curl --fail https://sibugaydevgroup.tech/up
```

When `deploy/php/99-archival-uploads.ini` changes, apply the PHP-FPM upload
limits after pulling (the default 2 MB limit is lower than the form's 10 MB):

```sh
install -m 644 deploy/php/99-archival-uploads.ini /etc/php/8.3/fpm/conf.d/99-archival-uploads.ini
php-fpm8.3 -t
systemctl reload php8.3-fpm
```

Resolve unexpected tracked changes before pulling. If a step fails after entering
maintenance mode, fix that failure before bringing the app up. Run database
migrations only when the deployment requires them; do not import SQL backups
as part of a routine code update.

For the Administrative Cases / Boundary Disputes update, take a production
database backup using the server's existing backup procedure. While in
maintenance mode, after pulling and before `artisan up`, run:

```sh
runuser -u www-data -- php artisan migrate --force --path=database/migrations/2026_11_11_000000_add_parties_to_administrative_cases_and_boundary_disputes.php
```

This adds nullable `respondent` and `complainant` columns to both existing
document tables. Existing records, file paths, IDs, routes, and metadata are
retained. Older records show `N/A` until parties are entered. Fresh installations
receive the same columns through this separate migration;
the original create migrations remain unchanged. The migration skips columns
that already exist. Do not run `migrate:fresh` on
production. Rolling back this migration removes the party columns and their
values; keep a database backup if those values need to be restored.

For user profile pictures, run the following in maintenance mode after pulling
and before `artisan up`:

```sh
runuser -u www-data -- php artisan migrate --force --path=database/migrations/2026_11_11_010000_add_profile_picture_path_to_users.php
```

This adds an optional picture path to each user without changing existing
accounts. Pictures use the `private` disk (R2 in production) under
`user-profiles/` and are served through the authenticated `/profile/picture`
route. Users without a picture see their initials beside the role dropdown.

Officials' profile pictures use the same `private` disk as documents (R2 in
production), under `private/sp-profiles/`. The picture record stores its disk;
older records default to `public` until copied. Images are served through the
authenticated `/official-profile-pictures/{picture}` route.

For the profile-picture storage deployment, run these while in maintenance mode,
after pulling and before `artisan up`:

```sh
runuser -u www-data -- php artisan migrate --force --path=database/migrations/2026_11_10_060000_add_disk_to_sp_profile_pictures.php
runuser -u www-data -- php artisan officials:migrate-pictures --dry-run
runuser -u www-data -- php artisan officials:migrate-pictures
```

The copy command verifies SHA-256 contents before changing each database record,
retains the original local file as a backup, and can be rerun safely.

`public/build` is generated on the server and ignored by Git. Keep runtime
storage, installed dependencies, certificates, and production credentials on
the server. `/root/archival-backend` is an older checkout and is not the live app.

The host Nginx configuration is tracked at `deploy/nginx/archival-backend.conf`.
When that file changes, apply it after pulling:

```sh
cp /etc/nginx/sites-available/archival-backend /root/archival-nginx.previous.conf
install -m 644 deploy/nginx/archival-backend.conf /etc/nginx/sites-available/archival-backend
nginx -t && systemctl reload nginx
```

The default HTTP host redirects the droplet IP and other hostnames to
`https://sibugaydevgroup.tech`, preserving the path and query string. This
redirect does not hide the origin IP or restrict origin access to Cloudflare.
