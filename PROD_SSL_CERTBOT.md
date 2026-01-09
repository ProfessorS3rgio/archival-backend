# Production SSL (DigitalOcean Droplet + Docker + Certbot)

This project can use free SSL from **Let’s Encrypt** via **Certbot**, with **Nginx** as a reverse proxy in front of the Laravel Apache container.

## Requirements

- Domain: `sibugaydevgroup.tech` (and optionally `www.sibugaydevgroup.tech`)
- DNS `A` record(s) point to your Droplet public IP
- Ports **80** and **443** allowed in:
  - DigitalOcean Cloud Firewall (if enabled)
  - `ufw` / server firewall (if enabled)

## What’s included in this repo

- Production compose updates in `docker-compose.prod.yml`
  - `nginx` reverse proxy publishes ports **80/443**
  - `certbot` container issues/renews certs
  - `app` is no longer published directly to the internet
- Nginx configs:
  - `docker/nginx/conf.d/app-http.conf` (HTTP + ACME challenge)
  - `docker/nginx/conf.d/app-https.conf.disabled` (HTTPS; enable after cert issuance)

## Step 1 — Bring up production stack (HTTP)

On the Droplet:

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

Confirm your site loads over HTTP:

- `http://sibugaydevgroup.tech`

## Step 2 — Issue the certificate (webroot method)

```bash
docker compose -f docker-compose.prod.yml run --rm certbot certonly \
  --webroot -w /var/www/certbot \
  -d sibugaydevgroup.tech -d www.sibugaydevgroup.tech \
  --email you@example.com \
  --agree-tos --no-eff-email
```

If this fails, the most common causes are:
- DNS not pointing to the Droplet
- Port 80 blocked
- Another service already bound to port 80

## Step 3 — Enable HTTPS in Nginx

On the Droplet:

1) Rename the HTTPS config:

```bash
mv docker/nginx/conf.d/app-https.conf.disabled docker/nginx/conf.d/app-https.conf
```

2) (Recommended) Switch HTTP to redirect to HTTPS:
- Edit `docker/nginx/conf.d/app-http.conf`
- Replace the `location / { ... }` block with:

```nginx
return 301 https://$host$request_uri;
```

3) Reload Nginx:

```bash
docker compose -f docker-compose.prod.yml restart nginx
```

Confirm:
- `https://sibugaydevgroup.tech` works

## Step 4 — Set APP_URL correctly

In your production `.env`, set:

```env
APP_URL=https://sibugaydevgroup.tech
```

This matters for signed URLs (like the Office Online DOC/DOCX preview URLs).

## Renewals

Let’s Encrypt certs expire every 90 days. Renew with:

```bash
docker compose -f docker-compose.prod.yml run --rm certbot renew
docker compose -f docker-compose.prod.yml restart nginx
```

Automate via cron on the Droplet (example):

```bash
crontab -e
```

Add:

```cron
0 3 * * * cd /path/to/archival-backend && docker compose -f docker-compose.prod.yml run --rm certbot renew && docker compose -f docker-compose.prod.yml restart nginx
```
