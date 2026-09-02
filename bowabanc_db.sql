-- Schema pour Blog d'Entreprise Professionnel
-- Basé sur les besoins frontend et les tables demandées : 
-- users, articles, article_categories, tags, views, services

-- 1. USERS
-- Gestion des auteurs et administrateurs
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor', 'author') DEFAULT 'author',
    `avatar` VARCHAR(255) NULL, -- Pour l'image de l'auteur affichée sur le blog
    `bio` TEXT NULL,            -- Biographie courte
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. ARTICLE CATEGORIES
-- Catégorisation principale des articles (ex: Actualités, Tech, RH...)
CREATE TABLE `article_categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ARTICLES
-- Contenu principal du blog
CREATE TABLE `articles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,      -- Auteur
    `category_id` BIGINT UNSIGNED NULL,  -- Catégorie principale
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt` TEXT NULL,                 -- Résumé pour les cartes (grid)
    `content` LONGTEXT NOT NULL,         -- Contenu complet HTML
    `cover_image` VARCHAR(255) NULL,     -- Image principale
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` BOOLEAN DEFAULT FALSE, -- Mise en avant (ex: Carousel)
    `views_count` INT UNSIGNED DEFAULT 0,-- Compteur cache pour affichage rapide
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `article_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TAGS
-- Mots-clés pour le filtrage (nuage de mots-clés)
CREATE TABLE `tags` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. ARTICLE_TAGS (Pivot)
-- Relation Many-to-Many entre Articles et Tags
CREATE TABLE `article_tags` (
    `article_id` BIGINT UNSIGNED NOT NULL,
    `tag_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`article_id`, `tag_id`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. VIEWS
-- Tracking des vues uniques (Analytics)
CREATE TABLE `views` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `article_id` BIGINT UNSIGNED NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,  -- Support IPv6
    `user_agent` TEXT NULL,             -- Pour info device/browser
    `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_article_ip` (`article_id`, `ip_address`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. SERVICES
-- Présentation des offres/services de l'entreprise
CREATE TABLE `services` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,             -- Description courte
    `content` LONGTEXT NULL,             -- Description détaillée
    `icon` VARCHAR(100) NULL,            -- Classe d'icône (bi bi-...) ou URL
    `image` VARCHAR(255) NULL,           -- Image d'illustration
    `display_order` INT DEFAULT 0,       -- Pour trier l'affichage
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
