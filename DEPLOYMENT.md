# Deployment Guide for cPanel

## Quick Deployment Steps

After pushing your code to the repository, follow these steps in your cPanel terminal:

### 1. Navigate to your project directory
```bash
cd ~/public_html
# or wherever your Laravel project is located
cd ~/your-project-path
```

### 2. Pull latest code
```bash
git pull origin main
# Replace 'main' with your branch name if different
```

### 3. Install dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 4. Run database migrations
```bash
php artisan migrate --force
```

### 5. Clear caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 6. Optimize for production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Set permissions (if needed)
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Using the Deployment Script

If you've uploaded the `deploy.sh` script:

1. Make it executable:
```bash
chmod +x deploy.sh
```

2. Run it:
```bash
./deploy.sh
```

## Important Notes

- **Always backup your database** before running migrations
- Use `--force` flag with migrations in production to avoid prompts
- The `--no-dev` flag in composer install excludes development dependencies
- Clear caches after every deployment to ensure changes take effect
- Check file permissions if you encounter write errors

## Troubleshooting

### If composer command not found:
```bash
# Find composer path
which composer
# Or use full path
/usr/local/bin/composer install --no-dev --optimize-autoloader
```

### If php artisan not found:
```bash
# Use full PHP path
/usr/local/bin/php artisan migrate --force
```

### If permission errors:
```bash
# Set ownership (replace username with your cPanel username)
chown -R username:username storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```
