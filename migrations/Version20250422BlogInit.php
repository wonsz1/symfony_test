<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250422BlogInit extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial blog schema: user, category, post, comment';
    }

    public function up(Schema $schema): void
    {
        // User
        $this->addSql('CREATE TABLE "user" (
            id SERIAL PRIMARY KEY,
            email VARCHAR(180) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            is_active BOOLEAN NOT NULL,
            roles JSON DEFAULT NULL
        )');
        // Category
        $this->addSql('CREATE TABLE category (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE
        )');
        // Post
        $this->addSql('CREATE TABLE post (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content TEXT NOT NULL,
            excerpt TEXT DEFAULT NULL,
            featured_image VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            author_id INT NOT NULL,
            category_id INT NOT NULL,
            view_count INT NOT NULL DEFAULT 0,
            CONSTRAINT fk_post_author FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE,
            CONSTRAINT fk_post_category FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE
        )');
        // Comment
        $this->addSql('CREATE TABLE comment (
            id SERIAL PRIMARY KEY,
            content TEXT NOT NULL,
            author_id INT NOT NULL,
            post_id INT NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            is_approved BOOLEAN NOT NULL DEFAULT FALSE,
            CONSTRAINT fk_comment_author FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE,
            CONSTRAINT fk_comment_post FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE "user"');
    }
}
