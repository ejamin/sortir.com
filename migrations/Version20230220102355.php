<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220102355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etats (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incriptions (id INT AUTO_INCREMENT NOT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieux (id INT AUTO_INCREMENT NOT NULL, id_ville_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, rue VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, INDEX IDX_9E44A8AEF7E4ECA3 (id_ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participants (id INT AUTO_INCREMENT NOT NULL, id_sites_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, is_admin TINYINT(1) NOT NULL, is_actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_71697092E7927C74 (email), INDEX IDX_7169709228E30803 (id_sites_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participants_sorties (participants_id INT NOT NULL, sorties_id INT NOT NULL, INDEX IDX_564D76DF838709D5 (participants_id), INDEX IDX_564D76DF15DFCFB2 (sorties_id), PRIMARY KEY(participants_id, sorties_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sites (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sorties (id INT AUTO_INCREMENT NOT NULL, id_organisateur_id INT DEFAULT NULL, id_site_id INT DEFAULT NULL, id_lieu_id INT DEFAULT NULL, id_etat_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, duree INT DEFAULT NULL, date_fin DATETIME NOT NULL, nb_inscrit_max INT NOT NULL, description VARCHAR(255) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, INDEX IDX_488163E830687172 (id_organisateur_id), INDEX IDX_488163E82820BF36 (id_site_id), INDEX IDX_488163E8B42FBABC (id_lieu_id), INDEX IDX_488163E8D3C32F8F (id_etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE villes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lieux ADD CONSTRAINT FK_9E44A8AEF7E4ECA3 FOREIGN KEY (id_ville_id) REFERENCES villes (id)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_7169709228E30803 FOREIGN KEY (id_sites_id) REFERENCES sites (id)');
        $this->addSql('ALTER TABLE participants_sorties ADD CONSTRAINT FK_564D76DF838709D5 FOREIGN KEY (participants_id) REFERENCES participants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participants_sorties ADD CONSTRAINT FK_564D76DF15DFCFB2 FOREIGN KEY (sorties_id) REFERENCES sorties (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E830687172 FOREIGN KEY (id_organisateur_id) REFERENCES participants (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E82820BF36 FOREIGN KEY (id_site_id) REFERENCES sites (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8B42FBABC FOREIGN KEY (id_lieu_id) REFERENCES lieux (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8D3C32F8F FOREIGN KEY (id_etat_id) REFERENCES etats (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieux DROP FOREIGN KEY FK_9E44A8AEF7E4ECA3');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_7169709228E30803');
        $this->addSql('ALTER TABLE participants_sorties DROP FOREIGN KEY FK_564D76DF838709D5');
        $this->addSql('ALTER TABLE participants_sorties DROP FOREIGN KEY FK_564D76DF15DFCFB2');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E830687172');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E82820BF36');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8B42FBABC');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8D3C32F8F');
        $this->addSql('DROP TABLE etats');
        $this->addSql('DROP TABLE incriptions');
        $this->addSql('DROP TABLE lieux');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE participants_sorties');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE sorties');
        $this->addSql('DROP TABLE villes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
