# Dayana Enterprises ERP

A comprehensive Enterprise Resource Planning system built with Laravel 13 for managing day-to-day business operations including inventory, purchasing, sales, financials, and reporting.

## Features

### Masters Management
- **Items** — Product catalog with barcodes, pricing, stock tracking
- **Categories** — Item categorization
- **Suppliers** — Supplier management with balances
- **Customers** — Customer management with receivables

### Inventory
- **Stock Overview** — Real-time stock with low-stock alerts
- **Goods Received Notes (GRN)** — Receive stock against purchase orders
- **Stock Adjustments** — Additions, deductions, defective stock management

### Purchasing
- **Purchase Orders** — Create, approve, receive, and manage POs
- **Supplier Payments** — Record payments against POs
- **Purchase Returns** — Return defective/incorrect stock to suppliers

### Sales
- **Point of Sale (POS)** — Quick billing interface
- **Sales Invoices** — Create and manage invoices
- **Customer Payments** — Record payments against invoices
- **Sales Returns** — Process customer returns and refunds

### Financial
- **Expenses** — Track business expenses
- **Cheque Management** — Manage incoming/outgoing cheques
- **Profit Analysis** — Monthly profit/loss analysis

### Reports
- **Inventory Reports** — Stock valuation, low stock items
- **Sales Reports** — Sales summaries with date filtering
- **Purchase Reports** — Purchase summaries
- **Financial Reports** — Profit analysis, expense breakdowns

### Administration
- **User Management** — Role-based access control
- **Roles & Permissions** — Granular permission management (via Spatie)

## Requirements

- PHP 8.3+
- MySQL 8.0+
- Composer
- Node.js & NPM (for frontend assets)

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/lalith76-sys/dayana.git
cd dayana

# 2. Install PHP dependencies
composer install

# 3. Install and build frontend assets
npm install
npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your database in .env
#    Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

### Database Setup

Configure your `.env` file with database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dayana_erp
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Then run the setup:

```bash
# Run migrations and seeders
php artisan migrate --seed
```

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@dayana.com | password |
| Manager | manager@dayana.com | password |
| Cashier | cashier@dayana.com | password |
| Store Keeper | store@dayana.com | password |
| Accountant | accountant@dayana.com | password |

## Nginx Configuration (Subdirectory)

If deploying under a subdirectory (e.g., `/dayana`), configure the server block to handle URL rewriting properly.

## Tech Stack

- **Framework:** Laravel 13
- **PHP:** 8.3
- **Database:** MySQL
- **Frontend:** AdminLTE 3, Bootstrap 4, jQuery, DataTables
- **Authentication:** Laravel UI (Bootstrap scaffolding)
- **Authorization:** Spatie Laravel-permission
- **Assets:** Vite

## Security

- `.env` file (with database credentials) is excluded from version control
- Use strong passwords in production
- Configure proper file permissions for `storage/` and `bootstrap/cache/`
- Set `APP_DEBUG=false` in production

## License

This project is open-sourced under the MIT license.
