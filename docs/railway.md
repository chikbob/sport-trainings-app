# Railway deployment

This project is prepared for Railway deployment with a Dockerfile build.

## Added files

- `railway.toml`
- `scripts/railway-predeploy.sh`

## Why the deploy was failing

Railway was executing a pre-deploy command that called `php artisan optimize:clear`.

Laravel's `config/view.php` used:

```php
realpath(storage_path('framework/views'))
```

On a fresh container, if `storage/framework/views` does not exist yet, `realpath(...)` returns `false`.  
Then `view:clear` fails with:

`RuntimeException: View path not found.`

This is fixed by using:

```php
storage_path('framework/views')
```

instead of `realpath(...)`.

## Railway config

`railway.toml` pins deployment to the repository `Dockerfile` and configures:

- healthcheck: `/health`
- start command: runs `/entrypoint.sh` and then `supervisord`
- restart policy
- pre-deploy command

The runtime image is also adapted for Railway:

- nginx listens on `PORT` from Railway
- nginx talks to PHP-FPM over `127.0.0.1:9000` inside the same container
- the nginx server block is rendered at startup from `docker/nginx/runtime.conf.template`
- the start command is explicit so stale dashboard start-command overrides do not bypass the Docker entrypoint

## Pre-deploy command

`scripts/railway-predeploy.sh`:

1. creates Laravel runtime directories
2. removes bootstrap cache files directly from disk
3. clears compiled Blade views
4. runs `php artisan migrate --force`

This avoids `cache:clear` against Redis during pre-deploy. Railway may start the build/deploy container before a Redis service is reachable, so clearing the application cache store during deployment is fragile.

## Recommended Railway variables

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=...`
- `APP_URL=https://<your-domain-or-railway-domain>` or rely on `RAILWAY_PUBLIC_DOMAIN`
- `DB_CONNECTION=pgsql` or `mysql` depending on your Railway database
- `DATABASE_URL=...` if using Railway-provided connection string
- `CACHE_DRIVER=redis`
- `SESSION_DRIVER=redis`
- `QUEUE_CONNECTION=redis`
- `REDIS_URL=...`

If Redis is not attached on Railway yet, use:

- `CACHE_DRIVER=file`
- `SESSION_DRIVER=file`
- `QUEUE_CONNECTION=database` or `sync`

## Notes

- Railway can build from the root `Dockerfile` automatically, but `railway.toml` makes that explicit.
- Railway injects a `PORT` variable and expects the service to listen on it; this is now handled automatically in the entrypoint.
- If Railway uses a separate worker service, use the same image and set its start command in the Railway dashboard to:

```sh
php artisan queue:work --sleep=3 --tries=3
```
