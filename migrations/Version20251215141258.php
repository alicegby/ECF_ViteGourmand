<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215141258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allergenes (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, icone VARCHAR(250) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE avis (avis_id INT AUTO_INCREMENT NOT NULL, notes SMALLINT NOT NULL, avis VARCHAR(250) NOT NULL, date_creation DATETIME NOT NULL, date_validation DATETIME DEFAULT NULL, commande_id INT NOT NULL, statut_avis_id INT NOT NULL, valide_par INT DEFAULT NULL, INDEX IDX_8F91ABF082EA2E54 (commande_id), INDEX IDX_8F91ABF075CF6AD3 (statut_avis_id), INDEX IDX_8F91ABF0D6A3CAF (valide_par), PRIMARY KEY (avis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(250) NOT NULL, description VARCHAR(500) NOT NULL, icone VARCHAR(250) NOT NULL, condition_obtention LONGTEXT NOT NULL, actif TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE boissons (id INT AUTO_INCREMENT NOT NULL, titre_boisson VARCHAR(50) NOT NULL, description VARCHAR(150) NOT NULL, stock INT NOT NULL, qte_par_pers INT NOT NULL, min_commande INT NOT NULL, prix_par_bouteille NUMERIC(10, 2) NOT NULL, image VARCHAR(250) NOT NULL, alt VARCHAR(250) NOT NULL, date_modif DATETIME DEFAULT NULL, category_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_13E865EE12469DE2 (category_id), INDEX IDX_13E865EE553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category_cheese (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category_drink (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category_food (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category_materiel (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category_personnel (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, numero_commande VARCHAR(50) NOT NULL, date_commande DATETIME NOT NULL, date_livraison DATE NOT NULL, heure_livraison TIME NOT NULL, nb_personne SMALLINT NOT NULL, prix_menu NUMERIC(10, 2) NOT NULL, montant_options NUMERIC(10, 2) NOT NULL, montant_reduction NUMERIC(10, 2) NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, pret_materiel TINYINT NOT NULL, restitution_materiel TINYINT NOT NULL, pret_personnel TINYINT NOT NULL, motif_annulation VARCHAR(250) DEFAULT NULL, date_modif DATETIME NOT NULL, adresse_livraison VARCHAR(250) NOT NULL, code_postal_livraison VARCHAR(10) NOT NULL, ville_livraison VARCHAR(100) NOT NULL, frais_livraison NUMERIC(10, 2) NOT NULL, distance_km NUMERIC(5, 2) NOT NULL, client_id INT DEFAULT NULL, menu_id INT DEFAULT NULL, statut_commande_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_6EEAA67D19EB6921 (client_id), INDEX IDX_6EEAA67DCCD7E912 (menu_id), INDEX IDX_6EEAA67DFB435DFD (statut_commande_id), INDEX IDX_6EEAA67D553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande_boisson (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, commande_id INT NOT NULL, boisson_id INT NOT NULL, INDEX IDX_7D2CBAED82EA2E54 (commande_id), INDEX IDX_7D2CBAED734B8089 (boisson_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande_fromage (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, commande_id INT NOT NULL, fromage_id INT NOT NULL, INDEX IDX_49047E2982EA2E54 (commande_id), INDEX IDX_49047E297FCE0491 (fromage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande_materiel (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, rendu TINYINT NOT NULL, date_envoi_mail DATE DEFAULT NULL, date_limite DATE DEFAULT NULL, penalite_appliquee TINYINT NOT NULL, montant_penalite NUMERIC(10, 2) DEFAULT NULL, commande_id INT NOT NULL, materiel_id INT NOT NULL, INDEX IDX_CEF2A80B82EA2E54 (commande_id), INDEX IDX_CEF2A80B16880AAF (materiel_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande_personnel (id INT AUTO_INCREMENT NOT NULL, heures INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, commande_id INT NOT NULL, personnel_id INT NOT NULL, INDEX IDX_B6B0A99C82EA2E54 (commande_id), INDEX IDX_B6B0A99C1C109075 (personnel_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande_plat (commande_id INT NOT NULL, plat_id INT NOT NULL, category_plat_id INT DEFAULT NULL, INDEX IDX_4B54A3E482EA2E54 (commande_id), INDEX IDX_4B54A3E4D73DB560 (plat_id), INDEX IDX_4B54A3E410E5F51E (category_plat_id), PRIMARY KEY (commande_id, plat_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commande_reduction (montant_reduction NUMERIC(10, 2) NOT NULL, commande_id INT NOT NULL, reduction_id INT NOT NULL, INDEX IDX_A1EB0E2A82EA2E54 (commande_id), INDEX IDX_A1EB0E2AC03CB092 (reduction_id), PRIMARY KEY (commande_id, reduction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE conditions (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, telephone VARCHAR(20) DEFAULT NULL, role VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_F804D3B9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE favori (id INT AUTO_INCREMENT NOT NULL, date_ajout DATETIME NOT NULL, utilisateur_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_EF85A2CCFB88E14F (utilisateur_id), INDEX IDX_EF85A2CCCCD7E912 (menu_id), UNIQUE INDEX uniq_utilisateur_menu (utilisateur_id, menu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fromages (id INT AUTO_INCREMENT NOT NULL, titre_fromage VARCHAR(50) NOT NULL, description VARCHAR(150) NOT NULL, stock INT NOT NULL, min_commande INT NOT NULL, prix_par_fromage NUMERIC(10, 2) NOT NULL, image VARCHAR(250) NOT NULL, alt VARCHAR(250) NOT NULL, date_modif DATETIME DEFAULT NULL, category_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_8FD54B8312469DE2 (category_id), INDEX IDX_8FD54B83553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE horaire (id INT AUTO_INCREMENT NOT NULL, jour VARCHAR(50) NOT NULL, heure_ouverture TIME NOT NULL, heure_fermeture TIME NOT NULL, date_modif DATETIME NOT NULL, modifie_par INT DEFAULT NULL, INDEX IDX_BBC83DB68D3DBC34 (modifie_par), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE image_menu (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(250) NOT NULL, alt_text VARCHAR(100) DEFAULT NULL, ordre INT NOT NULL, est_principale TINYINT NOT NULL, menu_id INT NOT NULL, INDEX IDX_8F3FD00DCCD7E912 (menu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE materiel (id INT AUTO_INCREMENT NOT NULL, titre_materiel VARCHAR(50) NOT NULL, description VARCHAR(200) NOT NULL, stock INT NOT NULL, prix_piece NUMERIC(10, 2) NOT NULL, image VARCHAR(250) NOT NULL, alt VARCHAR(250) NOT NULL, caution NUMERIC(10, 2) NOT NULL, date_modif DATETIME DEFAULT NULL, category_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_18D2B09112469DE2 (category_id), INDEX IDX_18D2B091553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, stock INT NOT NULL, nb_pers_min INT NOT NULL, prix_par_personne NUMERIC(10, 2) NOT NULL, description VARCHAR(300) NOT NULL, date_modif DATETIME DEFAULT NULL, regime_id INT DEFAULT NULL, theme_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_7D053A9335E7D534 (regime_id), INDEX IDX_7D053A9359027487 (theme_id), INDEX IDX_7D053A93553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu_condition (menu_id INT NOT NULL, condition_id INT NOT NULL, INDEX IDX_A496EF77CCD7E912 (menu_id), INDEX IDX_A496EF77887793B6 (condition_id), PRIMARY KEY (menu_id, condition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu_plat (id INT AUTO_INCREMENT NOT NULL, ordre SMALLINT NOT NULL, menu_id INT NOT NULL, plat_id INT NOT NULL, INDEX IDX_E8775249CCD7E912 (menu_id), INDEX IDX_E8775249D73DB560 (plat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE mouvement_stock (id INT AUTO_INCREMENT NOT NULL, type_mouvement VARCHAR(50) NOT NULL, quantite_avant INT NOT NULL, quantite_mouvement INT NOT NULL, quantite_apres INT NOT NULL, motif VARCHAR(250) DEFAULT NULL, date_mouvement DATETIME NOT NULL, menu_id INT DEFAULT NULL, boisson_id INT DEFAULT NULL, fromage_id INT DEFAULT NULL, materiel_id INT DEFAULT NULL, personnel_id INT DEFAULT NULL, plat_id INT DEFAULT NULL, commande_id INT DEFAULT NULL, employe_id INT DEFAULT NULL, INDEX IDX_61E2C8EBCCD7E912 (menu_id), INDEX IDX_61E2C8EB734B8089 (boisson_id), INDEX IDX_61E2C8EB7FCE0491 (fromage_id), INDEX IDX_61E2C8EB16880AAF (materiel_id), INDEX IDX_61E2C8EB1C109075 (personnel_id), INDEX IDX_61E2C8EBD73DB560 (plat_id), INDEX IDX_61E2C8EB82EA2E54 (commande_id), INDEX IDX_61E2C8EB1B65292 (employe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(250) NOT NULL, prenom VARCHAR(50) DEFAULT NULL, date_inscription DATETIME NOT NULL, actif TINYINT NOT NULL, UNIQUE INDEX UNIQ_7E8585C8E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE personnel (id INT AUTO_INCREMENT NOT NULL, titre_personnel VARCHAR(100) NOT NULL, description VARCHAR(250) NOT NULL, prix_heure NUMERIC(10, 2) NOT NULL, stock INT NOT NULL, date_modif DATETIME DEFAULT NULL, category_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_A6BCF3DE12469DE2 (category_id), INDEX IDX_A6BCF3DE553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE plats (id INT AUTO_INCREMENT NOT NULL, titre_plat VARCHAR(100) NOT NULL, description VARCHAR(300) NOT NULL, stock INT NOT NULL, image VARCHAR(250) DEFAULT NULL, alt_texte VARCHAR(250) DEFAULT NULL, date_modif DATETIME DEFAULT NULL, category_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_854A620A12469DE2 (category_id), INDEX IDX_854A620A553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE plat_allergene (plats_id INT NOT NULL, allergenes_id INT NOT NULL, INDEX IDX_6FA44BBFAA14E1C8 (plats_id), INDEX IDX_6FA44BBFC21A0BEF (allergenes_id), PRIMARY KEY (plats_id, allergenes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE reduction (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, condition_quantite INT DEFAULT NULL, reduction NUMERIC(5, 2) NOT NULL, actif TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE regime (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE statut_avis (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_AA9236DCA4D60759 (libelle), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE statut_commande (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_11EC1ADCA4D60759 (libelle), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(100) NOT NULL, mot_de_passe VARCHAR(250) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse_postale VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(100) NOT NULL, date_creation DATETIME NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur_badge (date_obtention DATETIME NOT NULL, utilisateur_id INT NOT NULL, badge_id INT NOT NULL, INDEX IDX_383510C3FB88E14F (utilisateur_id), INDEX IDX_383510C3F7A2C2FC (badge_id), PRIMARY KEY (utilisateur_id, badge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF082EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF075CF6AD3 FOREIGN KEY (statut_avis_id) REFERENCES statut_avis (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D6A3CAF FOREIGN KEY (valide_par) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE boissons ADD CONSTRAINT FK_13E865EE12469DE2 FOREIGN KEY (category_id) REFERENCES category_drink (id)');
        $this->addSql('ALTER TABLE boissons ADD CONSTRAINT FK_13E865EE553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D19EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB435DFD FOREIGN KEY (statut_commande_id) REFERENCES statut_commande (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE commande_boisson ADD CONSTRAINT FK_7D2CBAED82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_boisson ADD CONSTRAINT FK_7D2CBAED734B8089 FOREIGN KEY (boisson_id) REFERENCES boissons (id)');
        $this->addSql('ALTER TABLE commande_fromage ADD CONSTRAINT FK_49047E2982EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_fromage ADD CONSTRAINT FK_49047E297FCE0491 FOREIGN KEY (fromage_id) REFERENCES fromages (id)');
        $this->addSql('ALTER TABLE commande_materiel ADD CONSTRAINT FK_CEF2A80B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_materiel ADD CONSTRAINT FK_CEF2A80B16880AAF FOREIGN KEY (materiel_id) REFERENCES materiel (id)');
        $this->addSql('ALTER TABLE commande_personnel ADD CONSTRAINT FK_B6B0A99C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_personnel ADD CONSTRAINT FK_B6B0A99C1C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id)');
        $this->addSql('ALTER TABLE commande_plat ADD CONSTRAINT FK_4B54A3E482EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_plat ADD CONSTRAINT FK_4B54A3E4D73DB560 FOREIGN KEY (plat_id) REFERENCES plats (id)');
        $this->addSql('ALTER TABLE commande_plat ADD CONSTRAINT FK_4B54A3E410E5F51E FOREIGN KEY (category_plat_id) REFERENCES category_food (id)');
        $this->addSql('ALTER TABLE commande_reduction ADD CONSTRAINT FK_A1EB0E2A82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_reduction ADD CONSTRAINT FK_A1EB0E2AC03CB092 FOREIGN KEY (reduction_id) REFERENCES reduction (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CCCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE fromages ADD CONSTRAINT FK_8FD54B8312469DE2 FOREIGN KEY (category_id) REFERENCES category_cheese (id)');
        $this->addSql('ALTER TABLE fromages ADD CONSTRAINT FK_8FD54B83553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE horaire ADD CONSTRAINT FK_BBC83DB68D3DBC34 FOREIGN KEY (modifie_par) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE image_menu ADD CONSTRAINT FK_8F3FD00DCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B09112469DE2 FOREIGN KEY (category_id) REFERENCES category_materiel (id)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9335E7D534 FOREIGN KEY (regime_id) REFERENCES regime (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE menu_condition ADD CONSTRAINT FK_A496EF77CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_condition ADD CONSTRAINT FK_A496EF77887793B6 FOREIGN KEY (condition_id) REFERENCES conditions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_plat ADD CONSTRAINT FK_E8775249CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_plat ADD CONSTRAINT FK_E8775249D73DB560 FOREIGN KEY (plat_id) REFERENCES plats (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EBCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB734B8089 FOREIGN KEY (boisson_id) REFERENCES boissons (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB7FCE0491 FOREIGN KEY (fromage_id) REFERENCES fromages (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB16880AAF FOREIGN KEY (materiel_id) REFERENCES materiel (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB1C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EBD73DB560 FOREIGN KEY (plat_id) REFERENCES plats (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE12469DE2 FOREIGN KEY (category_id) REFERENCES category_personnel (id)');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE plats ADD CONSTRAINT FK_854A620A12469DE2 FOREIGN KEY (category_id) REFERENCES category_food (id)');
        $this->addSql('ALTER TABLE plats ADD CONSTRAINT FK_854A620A553B2554 FOREIGN KEY (modifie_par_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE plat_allergene ADD CONSTRAINT FK_6FA44BBFAA14E1C8 FOREIGN KEY (plats_id) REFERENCES plats (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_allergene ADD CONSTRAINT FK_6FA44BBFC21A0BEF FOREIGN KEY (allergenes_id) REFERENCES allergenes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_badge ADD CONSTRAINT FK_383510C3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur_badge ADD CONSTRAINT FK_383510C3F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF082EA2E54');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF075CF6AD3');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D6A3CAF');
        $this->addSql('ALTER TABLE boissons DROP FOREIGN KEY FK_13E865EE12469DE2');
        $this->addSql('ALTER TABLE boissons DROP FOREIGN KEY FK_13E865EE553B2554');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D19EB6921');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DCCD7E912');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB435DFD');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D553B2554');
        $this->addSql('ALTER TABLE commande_boisson DROP FOREIGN KEY FK_7D2CBAED82EA2E54');
        $this->addSql('ALTER TABLE commande_boisson DROP FOREIGN KEY FK_7D2CBAED734B8089');
        $this->addSql('ALTER TABLE commande_fromage DROP FOREIGN KEY FK_49047E2982EA2E54');
        $this->addSql('ALTER TABLE commande_fromage DROP FOREIGN KEY FK_49047E297FCE0491');
        $this->addSql('ALTER TABLE commande_materiel DROP FOREIGN KEY FK_CEF2A80B82EA2E54');
        $this->addSql('ALTER TABLE commande_materiel DROP FOREIGN KEY FK_CEF2A80B16880AAF');
        $this->addSql('ALTER TABLE commande_personnel DROP FOREIGN KEY FK_B6B0A99C82EA2E54');
        $this->addSql('ALTER TABLE commande_personnel DROP FOREIGN KEY FK_B6B0A99C1C109075');
        $this->addSql('ALTER TABLE commande_plat DROP FOREIGN KEY FK_4B54A3E482EA2E54');
        $this->addSql('ALTER TABLE commande_plat DROP FOREIGN KEY FK_4B54A3E4D73DB560');
        $this->addSql('ALTER TABLE commande_plat DROP FOREIGN KEY FK_4B54A3E410E5F51E');
        $this->addSql('ALTER TABLE commande_reduction DROP FOREIGN KEY FK_A1EB0E2A82EA2E54');
        $this->addSql('ALTER TABLE commande_reduction DROP FOREIGN KEY FK_A1EB0E2AC03CB092');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CCFB88E14F');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CCCCD7E912');
        $this->addSql('ALTER TABLE fromages DROP FOREIGN KEY FK_8FD54B8312469DE2');
        $this->addSql('ALTER TABLE fromages DROP FOREIGN KEY FK_8FD54B83553B2554');
        $this->addSql('ALTER TABLE horaire DROP FOREIGN KEY FK_BBC83DB68D3DBC34');
        $this->addSql('ALTER TABLE image_menu DROP FOREIGN KEY FK_8F3FD00DCCD7E912');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B09112469DE2');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B091553B2554');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9335E7D534');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9359027487');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93553B2554');
        $this->addSql('ALTER TABLE menu_condition DROP FOREIGN KEY FK_A496EF77CCD7E912');
        $this->addSql('ALTER TABLE menu_condition DROP FOREIGN KEY FK_A496EF77887793B6');
        $this->addSql('ALTER TABLE menu_plat DROP FOREIGN KEY FK_E8775249CCD7E912');
        $this->addSql('ALTER TABLE menu_plat DROP FOREIGN KEY FK_E8775249D73DB560');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EBCCD7E912');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB734B8089');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB7FCE0491');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB16880AAF');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB1C109075');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EBD73DB560');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB82EA2E54');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB1B65292');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DE12469DE2');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DE553B2554');
        $this->addSql('ALTER TABLE plats DROP FOREIGN KEY FK_854A620A12469DE2');
        $this->addSql('ALTER TABLE plats DROP FOREIGN KEY FK_854A620A553B2554');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBFAA14E1C8');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBFC21A0BEF');
        $this->addSql('ALTER TABLE utilisateur_badge DROP FOREIGN KEY FK_383510C3FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_badge DROP FOREIGN KEY FK_383510C3F7A2C2FC');
        $this->addSql('DROP TABLE allergenes');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE boissons');
        $this->addSql('DROP TABLE category_cheese');
        $this->addSql('DROP TABLE category_drink');
        $this->addSql('DROP TABLE category_food');
        $this->addSql('DROP TABLE category_materiel');
        $this->addSql('DROP TABLE category_personnel');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_boisson');
        $this->addSql('DROP TABLE commande_fromage');
        $this->addSql('DROP TABLE commande_materiel');
        $this->addSql('DROP TABLE commande_personnel');
        $this->addSql('DROP TABLE commande_plat');
        $this->addSql('DROP TABLE commande_reduction');
        $this->addSql('DROP TABLE conditions');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE favori');
        $this->addSql('DROP TABLE fromages');
        $this->addSql('DROP TABLE horaire');
        $this->addSql('DROP TABLE image_menu');
        $this->addSql('DROP TABLE materiel');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_condition');
        $this->addSql('DROP TABLE menu_plat');
        $this->addSql('DROP TABLE mouvement_stock');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE plats');
        $this->addSql('DROP TABLE plat_allergene');
        $this->addSql('DROP TABLE reduction');
        $this->addSql('DROP TABLE regime');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE statut_avis');
        $this->addSql('DROP TABLE statut_commande');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_badge');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
