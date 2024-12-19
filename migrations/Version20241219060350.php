<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219060350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE double_authentification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE historique_utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE inscription_pending_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE login_tentative_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE double_authentification (id INT NOT NULL, utilisateur_id INT DEFAULT NULL, code INT NOT NULL, daty TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE0128CDFB88E14F ON double_authentification (utilisateur_id)');
        $this->addSql('COMMENT ON COLUMN double_authentification.daty IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE historique_utilisateur (id INT NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, date_naissance TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, genre INT NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN historique_utilisateur.date_naissance IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN historique_utilisateur.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE inscription_pending (id INT NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, date_naissance TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, genre INT NOT NULL, mail VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN inscription_pending.date_naissance IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE login_tentative (id INT NOT NULL, utilisateur_id INT NOT NULL, tentative INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB5DA80CFB88E14F ON login_tentative (utilisateur_id)');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, date_naissance TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, genre INT NOT NULL, mail VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN utilisateur.date_naissance IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE double_authentification ADD CONSTRAINT FK_DE0128CDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE login_tentative ADD CONSTRAINT FK_BB5DA80CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE double_authentification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE historique_utilisateur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE inscription_pending_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE login_tentative_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('ALTER TABLE double_authentification DROP CONSTRAINT FK_DE0128CDFB88E14F');
        $this->addSql('ALTER TABLE login_tentative DROP CONSTRAINT FK_BB5DA80CFB88E14F');
        $this->addSql('DROP TABLE double_authentification');
        $this->addSql('DROP TABLE historique_utilisateur');
        $this->addSql('DROP TABLE inscription_pending');
        $this->addSql('DROP TABLE login_tentative');
        $this->addSql('DROP TABLE utilisateur');
    }
}
