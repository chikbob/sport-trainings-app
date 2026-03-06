# Quick Start Guide - Docker Development Setup

Your Laravel application with Inertia + Vue 3 + Vite is now fully containerized. Here's how to work with it:

## 🚀 Start Development

```bash
# Start all containers
docker compose up -d

# Check status
docker compose ps

# Access the application
#  - Frontend: http://localhost
#  - Vite dev server: http://localhost:5173
```

## 📝 Common Tasks

### Initialize the Application

```bash
# First time only - copy environment file
docker compose exec app cp .env.example .env

# Generate application key
docker compose exec app php artisan key:generate

# Clear caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

### Database Management

```bash
# Run migrations (auto-runs on startup, but manual run if needed)
docker compose exec app php artisan migrate

# Fresh migration (reset database)
docker compose exec app php artisan migrate:fresh

# Seed database with sample data
docker compose exec app php artisan migrate:fresh --seed

# Check migration status
docker compose exec app php artisan migrate:status

# Rollback migrations
docker compose exec app php artisan migrate:rollback
```

### Frontend Development

The Vite development server runs automatically in the `vite` container on **port 5173**.

**In your blade templates, use Vite directives:**
```blade
@vite('resources/js/app.js')
```

**Watch for changes:**
- Changes to `resources/` are automatically picked up by Vite
- Frontend assets are hot-reloaded at http://localhost:5173

### Artisan Commands

```bash
# Run any Laravel command
docker compose exec app php artisan <command>

# Examples:
docker compose exec app php artisan tinker          # Interactive shell
docker compose exec app php artisan queue:work      # Run jobs
docker compose exec app php artisan storage:link    # Link storage
docker compose exec app php artisan make:migration  # Create migration
docker compose exec app php artisan make:model      # Create model
```

### Database Access

```bash
# Access MySQL directly
docker compose exec db mysql -u laravel -psecret laravel

# Or with root:
docker compose exec db mysql -u root -psecret

# Example query:
# SHOW TABLES;
# SELECT * FROM users;
```

### Cache & Configuration

```bash
# Clear application cache
docker compose exec app php artisan cache:clear

# Cache configuration (production)
docker compose exec app php artisan config:cache

# Cache routes (production)
docker compose exec app php artisan route:cache

# Clear view cache
docker compose exec app php artisan view:clear
```

## 📂 Project Structure

```
.
├── app/                        # Application code
├── resources/
│   ├── js/                     # Vue components & scripts
│   ├── views/                  # Blade templates
│   └── css/                    # Stylesheets (with Tailwind)
├── database/
│   ├── migrations/             # Database migrations
│   ├── factories/              # Model factories
│   └── seeders/                # Database seeders
├── routes/                     # Route definitions
├── docker/                     # Docker configs
│   ├── nginx/                  # Nginx configuration
│   ├── php/                    # PHP configuration
│   └── supervisor/             # Process manager
├── Dockerfile                  # Multi-stage build
├── docker-compose.yml          # Development stack
└── vite.config.js             # Vite bundler config
```

## 🐳 Docker Services

| Service | Image | Port | Purpose |
|---------|-------|------|---------|
| **app** | PHP 8.1-FPM | 9000 | Laravel application |
| **nginx** | nginx:alpine | 80, 443 | Web server & reverse proxy |
| **db** | mysql:8.0 | 3306 | MySQL database |
| **redis** | redis:7-alpine | 6379 | Cache & queue |
| **vite** | PHP 8.1 + Node | 5173 | Frontend dev server |
| **queue** | PHP 8.1-FPM | - | Background job worker |

## 🔍 Viewing Logs

```bash
# View all logs
docker compose logs -f

# Specific service logs
docker compose logs -f app          # Application
docker compose logs -f nginx        # Web server
docker compose logs -f db           # Database
docker compose logs -f vite         # Vite dev server

# Last 50 lines
docker compose logs --tail=50 app
```

## 🛑 Stop & Clean Up

```bash
# Stop containers (data persists)
docker compose down

# Remove everything including volumes (⚠️ data is lost)
docker compose down -v

# Restart services
docker compose restart

# Restart specific service
docker compose restart app
```

## 🔧 Troubleshooting

### "Connection refused" on port 80?
Make sure no other service is using port 80:
```bash
# Check what's using port 80
lsof -i :80

# Or change Nginx port in docker-compose.yml:
ports:
  - "8080:80"  # Now access via http://localhost:8080
```

### Vite assets not loading?
```bash
# Clear Vite cache
docker compose down
docker compose up -d

# Or rebuild Vite:
docker compose exec vite npm run build
```

### Database connection errors?
```bash
# Check database status
docker compose exec db mysqladmin ping -u root -psecret

# Check logs
docker compose logs db

# Restart database
docker compose restart db
```

### Permission errors in storage?
```bash
# Fix permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### "Container is restarting"?
```bash
# View error logs
docker compose logs app

# Check if migrations are failing
docker compose logs app | grep -i "error\|fail"
```

## 📋 Environment Variables

Located in `.env`. Key variables:

```env
APP_NAME=Sport Training
APP_ENV=development
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

Change `APP_ENV=development` to `APP_ENV=production` and `APP_DEBUG=false` for production.

## 🚀 For Production Deployment

See `DEPLOYMENT.md` for:
- Docker Compose for simple production deployments
- Kubernetes manifests for scalable deployments
- GitHub Actions CI/CD pipeline
- Production security best practices

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Vite Documentation](https://vitejs.dev/)
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/reference/)

---

**Happy coding!** 🎉

Sources: https://docs.docker.com/, https://docs.docker.com/compose/, https://laravel.com/docs/, https://inertiajs.com/
