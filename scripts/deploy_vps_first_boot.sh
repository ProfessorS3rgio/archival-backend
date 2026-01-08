#!/usr/bin/env bash
set -euo pipefail

# First boot helper: enables migrations + admin seeding once.
# Run this ONE time on a fresh server (or after wiping the DB volume).

COMPOSE_FILE="docker-compose.prod.yml"

export RUN_MIGRATIONS_ON_STARTUP=true
export RUN_SEED_ON_STARTUP=true

echo "First boot: enabling migrations + AdminUserSeeder..."
docker compose -f "$COMPOSE_FILE" up -d

echo "Now disable startup migration/seed (recommended):"
echo "  unset RUN_MIGRATIONS_ON_STARTUP RUN_SEED_ON_STARTUP"
echo "  docker compose -f $COMPOSE_FILE up -d"
