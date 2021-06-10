<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610083641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6CF2182C8');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6CF2182C8 FOREIGN KEY (circuit_id) REFERENCES circuit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6CF2182C8');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6CF2182C8 FOREIGN KEY (circuit_id) REFERENCES circuit (id)');
    }
}
