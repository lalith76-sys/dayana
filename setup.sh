#!/bin/bash

# Dayana Enterprises ERP/POS Setup Script
# Run: bash setup.sh

echo "========================================"
echo " Dayana Enterprises ERP/POS Installation"
echo "========================================"
echo ""

# Navigate to project
cd /var/www/dayana

# 1. Environment Configuration
echo "[1/8] Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

echo ""
echo "Please update your .env file with database credentials:"
echo "DB_CONNECTION=mysql"
echo "DB_HOST=127.0.0.1"
echo "DB_PORT=3306"
echo "DB_DATABASE=dayana_erp"
echo "DB_USERNAME=root"
echo "DB_PASSWORD=your_password"
echo ""

# 2. Install Dependencies
echo "[2/8] Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo ""
echo "[3/8] Installing NPM dependencies..."
npm install
npm run build

echo ""
# 3. Create Database
echo "[4/8] Create database if needed..."
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS dayana_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" 2>/dev/null || echo "Please create database manually"

echo ""
# 4. Run Migrations
echo "[5/8] Running database migrations..."
php artisan migrate --force

echo ""
# 5. Run Seeders
echo "[6/8] Seeding database..."
php artisan db:seed --class=RolePermissionSeeder --force
php artisan db:seed --class=UserSeeder --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=ItemSeeder --force
php artisan db:seed --class=CustomerSeeder --force
php artisan db:seed --class=SupplierSeeder --force

echo ""
# 6. Create Storage Link
echo "[7/8] Creating storage link..."
php artisan storage:link

echo ""
# 7. Optimize
echo "[8/8] Optimizing Laravel..."
php artisan optimize:clear
php artisan view:cache
php artisan route:cache
php artisan config:cache

echo ""
echo "========================================"
echo " Installation Complete!"
echo "========================================"
echo ""
echo "Default Login Credentials:"
echo "------------------------"
echo "Super Admin: admin@dayana.com / password"
echo "Manager: manager@dayana.com / password"
echo "Cashier: cashier@dayana.com / password"
echo "Store Keeper: store@dayana.com / password"
echo "Accountant: accountant@dayana.com / password"
echo ""
echo "Access URL: http://your-domain.com"
echo ""
echo "IMPORTANT: Change passwords after first login!"
echo "=============================="
echo ""

# Optional: Set permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "Setup completed successfully!"