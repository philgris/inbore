<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415130754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE test_id_seq CASCADE');
        $this->addSql('ALTER TABLE typecontactvoc DROP CONSTRAINT contact_fk_fkey');
        $this->addSql('ALTER TABLE typecontactvoc ALTER voc_fk DROP NOT NULL');
        $this->addSql('ALTER TABLE typecontactvoc ADD CONSTRAINT FK_E1598A0DF0862414 FOREIGN KEY (contact_fk) REFERENCES contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE typecontactvoc DROP CONSTRAINT FK_E1598A0DF0862414');
        $this->addSql('ALTER TABLE typecontactvoc ALTER voc_fk SET NOT NULL');
        $this->addSql('ALTER TABLE typecontactvoc ADD CONSTRAINT contact_fk_fkey FOREIGN KEY (contact_fk) REFERENCES contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
