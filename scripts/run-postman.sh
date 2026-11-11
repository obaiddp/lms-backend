#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COLLECTION="$ROOT_DIR/postman/lms-backend.postman_collection.json"
ENVIRONMENT="$ROOT_DIR/postman/lms-backend.postman_environment.json"

if ! command -v npx >/dev/null 2>&1; then
  echo "npx is required to run the Postman suite." >&2
  exit 1
fi

exec npx --yes newman run "$COLLECTION" \
  --environment "$ENVIRONMENT" \
  --env-var "base_url=${BASE_URL:-http://localhost}" \
  --reporters cli,junit \
  --reporter-junit-export "$ROOT_DIR/postman/newman-results.xml" \
  "$@"
