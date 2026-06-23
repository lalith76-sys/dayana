# Dayana Enterprises ERP/POS System

## Complete Installation Guide

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer 2.x
- Node.js 18+
- NPM
- Web Server (Apache/Nginx)

### Installation Steps

#### 1. Server Requirements
```bash
# Check PHP version
php -v

# Required PHP extensions
php -m | grep -E "mysql|pdo|mbstring|xml|curl|gd|zip|bcmath|intl"
```

#### 2. Configure Environment
```bash
cd /var/www/dayana
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database settings:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dayana_erp
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 3. Create Database
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS dayana_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

#### 4. Run Installation Script
```bash
# Option A: Quick setup
php setup_database.php

# Option B: Step by step
php artisan migrate:fresh --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan db:seed --class=UserSeeder --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=ItemSeeder --force
php artisan db:seed --class=CustomerSeeder --force
php artisan db:seed --class=SupplierSeeder --force
```

#### 5. Frontend Assets (Optional - for production)
```bash
npm install
npm run build
```

#### 6. Storage & Permissions
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 7. Optimize (Production)
```bash
php artisan optimize:clear
php artisan view:cache
php artisan route:cache
php artisan config:cache
```

### Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | admin@dayana.com | password |
| **Manager** | manager@dayana.com | password |
| **Cashier** | cashier@dayana.com | password |
| **Store Keeper** | store@dayana.com | password |
| **Accountant** | accountant@dayana.com | password |

> ⚠️ **IMPORTANT: Change passwords after first login!**

### URL Access
```
http://your-server-ip/
```

### Directory Structure
```
/var/www/dayana/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # 15+ controllers for all modules
│   │   └── Requests/        # Form request validation
│   ├── Models/              # 21 Eloquent models
│   ├── Repositories/        # Repository pattern
│   ├── Services/            # Business logic layer
│   ├── Interfaces/          # Repository interfaces
│   ├── Exports/             # Excel exports
│   └── Helpers/             # System helper utilities
├── database/
│   ├── migrations/          # 20 migration files
│   └── seeders/             # 6 seeder files
├── resources/
│   └── views/               # AdminLTE 3 Blade templates
├── routes/
│   └── web.php              # 80+ registered routes
└── setup_database.php       # Automated setup script
```

### Module Summary
1. **Dashboard** - Real-time KPIs with charts
2. **Items** - Inventory master with stock card
3. **Categories** - Item categorization
4. **Suppliers** - Supplier management & ledger
5. **Customers** - Customer management & credit tracking
6. **Purchase Orders** - PO with approval workflow
7. **Goods Received Notes** - GRN with auto inventory update
8. **Stock Management** - Real-time stock with adjustments
9. **POS System** - Barcode, quick sale, hold/resume
10. **Sales Invoices** - Complete invoicing
11. **Customer Payments** - Payment allocation & receipt
12. **Supplier Payments** - Payment voucher
13. **Cheque Management** - Received/issued tracking
14. **Sales Returns** - Return with credit note
15. **Purchase Returns** - Return with supplier credit
16. **Expenses** - Multi-type expense management
17. **Profit Analysis** - Auto-calculated P&L
18. **Reports** - PDF & Excel exports
19. **User Management** - Role-based access control

### Role Permissions
- **Super Admin**: Full system access
- **Manager**: All operational modules
- **Cashier**: POS, sales, customer payments
- **Store Keeper**: Inventory, GRN, stock adjustments
- **Accountant**: Expenses, cheques, reports

### REST API
API endpoints available at `/api/` with Sanctum authentication.