# BLUESKY Transactions — Installation Guide

## Requirements
- PHP 8.2+
- XAMPP (Apache + MySQL)
- Composer
- Modern web browser

---

## Installation Steps

### 1. Start XAMPP
1. Open the XAMPP Control Panel
2. Start **Apache** and **MySQL**

### 2. Create the database
Open **phpMyAdmin** at `http://local

host/phpmyadmin` and run:
```sql
CREATE DATABASE bluesky_transactions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or via the XAMPP MySQL shell:
```
mysql -u root -e "CREATE DATABASE bluesky_transactions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Configure the environment
Open the `.env` file and verify:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bluesky_transactions
DB_USERNAME=root
DB_PASSWORD=        ← leave empty if no XAMPP root password
```

### 4. Install dependencies
```bash
cd bluesky-transactions
composer install
```

### 5. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Start the server
```bash
php artisan serve
```

Open your browser at: **http://localhost:8000**

---

## Default Admin Account
| Email | Password |
|-------|----------|
| admin@bluesky.com | Admin@2024! |

---

## Supported Countries
| Flag | Country | Code | Currency |
|------|---------|------|----------|
| 🇨🇩 | DR Congo | CD | CDF |
| 🇿🇲 | Zambia | ZM | ZMW |
| 🇹🇿 | Tanzania | TZ | TZS |
| 🇰🇪 | Kenya | KE | KES |
| 🇲🇼 | Malawi | MW | MWK |
| 🇿🇼 | Zimbabwe | ZW | ZWL |
| 🇿🇦 | South Africa | ZA | ZAR |
| 🇳🇦 | Namibia | NA | NAD |

---

## File Structure

```
bluesky-transactions/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php         # Login, Register, Logout
│   │   │   ├── AdminController.php        # Admin dashboard, agents, statistics
│   │   │   ├── AgentController.php        # Agent dashboard
│   │   │   ├── TransactionController.php  # Transaction CRUD
│   │   │   ├── ExportController.php       # CSV export
│   │   │   └── LangController.php         # Language switcher
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php          # Access control (admin/agent)
│   │       └── SetLocale.php               # Language session middleware
│   └── Models/
│       ├── Country.php                     # Country model (8 African countries)
│       ├── User.php                        # User model (agent/admin)
│       └── Transaction.php                 # Transaction model
│
├── database/
│   ├── migrations/                         # Database structure
│   ├── seeders/
│   │   ├── CountrySeeder.php               # Seeds 8 African countries
│   │   └── AdminSeeder.php                 # Seeds default admin account
│   └── bluesky_schema.sql                  # Raw SQL schema (alternative to migrations)
│
├── lang/
│   ├── en/app.php                          # English translations
│   └── fr/app.php                          # French translations
│
├── resources/views/
│   ├── layouts/app.blade.php               # Main layout (sidebar + topbar + dark mode)
│   ├── auth/
│   │   ├── login.blade.php                 # Login page
│   │   └── register.blade.php              # Agent registration page
│   ├── admin/
│   │   ├── dashboard.blade.php             # Admin dashboard with charts
│   │   ├── agents/index.blade.php          # Agent management
│   │   ├── transactions/index.blade.php    # All transactions
│   │   └── statistics.blade.php            # Advanced statistics
│   └── agent/
│       ├── dashboard.blade.php             # Agent dashboard
│       └── transactions/
│           ├── index.blade.php             # Agent transaction list
│           ├── create.blade.php            # New transaction form
│           └── show.blade.php              # Transaction receipt
│
├── public/
│   ├── css/bluesky.css                     # Full design system (dark mode + animations)
│   ├── js/bluesky.js                       # Dark mode, animations, counters, UI
│   └── images/
│       └── logo.png                        # Blue Sky logo
│
├── routes/web.php                          # All application routes
├── START_BLUESKY.bat                       # One-click startup script (Windows)
└── .env                                    # Environment configuration
```

---

## Features
- **Professional design** with Blue Sky logo and animated UI
- **Dark / Light mode** toggle (saved in localStorage)
- **Bilingual** English / French interface
- **8 African countries** with flag emojis
- **Admin dashboard** with Chart.js graphs (monthly + country breakdown)
- **Agent dashboard** with personal statistics and progression
- **Transaction form** with automatic fee calculation preview
- **Printable receipt** for each transaction
- **Agent management**: activate, deactivate, promote to admin
- **Advanced statistics**: yearly growth, monthly detail per country
- **CSV export** with filters
- **Mobile responsive** with collapsible sidebar
