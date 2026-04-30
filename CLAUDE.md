# wp-reseller-store

## Security baseline

### Accepted moderate vulnerabilities (as of April 2026)

`npm audit` reports 8 moderate vulnerabilities that **cannot be fixed without breaking version downgrades**:

| Advisory | Package | Root cause |
|---|---|---|
| GHSA-2g4f-4pwh-qvx6 | `ajv` (ReDoS via `$data` option) | transitive dep of `@wordpress/env` via `@wp-playground/*` |
| GHSA-w5hq-g745-h8pq | `uuid` < 14.0.0 (missing buffer bounds check) | transitive dep of `cypress` via `@cypress/request` |

Fixes require `@wordpress/env@10.38.0` (breaking) or `cypress@4.2.0` (breaking, ancient). Both are **dev-only** dependencies with no production exposure.

Re-evaluate when `@wordpress/env` or `cypress` publish patch releases that resolve these transitively.
