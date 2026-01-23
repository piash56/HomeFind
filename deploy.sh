#!/bin/bash

# Laravel Deployment Script for cPanel
# Run this script after: git pull origin main

echo "🚀 Starting deployment..."

# Step 1: Install/Update Dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Step 2: Run Database Migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Step 3: Clear All Caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Step 4: Optimize for Production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 5: Set Permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment completed successfully!"
