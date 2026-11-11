#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COLLECTION="$ROOT_DIR/postman/lms-backend.postman_collection.json"
ENVIRONMENT="$ROOT_DIR/postman/lms-backend.postman_environment.json"

if ! command -v npx >/dev/null 2>&1; then
  echo "npx is required to run the Postman suite." >&2
  exit 1
fi

BASE_URL="${BASE_URL:-http://localhost}"
if command -v curl >/dev/null 2>&1 && ! curl --silent --show-error --fail --max-time 5 \
  "${BASE_URL%/}/api/frontend/all-courses" >/dev/null; then
  cat >&2 <<EOF
Unable to reach the LMS backend at ${BASE_URL}.
Start the application first, then rerun this command, for example:
  php artisan serve --host=127.0.0.1 --port=8000
  BASE_URL=http://127.0.0.1:8000 ./scripts/run-postman.sh
EOF
  exit 1
fi

exec npx --yes newman run "$COLLECTION" \
  --environment "$ENVIRONMENT" \
  --env-var "base_url=$BASE_URL" \
  --reporters cli,junit \
  --reporter-junit-export "$ROOT_DIR/postman/newman-results.xml" \
  "$@"
