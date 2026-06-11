# BlueSky Transactions System

A web platform for managing international money transfers to 8 African countries. Bilingual interface (EN/FR), admin dashboard with charts, agent management, and full transaction tracking.

---

## Tech Stack

- **Backend**: PHP 8.2+, Laravel 12
- **Frontend**: Blade, Vite, Tailwind CSS 4
- **Database**: MySQL
- **CSS/JS**: Custom design system, dark mode, animations

---

## Features

- Transaction management with automatic fee calculation
- Admin dashboard: charts, statistics, agent management
- Agent dashboard: personal stats, transaction history
- Agent approval workflow (registration → activation by admin)
- Bilingual interface EN / FR with real-time language switch
- Dark / light mode (saved in localStorage)
- CSV export (admin: all transactions, agent: own transactions only)
- Printable receipts per transaction
- Support ticket system (agents → admin)
- 8 African countries with configurable fees per country
- Role-based access control (admin / agent)
- Profile management with photo upload
- Email notification when an agent account is approved

---

## Requirements

- PHP 8.2+
- XAMPP (Apache + MySQL)
- Composer
- Node.js + NPM

---

## Installation

### 1. Start XAMPP

Open the XAMPP control panel and start **Apache** and **MySQL**.

### 2. Create the database

In phpMyAdmin (`http://localhost/phpmyadmin`) or via command line:

```sql
CREATE DATABASE bluesky_transactions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configure the environment

Copy `.env.example` to `.env` and update:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bluesky_transactions
DB_USERNAME=root
DB_PASSWORD=          # leave empty if no XAMPP password set
```

### 4. Install dependencies

```bash
composer install
npm install
```

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 7. Build assets (optional for development)

```bash
npm run dev
```

### 8. Start the development server

```bash
php artisan serve
```

Open in your browser: **http://localhost:8000**

> **Windows shortcut**: double-click `START_BLUESKY.bat` to start everything automatically.

---

## One-command setup

The `composer setup` script handles steps 4–7 in one go:

```bash
composer setup
```

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

## Project Structure

```
bluesky-transactions/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 10 controllers (Auth, Admin, Agent, Transaction, Country, Export, Profile, Lang, AgentReport)
│   │   └── Middleware/         # RoleMiddleware, SetLocale
│   ├── Mail/                   # AgentApprovedMail
│   └── Models/                 # User, Transaction, Country, AgentReport
├── database/
│   ├── migrations/             # Database schema (users, countries, transactions, agent_reports)
│   └── seeders/                # CountrySeeder, AdminSeeder
├── lang/
│   ├── en/                     # English translations
│   └── fr/                     # French translations
├── public/
│   ├── css/bluesky.css         # Full design system (dark mode, animations)
│   ├── js/bluesky.js           # Dark mode toggle, animations, counters
│   └── images/                 # logo.png + UI images
├── resources/views/            # 20 Blade templates
│   ├── admin/                  # Dashboard, agents, transactions, statistics, countries, reports
│   ├── agent/                  # Dashboard, transactions (list/create/edit/show/print), profile
│   ├── auth/                   # Login, register
│   ├── emails/                 # Agent approved email template
│   ├── components/             # Flag component
│   └── layouts/app.blade.php   # Main layout (sidebar + topbar)
├── routes/web.php              # All application routes
├── .env.example                # Environment template
└── START_BLUESKY.bat           # Windows auto-start script
```

---

## Routes Overview

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/login` | Login page |
| GET | `/register` | Agent registration |
| GET | `/admin/dashboard` | Admin dashboard |
| GET | `/admin/agents` | Agent list & approval |
| PATCH | `/admin/agents/{id}/status` | Activate / deactivate agent |
| PATCH | `/admin/agents/{id}/promote` | Promote agent to admin |
| GET | `/admin/transactions` | All transactions (admin) |
| GET | `/admin/statistics` | Statistics & charts |
| GET | `/admin/countries` | Country & fee management |
| GET | `/admin/reports` | Support tickets (admin view) |
| GET | `/admin/export/csv` | Export all transactions as CSV |
| GET | `/agent/dashboard` | Agent dashboard |
| GET | `/agent/transactions` | Agent's own transactions |
| GET | `/agent/transactions/create` | New transaction form |
| GET | `/agent/transactions/{id}/print` | Printable receipt |
| GET | `/agent/export/csv` | Export own transactions as CSV |
| POST | `/agent/reports` | Submit a support ticket |
| GET/PUT | `/profile` | View / update profile |
| GET | `/lang/{locale}` | Switch language (en/fr) |

---

## License

MIT
