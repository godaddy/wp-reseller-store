# Getting Started

## Requirements

| Requirement | Version |
|---|---|
| Node.js | >= 22.13.0 |
| PHP | >= 8.1 |
| WordPress | >= 6.2 |

## Local Development with `@wordpress/env`

The preferred local development setup uses [`@wordpress/env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/), which manages a Docker-based WordPress environment automatically.

### Start the environment

```bash
npm install
npx wp-env start
```

WordPress will be available at `http://localhost:8888`. The plugin is automatically mounted and activated.

Default credentials: `admin` / `password`.

### Stop the environment

```bash
npx wp-env stop
```

### Reset (wipe database)

```bash
npx wp-env clean all
npx wp-env start
```

---

## Building Assets

All compiled assets live in `assets/`. Never edit them directly — always edit source files under `.dev/src/` and rebuild.

### JavaScript (Gutenberg blocks)

```bash
npm run js       # build + copy block.json files to assets/blocks/
```

Output: `assets/js/editor.blocks.min.js`

Block metadata is output to `assets/blocks/product/block.json` and `assets/blocks/domain-search/block.json` for PHP registration.

### CSS

```bash
npm run css      # compile SCSS → CSS (LTR + RTL, normal + minified)
```

### Full production build

```bash
npm run build    # js + css + copy plugin files to build/
```

---

## Linting

```bash
npm run lint     # ESLint on .dev/src/
```

The project uses `@wordpress/eslint-plugin` (recommended config) plus Prettier with tabs and single quotes (see `.prettierrc.json`).

---

## Architecture

### PHP

| Path | Purpose |
|---|---|
| `reseller-store.php` | Plugin entry point |
| `class-plugin.php` | Singleton bootstrap, defines constants |
| `includes/class-*.php` | Feature classes (API, blocks, display, sync, …) |
| `includes/trait-*.php` | Shared traits (Singleton, Helpers, Data) |
| `includes/widgets/` | WordPress widget implementations |
| `includes/functions/` | Global helper functions |

All PHP files use `declare(strict_types=1)` and PHP 8.1+ typed properties and return types.

### Gutenberg Blocks

| Path | Purpose |
|---|---|
| `.dev/src/blocks/product/` | Product block source |
| `.dev/src/blocks/domain-search/` | Domain Search block source |
| `assets/blocks/*/block.json` | Built block metadata (PHP registration) |

Blocks are registered via `register_block_type()` pointing at `assets/blocks/<name>/` which contains the `block.json` manifest. The PHP render callbacks delegate to the corresponding widget classes.

JavaScript uses `@wordpress/*` ESM imports (externalized by webpack — resolved to `window.wp.*` at runtime by WordPress).

### Build pipeline

```
.dev/src/index.js
  └── .dev/src/blocks/product/index.js     → @wordpress/blocks, @wordpress/element, …
  └── .dev/src/blocks/domain-search/index.js

webpack (externals: @wordpress/* → wp.*)
  └──> assets/js/editor.blocks.min.js

copyfiles
  └──> assets/blocks/*/block.json
```

---

## Docker (legacy)

If you prefer a manual Docker setup instead of `@wordpress/env`, create a `docker-compose.yml` in a separate `wp-reseller-dev/` directory:

```yml
version: '3.3'
services:
  db:
    image: mysql:8.0
    volumes:
      - db_data:/var/lib/mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: somewordpress
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
  wordpress:
    depends_on:
      - db
    image: wordpress:latest
    ports:
      - "8000:80"
    restart: always
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
    volumes:
      - "./wordpress:/var/www/html"
      - "./plugins:/var/www/html/wp-content/plugins"
volumes:
  db_data: {}
```

Run `docker compose up`, then clone this repo into the `plugins/` volume and activate it from the WordPress admin.
