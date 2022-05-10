<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510084333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD civility_id INT NOT NULL, ADD city_id INT DEFAULT NULL, ADD firstname_user VARCHAR(70) NOT NULL, ADD lastname_user VARCHAR(70) NOT NULL, ADD created_date DATETIME NOT NULL, ADD last_login_date DATETIME DEFAULT NULL, ADD active TINYINT(1) NOT NULL, ADD address VARCHAR(150) DEFAULT NULL, ADD address_two VARCHAR(150) DEFAULT NULL, ADD address_three VARCHAR(150) DEFAULT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD mobile_phone VARCHAR(20) DEFAULT NULL, ADD email_contact VARCHAR(150) DEFAULT NULL, ADD uploaded_cv VARCHAR(150) DEFAULT NULL, ADD coverletter_content LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64923D6A298 FOREIGN KEY (civility_id) REFERENCES civility (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64923D6A298 ON user (civility_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498BAC62AF ON user (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64923D6A298');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498BAC62AF');
        $this->addSql('DROP INDEX IDX_8D93D64923D6A298 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6498BAC62AF ON user');
        $this->addSql('ALTER TABLE user DROP civility_id, DROP city_id, DROP firstname_user, DROP lastname_user, DROP created_date, DROP last_login_date, DROP active, DROP address, DROP address_two, DROP address_three, DROP phone, DROP mobile_phone, DROP email_contact, DROP uploaded_cv, DROP coverletter_content');
    }
}
