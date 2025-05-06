<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506092814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create basic objects';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE discount_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE discount_code_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE discount (id INT NOT NULL, name VARCHAR(255) NOT NULL, percent_amount INT NOT NULL, code_prefix VARCHAR(15) NOT NULL, number_of_codes INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E1E0B40EEC37DE2C ON discount (code_prefix)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE discount_code (id INT NOT NULL, discount_id INT NOT NULL, code VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E997352277153098 ON discount_code (code)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E99735224C7C611F ON discount_code (discount_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discount_code ADD CONSTRAINT FK_E99735224C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE discount_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE discount_code_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discount_code DROP CONSTRAINT FK_E99735224C7C611F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE discount
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE discount_code
        SQL);
    }
}
