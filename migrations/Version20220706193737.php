<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706193737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pages_list CHANGE page_meta_desc page_meta_desc VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE posts_list CHANGE post_meta_title post_meta_title VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pages_list CHANGE page_meta_desc page_meta_desc VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE posts_list CHANGE post_meta_title post_meta_title VARCHAR(255) NOT NULL');
    }
}
