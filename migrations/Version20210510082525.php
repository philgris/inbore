<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210510082525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adress ADD date_user_cre TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE adress ADD date_user_maj TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE adress ADD user_cre BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE adress ADD user_maj BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE adress DROP date_user_cre');
        $this->addSql('ALTER TABLE adress DROP date_user_maj');
        $this->addSql('ALTER TABLE adress DROP user_cre');
        $this->addSql('ALTER TABLE adress DROP user_maj');
    }
}
