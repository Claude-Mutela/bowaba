-- Tables pour les Statistiques et Contenus Dynamiques
-- À importer dans votre base de données 'bowabanc_db' via phpMyAdmin

-- 8. PROJECTS (Pour "Projets Réalisés" -> COUNT(id))
-- Liste des projets réalisés pour le portfolio et stats
CREATE TABLE `projects` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `client_name` VARCHAR(255) NULL,
    `category` VARCHAR(100) NULL, -- ex: Web, Design, Formation
    `completion_date` DATE NULL,
    `status` ENUM('completed', 'in_progress') DEFAULT 'completed',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. PARTNERS (Pour "Entreprises Accompagnées" -> COUNT(id))
-- Liste des entreprises/partenaires
CREATE TABLE `partners` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `logo` VARCHAR(255) NULL,       -- URL/Chemin du logo
    `website` VARCHAR(255) NULL,    -- Site web
    `description` TEXT NULL,        -- Courte description
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. TESTIMONIALS (Pour "Clients Satisfaits" -> COUNT(id) WHERE rating >= 4)
-- Témoignages clients
CREATE TABLE `testimonials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_name` VARCHAR(255) NOT NULL,
    `position` VARCHAR(255) NULL,    -- Poste (ex: CEO)
    `company` VARCHAR(255) NULL,     -- Entreprise
    `content` TEXT NOT NULL,         -- Le témoignage
    `rating` TINYINT UNSIGNED DEFAULT 5, -- Note sur 5
    `image` VARCHAR(255) NULL,       -- Photo du client
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. TRAINING PROGRAMS (Programmes de Formation)
-- Liste des formations disponibles
CREATE TABLE `training_programs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `duration` VARCHAR(100) NULL,    -- ex: "3 mois"
    `image` VARCHAR(255) NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. STUDENTS (Pour "Personnes Formées" -> COUNT(id))
-- Liste des apprenants certifiés ou formés
CREATE TABLE `students` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `program_id` BIGINT UNSIGNED NULL, -- Lien vers le programme
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL,
    `completion_date` DATE NULL,
    `certificate_id` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`program_id`) REFERENCES `training_programs`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. SETTINGS (Pour les compteurs globaux ou ajustements manuels si besoin)
-- Permet d'ajouter une valeur de base aux compteurs (ex: +500 anciens clients)
CREATE TABLE `settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(191) NOT NULL UNIQUE,  -- ex: 'base_clients_count'
    `setting_value` TEXT NULL,                  -- ex: '500'
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données initiales pour les paramètres
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('base_satisfied_clients', '0', 'Nombre de base de clients satisfaits (s\'ajoute au count)'),
('base_projects_done', '0', 'Nombre de base de projets réalisés'),
('base_trained_people', '0', 'Nombre de base de personnes formées'),
('base_companies_supported', '0', 'Nombre de base d\'entreprises accompagnées');
