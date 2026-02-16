# ClearChange – Docker / Podman

Run the full stack with **Podman** and **podman-compose** (or Docker Compose). All services run in containers; no host PHP or Node required.

## Stack

- **clearchange_db**: MySQL 8.0, database `clearchange`, user `clearchange` / password `password`, port **3309** (host)
- **clearchange_app**: PHP 8.3-FPM (Laravel)
- **clearchange_queue**: Laravel queue worker (optional; use when you enable queues). Start with `--profile with-queue`
- **clearchange_web**: nginx, Laravel via PHP-FPM → **http://localhost:8082**
- **clearchange_node**: Node 20, Vite dev server → **http://localhost:5175**

For **HTTPS** and clean URLs (**https://clearchange.docker**), use the [shared proxy](../proxy/README.md) and add `clearchange.docker` to your hosts file.

## One-time setup

From the project root:

```bash
cp .env.example .env
# Edit .env: APP_URL=http://localhost:8082 (or https://clearchange.docker if using proxy)

podman compose up -d --build

podman compose exec clearchange_app php artisan key:generate
podman compose exec clearchange_app php artisan migrate --force
```

## Start / stop

```bash
podman compose up -d --build
podman compose down
```

## Queue (optional)

When you add jobs, set `QUEUE_CONNECTION=database` in `.env` and start the queue:

```bash
podman compose --profile with-queue up -d
```

## Run commands in containers

- **Artisan**: `podman compose exec clearchange_app php artisan <command>`
- **npm**: `podman compose run --rm clearchange_node npm install` or `npm run build`

## URLs

- **Without proxy**: http://localhost:8082 (web), http://localhost:5175 (Vite HMR)
- **With proxy**: https://clearchange.docker (add `127.0.0.1 clearchange.docker` to `/etc/hosts`)
