#!/usr/bin/env bash
# test-matrix.sh — Install reseller-store.zip against a WP × PHP version matrix.
#
# Usage:
#   bash bin/test-matrix.sh          # full matrix (WP 6.2/6.5/6.7/6.8 × PHP 8.1/8.2/8.3)
#   bash bin/test-matrix.sh --quick  # 4 combos only (corners of the matrix)

set -euo pipefail

# Resolve wp-env binary — check PATH first, then known nvm locations
WP_ENV=""
if command -v wp-env &>/dev/null; then
  WP_ENV="$(command -v wp-env)"
else
  # Walk nvm node versions looking for a global wp-env install
  for node_dir in "$HOME"/.nvm/versions/node/*/lib/node_modules/@wordpress/env/bin/wp-env; do
    [[ -x "$node_dir" ]] && WP_ENV="$node_dir" && break
  done
fi
[[ -n "$WP_ENV" && -x "$WP_ENV" ]] || { echo "wp-env not found — run: npm install -g @wordpress/env"; exit 1; }

# Resolve the node binary that pairs with this wp-env
NODE_BIN="$(dirname "$(dirname "$WP_ENV")")/../../../../bin/node"
[[ -x "$NODE_BIN" ]] || NODE_BIN="node"
export WP_ENV NODE_BIN

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ZIP="$PROJECT_DIR/reseller-store.zip"
ENV_JSON="$PROJECT_DIR/.wp-env.json"
ENV_JSON_BAK="$PROJECT_DIR/.wp-env.json.bak"

# wp-env hash is derived from the project path — stable regardless of config changes
WP_ENV_HASH="0480f382487ccc16564602afe4937cab"
WP_CONTENT="$HOME/.wp-env/$WP_ENV_HASH/WordPress/wp-content"

RESULTS=()
PASS=0
FAIL=0

# ── Matrix ─────────────────────────────────────────────────────────────────────
WP_VERSIONS=("6.2" "6.5" "6.7" "6.8")
PHP_VERSIONS=("8.1" "8.2" "8.3")

if [[ "${1:-}" == "--quick" ]]; then
  WP_VERSIONS=("6.2" "6.8")
  PHP_VERSIONS=("8.1" "8.3")
fi

# ── Helpers ───────────────────────────────────────────────────────────────────
red()   { printf '\033[31m%s\033[0m\n' "$*"; }
green() { printf '\033[32m%s\033[0m\n' "$*"; }
cyan()  { printf '\033[36m%s\033[0m\n' "$*"; }
bold()  { printf '\033[1m%s\033[0m\n' "$*"; }

cli() { "$WP_ENV" run cli -- "$@" 2>&1; }

# ── Restore original config on exit ───────────────────────────────────────────
cleanup() {
  echo ""
  bold "Restoring original .wp-env.json …"
  [[ -f "$ENV_JSON_BAK" ]] && mv "$ENV_JSON_BAK" "$ENV_JSON"
  rm -f "$WP_CONTENT/reseller-store.zip"
  cd "$PROJECT_DIR" && "$WP_ENV" start --update 2>&1 | grep -E "✔|✗|Error" || true
}
trap cleanup EXIT

# ── Pre-flight ────────────────────────────────────────────────────────────────
[[ ! -f "$ZIP" ]] && { red "✗  reseller-store.zip not found — run: npm run build first"; exit 1; }

bold ""
bold "════════════════════════════════════════════════════"
bold "  Reseller Store v2.2.17 — WP × PHP install matrix"
bold "════════════════════════════════════════════════════"
echo "  ZIP     : $ZIP ($(du -sh "$ZIP" | cut -f1))"
echo "  WP      : ${WP_VERSIONS[*]}"
echo "  PHP     : ${PHP_VERSIONS[*]}"
echo "  Combos  : $(( ${#WP_VERSIONS[@]} * ${#PHP_VERSIONS[@]} ))"
echo ""

cp "$ENV_JSON" "$ENV_JSON_BAK"

# ── Matrix loop ───────────────────────────────────────────────────────────────
for WP in "${WP_VERSIONS[@]}"; do
  for PHP in "${PHP_VERSIONS[@]}"; do
    LABEL="WP ${WP} / PHP ${PHP}"
    cyan "── ${LABEL} ──────────────────────────────────"

    # 1. Write config (no plugin source mount — install from zip only)
    cat > "$ENV_JSON" <<JSON
{
  "core": "WordPress/WordPress#${WP}",
  "phpVersion": "${PHP}",
  "testsEnvironment": false
}
JSON

    # 2. Start / restart the environment
    printf "  [1/4] Starting wp-env … "
    START_OUT=$("$WP_ENV" start --update 2>&1 || true)
    if echo "$START_OUT" | grep -qE "Done!|WordPress.*started|already running"; then
      echo "ok"
    else
      echo "FAILED"
      echo "$START_OUT" | tail -5
      RESULTS+=("FAIL|${LABEL}|wp-env start failed")
      FAIL=$(( FAIL + 1 ))
      continue
    fi

    # 3. Copy zip into the mounted wp-content dir
    printf "  [2/4] Copying zip … "
    cp "$ZIP" "$WP_CONTENT/reseller-store.zip"
    echo "done"

    # 4. Install + activate via WP-CLI
    printf "  [3/4] Installing plugin … "
    INSTALL=$(cli wp plugin install /var/www/html/wp-content/reseller-store.zip --force --activate) || true

    if echo "$INSTALL" | grep -qiE "installed|updated"; then
      echo "ok"
    else
      echo ""
      ERR=$(echo "$INSTALL" | grep -iE "error|fatal" | head -1)
      RESULTS+=("FAIL|${LABEL}|${ERR:-install command failed}")
      FAIL=$(( FAIL + 1 ))
      red "       ✗ ${ERR:-install command failed}"
      rm -f "$WP_CONTENT/reseller-store.zip"
      continue
    fi

    # 5. Verify plugin is active
    printf "  [4/4] Verifying activation … "
    STATUS=$(cli wp plugin status reseller-store) || true
    PHP_VER=$(cli php -r "echo PHP_VERSION;" 2>/dev/null || echo "?")
    WP_VER=$(cli wp core version 2>/dev/null || echo "?")

    if echo "$STATUS" | grep -qi "Status:.*Active\|is active"; then
      echo "active ✓"
      RESULTS+=("PASS|${LABEL}|WP ${WP_VER} / PHP ${PHP_VER}")
      PASS=$(( PASS + 1 ))
      green "  ✓  ${LABEL}  →  WP ${WP_VER} / PHP ${PHP_VER}"
    else
      STATE=$(echo "$STATUS" | grep -i "Status:" | head -1)
      RESULTS+=("FAIL|${LABEL}|not active — ${STATE}")
      FAIL=$(( FAIL + 1 ))
      red "  ✗  ${LABEL}  →  not active: ${STATE}"
    fi

    # Clean up for next iteration
    cli wp plugin deactivate reseller-store 2>/dev/null || true
    cli wp plugin delete reseller-store      2>/dev/null || true
    rm -f "$WP_CONTENT/reseller-store.zip"
    echo ""
  done
done

# ── Summary ───────────────────────────────────────────────────────────────────
bold "════════════════════════════════════════════════════"
bold "  Results: ${PASS} passed, ${FAIL} failed"
bold "════════════════════════════════════════════════════"
for r in "${RESULTS[@]}"; do
  STATUS="${r%%|*}"
  REST="${r#*|}"
  COMBO="${REST%%|*}"
  NOTE="${REST#*|}"
  if [[ "$STATUS" == "PASS" ]]; then
    green "  ✓  ${COMBO}  (${NOTE})"
  else
    red   "  ✗  ${COMBO}  — ${NOTE}"
  fi
done
echo ""
[[ $FAIL -gt 0 ]] && exit 1 || exit 0
