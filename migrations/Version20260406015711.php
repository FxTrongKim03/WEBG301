<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260406015711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course CHANGE credits credits INT DEFAULT 3 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_169E6FB977153098 ON course (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD1DE18A77153098 ON department (code)');
        $this->addSql('ALTER TABLE enrollment CHANGE status status VARCHAR(20) DEFAULT \'active\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33E7927C74 ON student (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_169E6FB977153098 ON course');
        $this->addSql('ALTER TABLE course CHANGE credits credits INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_CD1DE18A77153098 ON department');
        $this->addSql('ALTER TABLE enrollment CHANGE status status VARCHAR(20) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_B723AF33E7927C74 ON student');
    }
}
