# ==========================================
# PRE-DEPLOYMENT CHECKLIST
# School Discipline SaaS
# ==========================================

## TAHAP 1: ENVIRONMENT CONFIGURATION

### WAJIB - Sebelum Deployment
1. [ ] Set `APP_ENV=production`
2. [ ] Set `APP_DEBUG=false`
3. [ ] Set `APP_KEY` (php artisan key:generate)
4. [ ] Konfigurasi database untuk production (MySQL/MariaDB)
5. [ ] Set `SESSION_DRIVER=redis` atau `database`
6. [ ] Set `CACHE_STORE=redis` atau `database`
7. [ ] Konfigurasi mail (production mail driver)
8. [ ] Set `QUEUE_CONNECTION=redis` atau `database`

### OPSIONAL - Untuk Optimasi
- [ ] Set `LOG_CHANNEL=slack` untuk error monitoring
- [ ] Set up S3 untuk file storage
- [ ] Configure Redis untuk caching

## TAHAP 2: DATABASE

### Checklist Database Migration
1. [ ] Test migration di development dengan MySQL
2. [ ] Backup production database
3. [ ] Run `php artisan migrate`
4. [ ] Seed data jika diperlukan

### Database Compatibility Check
- [ ] Verify foreign key constraints work with MySQL
- [ ] Verify enum types (use string instead)
- [ ] Verify date/time handling
- [ ] Verify unique constraints

## TAHAP 3: ASSETS

### Build Assets
```bash
npm run build
```

### Checklist
- [ ] Assets di-build untuk production
- [ ] Mix manifest ada di `public/mix-manifest.json`
- [ ] CSS/JS files ter-cache dengan versioning

## TAHAP 4: STORAGE

### Konfigurasi Storage
```bash
php artisan storage:link
```

### Checklist
- [ ] Create symbolic link untuk storage
- [ ] Set permissions untuk storage directory
- [ ] Configure public disk untuk uploads

## TAHAP 5: CACHING

### Clear & Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear
```

## TAHAP 6: PERMISSIONS

### Directory Permissions
```bash
chmod -R 775 storage bootstrap/cache
chgrp -R www-data storage bootstrap/cache
```

## TAHAP 7: SSL & SECURITY

### Checklist
- [ ] SSL certificate terinstall
- [ ] HTTPS enforced
- [ ] Security headers configured
- [ ] CORS configured jika perlu

## TAHAP 8: TESTING

### Run Full Test Suite
```bash
php artisan test
```

### Expected Results
- [ ] 0 failed tests
- [ ] All assertions pass

## TAHAP 9: MONITORING

### Set Up
- [ ] Error logging configured
- [ ] Health check endpoint
- [ ] Uptime monitoring
- [ ] Backup schedule

## TAHAP 10: VERIFICATION

### Smoke Test
- [ ] Login berfungsi
- [ ] CRUD operations work
- [ ] File uploads work
- [ ] PDF export works
- [ ] Excel export works
- [ ] Mobile responsive works

## DEPLOYMENT COMMANDS

```bash
# SSH ke server
ssh user@server.com

# Navigate ke project
cd /var/www/school-discipline

# Pull changes
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue worker
php artisan queue:restart

# Restart scheduler
crontab -e
# Add: * * * * * cd /project && php artisan schedule:run >> /dev/null 2>&1
```

## ROLLBACK PLAN

Jika ada masalah:

```bash
# Revert migrations
php artisan migrate:rollback

# Clear cache
php artisan optimize:clear

# Restore from backup
# (restore database from backup)
```
