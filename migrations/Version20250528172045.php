<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528172045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD uuid UUID NOT NULL DEFAULT gen_random_uuid()
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP uuid
        SQL);
    }
}
