#!/usr/bin/env bash
set -euo pipefail

# Simple production deploy for a single DigitalOcean Droplet.
# Assumes you are in the project directory on the server.

COMPOSE_FILE="docker-compose.prod.yml"

echo "Building + starting containers (prod)..."
docker compose -f "$COMPOSE_FILE" up -d --build

echo "Showing container status..."
docker compose -f "$COMPOSE_FILE" ps

echo "Done. App should be on http://<SERVER_IP>/"

echo "If this is first deploy and DB is empty, run once:"
echo "  RUN_MIGRATIONS_ON_STARTUP=true RUN_SEED_ON_STARTUP=true docker compose -f $COMPOSE_FILE up -d"
