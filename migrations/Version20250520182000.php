<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250520182000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make password nullable for OAuth users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ALTER COLUMN password DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD google_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ALTER COLUMN password SET NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP google_id');
    }
}
