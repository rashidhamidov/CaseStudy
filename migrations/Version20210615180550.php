<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210615180550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD providerapi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739C87F860D0 FOREIGN KEY (providerapi_id) REFERENCES provider_api (id)');
        $this->addSql('CREATE INDEX IDX_92C4739C87F860D0 ON provider (providerapi_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider DROP FOREIGN KEY FK_92C4739C87F860D0');
        $this->addSql('DROP INDEX IDX_92C4739C87F860D0 ON provider');
        $this->addSql('ALTER TABLE provider DROP providerapi_id');
    }
}
