# BlueSky Transactions System

Plateforme web de gestion de transferts d'argent internationaux vers 8 pays africains. Interface bilingue (FR/EN), tableau de bord admin avec graphiques, gestion des agents, et suivi complet des transactions.

---

## Stack technique

- **Backend** : PHP 8.2+, Laravel 12
- **Frontend** : Blade, Vite, Tailwind CSS 4
- **Base de données** : MySQL
- **CSS/JS** : Système de design custom, dark mode, animations

---

## Fonctionnalités

- Gestion des transactions avec calcul automatique des frais
- Tableau de bord admin : graphiques, statistiques, gestion des agents
- Tableau de bord agent : stats personnelles, historique
- Workflow d'approbation des agents (inscription → activation par l'admin)
- Interface bilingue EN / FR avec switch en temps réel
- Mode sombre / clair (sauvegardé en localStorage)
- Export CSV (admin : toutes transactions, agent : ses propres transactions)
- Reçus imprimables par transaction
- Système de tickets de support (agents → admin)
- 8 pays africains avec frais configurables par pays
- Contrôle d'accès par rôle (admin / agent)
- Gestion du profil avec photo

---

## Prérequis

- PHP 8.2+
- XAMPP (Apache + MySQL)
- Composer
- Node.js + NPM

---

## Installation

### 1. Démarrer XAMPP

Ouvre le panneau de contrôle XAMPP et démarre **Apache** et **MySQL**.

### 2. Créer la base de données

Dans phpMyAdmin (`http://localhost/phpmyadmin`) ou en ligne de commande :

```sql
CREATE DATABASE bluesky_transactions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configurer l'environnement

Copie `.env.example` en `.env` et configure :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bluesky_transactions
DB_USERNAME=root
DB_PASSWORD=          # laisser vide si pas de mot de passe XAMPP
```

### 4. Installer les dépendances

```bash
composer install
npm install
```

### 5. Générer la clé d'application

```bash
php artisan key:generate
```

### 6. Lancer les migrations et les seeders

```bash
php artisan migrate
php artisan db:seed
```

### 7. Compiler les assets (optionnel pour le dev)

```bash
npm run dev
```

### 8. Démarrer le serveur

```bash
php artisan serve
```

Ouvrir dans le navigateur : **http://localhost:8000**

> **Raccourci Windows** : double-clique sur `START_BLUESKY.bat` pour démarrer automatiquement.

---

## Compte admin par défaut

| Email | Mot de passe |
|-------|-------------|
| admin@bluesky.com | Admin@2024! |

---

## Pays supportés

| Drapeau | Pays | Code | Devise |
|---------|------|------|--------|
| 🇨🇩 | RD Congo | CD | CDF |
| 🇿🇲 | Zambie | ZM | ZMW |
| 🇹🇿 | Tanzanie | TZ | TZS |
| 🇰🇪 | Kenya | KE | KES |
| 🇲🇼 | Malawi | MW | MWK |
| 🇿🇼 | Zimbabwe | ZW | ZWL |
| 🇿🇦 | Afrique du Sud | ZA | ZAR |
| 🇳🇦 | Namibie | NA | NAD |

---

## Structure du projet

```
bluesky-transactions/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 10 contrôleurs (Auth, Admin, Agent, Transaction...)
│   │   └── Middleware/         # RoleMiddleware, SetLocale
│   └── Models/                 # User, Transaction, Country, AgentReport
├── database/
│   ├── migrations/             # Structure de la base de données
│   └── seeders/                # CountrySeeder, AdminSeeder
├── lang/
│   ├── en/                     # Traductions anglaises
│   └── fr/                     # Traductions françaises
├── public/
│   ├── css/bluesky.css         # Système de design complet (dark mode)
│   ├── js/bluesky.js           # Dark mode, animations, compteurs
│   └── images/                 # logo.png + images UI
├── resources/views/            # 29 templates Blade
│   ├── admin/                  # Dashboard, agents, transactions, stats, pays
│   ├── agent/                  # Dashboard, transactions, profil
│   ├── auth/                   # Login, inscription
│   └── layouts/app.blade.php   # Layout principal (sidebar + topbar)
├── routes/web.php              # Toutes les routes de l'application
├── .env                        # Configuration environnement
└── START_BLUESKY.bat           # Script démarrage Windows
```

---

## Licence

MIT
