<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220517093507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_note DROP FOREIGN KEY FK_3851929C979B1AD6');
        $this->addSql('ALTER TABLE application_note ADD CONSTRAINT FK_3851929C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_note DROP FOREIGN KEY FK_3851929C979B1AD6');
        $this->addSql('ALTER TABLE application_note ADD CONSTRAINT FK_3851929C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }
}
