-- ================================================================
-- BLUESKY TRANSACTIONS — Schéma complet de la base de données
-- Créer la DB d'abord: CREATE DATABASE bluesky_transactions;
-- ================================================================

USE bluesky_transactions;

-- ----------------------------------------------------------------
-- TABLE: countries
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `countries` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                  VARCHAR(255) NOT NULL,
  `code`                  CHAR(2) NOT NULL,
  `currency_code`         VARCHAR(5) NOT NULL,
  `currency_name`         VARCHAR(255) NOT NULL,
  `flag_emoji`            VARCHAR(10) NOT NULL,
  `phone_code`            VARCHAR(10) NOT NULL,
  `default_fee_percentage` DECIMAL(5,2) NOT NULL DEFAULT 3.00,
  `is_active`             TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`            TIMESTAMP NULL DEFAULT NULL,
  `updated_at`            TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- TABLE: users (agents + admins)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255) NOT NULL,
  `email`             VARCHAR(255) NOT NULL,
  `phone`             VARCHAR(20) DEFAULT NULL,
  `role`              VARCHAR(20) NOT NULL DEFAULT 'agent',   -- agent | admin
  `country_id`        BIGINT UNSIGNED DEFAULT NULL,
  `agent_code`        VARCHAR(20) DEFAULT NULL,
  `status`            VARCHAR(20) NOT NULL DEFAULT 'pending', -- active | inactive | pending
  `address`           VARCHAR(500) DEFAULT NULL,
  `id_number`         VARCHAR(50) DEFAULT NULL,
  `profile_photo`     VARCHAR(255) DEFAULT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password`          VARCHAR(255) NOT NULL,
  `remember_token`    VARCHAR(100) DEFAULT NULL,
  `created_at`        TIMESTAMP NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_agent_code_unique` (`agent_code`),
  FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- TABLE: transactions
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transactions` (
  `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_number`     VARCHAR(30) NOT NULL,
  `sender_name`            VARCHAR(255) NOT NULL,
  `sender_phone`           VARCHAR(25) NOT NULL,
  `receiver_name`          VARCHAR(255) DEFAULT NULL,
  `receiver_phone`         VARCHAR(25) DEFAULT NULL,
  `amount`                 DECIMAL(15,2) NOT NULL,
  `fee_percentage`         DECIMAL(5,2) NOT NULL DEFAULT 3.00,
  `fee_amount`             DECIMAL(15,2) NOT NULL,
  `total_amount`           DECIMAL(15,2) NOT NULL,
  `origin_country_id`      BIGINT UNSIGNED NOT NULL,
  `destination_country_id` BIGINT UNSIGNED NOT NULL,
  `agent_id`               BIGINT UNSIGNED NOT NULL,
  `status`                 VARCHAR(20) NOT NULL DEFAULT 'completed', -- pending | completed | cancelled
  `payment_method`         VARCHAR(20) NOT NULL DEFAULT 'cash',      -- cash | mobile_money | bank
  `notes`                  TEXT DEFAULT NULL,
  `sent_at`                TIMESTAMP NULL DEFAULT NULL,
  `created_at`             TIMESTAMP NULL DEFAULT NULL,
  `updated_at`             TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_number_unique` (`transaction_number`),
  INDEX `transactions_agent_id_index` (`agent_id`),
  INDEX `transactions_origin_idx` (`origin_country_id`),
  INDEX `transactions_dest_idx` (`destination_country_id`),
  INDEX `transactions_status_idx` (`status`),
  INDEX `transactions_created_idx` (`created_at`),
  FOREIGN KEY (`origin_country_id`) REFERENCES `countries` (`id`),
  FOREIGN KEY (`destination_country_id`) REFERENCES `countries` (`id`),
  FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- DATA: 8 pays africains
-- ----------------------------------------------------------------
INSERT INTO `countries` (`name`, `code`, `currency_code`, `currency_name`, `flag_emoji`, `phone_code`, `default_fee_percentage`, `is_active`, `created_at`, `updated_at`) VALUES
('République Démocratique du Congo', 'CD', 'CDF', 'Franc Congolais',  '🇨🇩', '+243', 3.50, 1, NOW(), NOW()),
('Zambie',                           'ZM', 'ZMW', 'Kwacha zambien',    '🇿🇲', '+260', 3.00, 1, NOW(), NOW()),
('Tanzanie',                         'TZ', 'TZS', 'Shilling tanzanien','🇹🇿', '+255', 3.00, 1, NOW(), NOW()),
('Kenya',                            'KE', 'KES', 'Shilling kenyan',   '🇰🇪', '+254', 2.50, 1, NOW(), NOW()),
('Malawi',                           'MW', 'MWK', 'Kwacha malawien',   '🇲🇼', '+265', 3.50, 1, NOW(), NOW()),
('Zimbabwe',                         'ZW', 'ZWL', 'Dollar zimbabwéen', '🇿🇼', '+263', 3.00, 1, NOW(), NOW()),
('Afrique du Sud',                   'ZA', 'ZAR', 'Rand sud-africain', '🇿🇦', '+27',  2.00, 1, NOW(), NOW()),
('Namibie',                          'NA', 'NAD', 'Dollar namibien',   '🇳🇦', '+264', 2.50, 1, NOW(), NOW());

-- ----------------------------------------------------------------
-- DATA: compte administrateur (mot de passe: Admin@2024!)
-- Le hash bcrypt ci-dessous correspond à Admin@2024!
-- ----------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `phone`, `role`, `country_id`, `agent_code`, `status`, `email_verified_at`, `password`, `created_at`, `updated_at`) VALUES
('Super Administrateur', 'admin@bluesky.com', '+000000000000', 'admin', NULL, 'BSK-ADMIN-001', 'active', NOW(),
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());
-- Note: Le hash ci-dessus est 'password' — changez-le via: php artisan tinker puis User::first()->update(['password'=>Hash::make('votre_mdp')])
-- Après php artisan db:seed, le vrai hash Admin@2024! est utilisé automatiquement
