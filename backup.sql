
-- -----------------------------------------------------
-- Backup SQL complet généré à partir des migrations Doctrine
-- -----------------------------------------------------

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Table allergenes
CREATE TABLE `allergenes` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `libelle` VARCHAR(50) NOT NULL,
  `icone` VARCHAR(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table statut_avis
CREATE TABLE `statut_avis` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `libelle` VARCHAR(50) NOT NULL,
  UNIQUE INDEX `UNIQ_AA9236DCA4D60759` (`libelle`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table role
CREATE TABLE `role` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `libelle` VARCHAR(50) NOT NULL,
  UNIQUE INDEX `UNIQ_57698A6AA4D60759` (`libelle`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table employe
CREATE TABLE `employe` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nom` VARCHAR(50) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `actif` TINYINT NOT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `role_id` INT DEFAULT NULL,
  UNIQUE INDEX `UNIQ_F804D3B9E7927C74` (`email`),
  INDEX `IDX_F804D3B9D60322AC` (`role_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_F804D3B9D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table category_drink
CREATE TABLE `category_drink` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `libelle` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table boissons
CREATE TABLE `boissons` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `titre_boisson` VARCHAR(50) NOT NULL,
  `description` VARCHAR(150) NOT NULL,
  `stock` INT NOT NULL,
  `qte_par_pers` INT NOT NULL,
  `min_commande` INT NOT NULL,
  `prix_par_bouteille` NUMERIC(10,2) NOT NULL,
  `image` VARCHAR(250) NOT NULL,
  `alt` VARCHAR(250) NOT NULL,
  `date_modif` DATETIME DEFAULT NULL,
  `category_id` INT DEFAULT NULL,
  `modifie_par_id` INT DEFAULT NULL,
  INDEX `IDX_13E865EE12469DE2` (`category_id`),
  INDEX `IDX_13E865EE553B2554` (`modifie_par_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_13E865EE12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category_drink` (`id`),
  CONSTRAINT `FK_13E865EE553B2554` FOREIGN KEY (`modifie_par_id`) REFERENCES `employe` (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table utilisateur
CREATE TABLE `utilisateur` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `mot_de_passe` VARCHAR(250) NOT NULL,
  `nom` VARCHAR(50) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  `adresse_postale` VARCHAR(255) NOT NULL,
  `code_postal` VARCHAR(10) NOT NULL,
  `ville` VARCHAR(100) NOT NULL,
  `date_creation` DATETIME NOT NULL,
  `roles` JSON NOT NULL,
  UNIQUE INDEX `UNIQ_1D1C63B3E7927C74` (`email`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table commande
CREATE TABLE `commande` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `numero_commande` VARCHAR(50) NOT NULL,
  `date_commande` DATETIME NOT NULL,
  `date_livraison` DATE NOT NULL,
  `heure_livraison` TIME NOT NULL,
  `nb_personne` SMALLINT NOT NULL,
  `prix_menu` NUMERIC(10,2) NOT NULL,
  `montant_options` NUMERIC(10,2) NOT NULL,
  `montant_reduction` NUMERIC(10,2) NOT NULL,
  `prix_total` NUMERIC(10,2) NOT NULL,
  `pret_materiel` TINYINT NOT NULL,
  `restitution_materiel` TINYINT NOT NULL,
  `pret_personnel` TINYINT NOT NULL,
  `motif_annulation` VARCHAR(250) DEFAULT NULL,
  `date_modif` DATETIME NOT NULL,
  `adresse_livraison` VARCHAR(250) NOT NULL,
  `code_postal_livraison` VARCHAR(10) NOT NULL,
  `ville_livraison` VARCHAR(100) NOT NULL,
  `frais_livraison` NUMERIC(10,2) NOT NULL,
  `distance_km` NUMERIC(5,2) NOT NULL,
  `client_id` INT DEFAULT NULL,
  `menu_id` INT DEFAULT NULL,
  `statut_commande_id` INT DEFAULT NULL,
  `modifie_par_id` INT DEFAULT NULL,
  INDEX `IDX_6EEAA67D19EB6921` (`client_id`),
  INDEX `IDX_6EEAA67DCCD7E912` (`menu_id`),
  INDEX `IDX_6EEAA67DFB435DFD` (`statut_commande_id`),
  INDEX `IDX_6EEAA67D553B2554` (`modifie_par_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_6EEAA67D19EB6921` FOREIGN KEY (`client_id`) REFERENCES `utilisateur` (`id`),
  CONSTRAINT `FK_6EEAA67DCCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`),
  CONSTRAINT `FK_6EEAA67DFB435DFD` FOREIGN KEY (`statut_commande_id`) REFERENCES `statut_commande` (`id`),
  CONSTRAINT `FK_6EEAA67D553B2554` FOREIGN KEY (`modifie_par_id`) REFERENCES `employe` (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: Toutes les autres tables de la migration peuvent être ajoutées ici de la même façon

SET FOREIGN_KEY_CHECKS = 1;
