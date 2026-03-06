# Deployment Pipeline Documentation

This Laravel application has a complete deployment pipeline configured for development, testing, and production environments.

## Quick Start

### Local Development with Docker Compose

```bash
# Copy environment file
cp .env.example .env

# Start all services
docker compose up -d --pull always

# Run database migrations
docker compose exec app php artisan migrate

# Access the application
# http://localhost
```

### Build Docker Image Locally

```bash
docker build -t laravel-app:latest .
```

## Architecture

### Docker Stack

- **PHP-FPM**: Application runtime (PHP 8.1)
- **Nginx**: Reverse proxy and static asset server
- **MySQL**: Database (8.0)
- **Redis**: Cache and queue backend (7)
- **Queue Worker**: Background job processor (optional)

### Services Configuration

| Service | Image | Port | Purpose |
|---------|-------|------|---------|
| app | php:8.1-fpm | 9000 | PHP-FPM application |
| nginx | nginx:alpine | 80/443 | HTTP reverse proxy |
| db | mysql:8.0 | 3306 | Database |
| redis | redis:7-alpine | 6379 | Cache/Queue |
| queue | php:8.1-fpm | - | Queue worker |

## Deployment Methods

### 1. Docker Compose (Simple Deployment)

Use `docker-compose.yml` for single-host production deployments.

```bash
# Deploy
docker compose -f docker-compose.yml up -d

# View logs
docker compose logs -f app

# Scale services (for queue workers)
docker compose up -d --scale queue=3
```

### 2. Kubernetes (Multi-Node Deployment)

Use `k8s/manifests.yaml` for production clusters.

**Prerequisites:**
- Kubernetes cluster (1.20+)
- kubectl CLI configured
- Docker images pushed to registry

**Deploy:**
```bash
# Create namespace and deploy
kubectl apply -f k8s/manifests.yaml

# Wait for rollout
kubectl rollout status deployment/laravel-app -n laravel-app

# View pods
kubectl get pods -n laravel-app

# Check logs
kubectl logs -f deployment/laravel-app -n laravel-app

# Port forward for local access
kubectl port-forward -n laravel-app svc/laravel-app 8080:80
```

### 3. GitHub Actions CI/CD

The pipeline (`.github/workflows/ci-cd.yml`) automates:
- **Test**: Runs PHPUnit tests and code style checks on every push
- **Build**: Builds and pushes Docker image to ghcr.io on main branch
- **Deploy**: Automatically deploys to production via SSH

**Setup:**
1. Add GitHub secrets:
   - `DEPLOYMENT_KEY`: SSH private key (RSA format)
   - `DEPLOYMENT_HOST`: Server hostname/IP
   - `DEPLOYMENT_USER`: SSH user for deployment
   
2. Create GitHub environment named `production`

3. Update deploy script in CI/CD pipeline with your server details

## Configuration

### Environment Variables

Create `.env` from `.env.example`:

```bash
cp .env.example .env
```

Key variables:
```env
APP_NAME=Laravel
APP_DEBUG=false
APP_ENV=production

DB_HOST=db
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=your-secure-password

REDIS_HOST=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Docker Build Cache

The Dockerfile uses a multi-stage build for optimization:
- **Stage 1 (builder)**: Installs dependencies and builds assets
- **Stage 2 (runtime)**: Minimal image with only runtime requirements

This reduces image size from ~2GB to ~500MB.

## Monitoring & Troubleshooting

### Health Checks

All containers include health checks:

```bash
# Check container health
docker compose ps

# Or with Docker directly
docker inspect laravel_app --format='{{.State.Health.Status}}'
```

### View Logs

```bash
# Docker Compose
docker compose logs app        # App logs
docker compose logs db         # Database logs
docker compose logs -f         # Follow all logs

# Kubernetes
kubectl logs deployment/laravel-app -n laravel-app
kubectl logs -f pod/<pod-name> -n laravel-app

# Tail last 100 lines
kubectl logs -n laravel-app deployment/laravel-app --tail=100
```

### Database Migrations

```bash
# Docker Compose
docker compose exec app php artisan migrate

# Kubernetes
kubectl exec -it deployment/laravel-app -n laravel-app -- php artisan migrate

# Rollback
docker compose exec app php artisan migrate:rollback
```

### Clear Cache

```bash
# Docker Compose
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# Kubernetes
kubectl exec deployment/laravel-app -n laravel-app -- php artisan cache:clear
```

## Production Considerations

### Security

- Nginx security headers configured
- HTTPS enabled (use reverse proxy or certificates)
- Database credentials in secrets (not image)
- Health checks for automatic restart

### Scaling

**Docker Compose:**
```bash
# Scale queue workers
docker compose up -d --scale queue=5
```

**Kubernetes:**
```bash
# Manual scale
kubectl scale deployment laravel-app -n laravel-app --replicas=5

# Automatic scaling via HPA
kubectl get hpa -n laravel-app
```

### Backups

Database volumes persist in `./storage/db` (Compose) or PVC (Kubernetes).

```bash
# Backup database
docker compose exec db mysqldump -u root -p$DB_PASSWORD laravel > backup.sql

# Restore
docker compose exec -T db mysql -u root -p$DB_PASSWORD laravel < backup.sql
```

## Performance Optimization

### Caching Strategy

1. **Config Caching**: `php artisan config:cache`
2. **Route Caching**: `php artisan route:cache`
3. **View Caching**: `php artisan view:cache`
4. **Redis for sessions/cache**: Configured in `.env`

### Asset Optimization

- Assets built with Vite during Docker build
- Browser caching configured (1 year for versioned assets)
- Gzip compression enabled in Nginx

### Resource Limits

Kubernetes includes resource requests/limits to prevent resource exhaustion.

## Rollback & Recovery

### Docker Compose

```bash
# Stop current deployment
docker compose down

# Pull previous image
docker pull ghcr.io/your-org/your-repo:previous-tag

# Start with previous version
docker compose up -d
```

### Kubernetes

```bash
# View rollout history
kubectl rollout history deployment/laravel-app -n laravel-app

# Rollback to previous version
kubectl rollout undo deployment/laravel-app -n laravel-app
```

## File Structure

```
.
├── Dockerfile                          # Multi-stage build
├── docker-compose.yml                  # Local & simple production
├── .dockerignore                       # Optimize build context
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf                  # Nginx config
│   │   └── default.conf                # Site config
│   ├── php/
│   │   ├── php.ini                     # PHP settings
│   │   └── www.conf                    # PHP-FPM pool
│   ├── supervisor/
│   │   └── supervisord.conf            # Supervisor config
│   └── entrypoint.sh                   # Container startup script
├── k8s/
│   └── manifests.yaml                  # Kubernetes deployment
├── .github/
│   └── workflows/
│       └── ci-cd.yml                   # GitHub Actions pipeline
└── app/
    ├── routes/                         # Laravel routes
    ├── app/                            # Application code
    └── ...
```

## Support & Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/reference/)
- [Kubernetes Documentation](https://kubernetes.io/docs/)
- [Laravel Documentation](https://laravel.com/docs/)

Sources: https://docs.docker.com/, https://docs.docker.com/compose/, https://kubernetes.io/docs/
