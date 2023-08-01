<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801074526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_list DROP post_name, DROP post_name_en, DROP post_url, DROP post_id, DROP post_meta_title, DROP post_meta_title_en, DROP post_meta_desc, DROP post_meta_desc_en, DROP post_thumb, DROP status, DROP created_at, DROP updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_list ADD post_name VARCHAR(255) NOT NULL, ADD post_name_en VARCHAR(255) DEFAULT NULL, ADD post_url VARCHAR(255) NOT NULL, ADD post_id VARCHAR(255) NOT NULL, ADD post_meta_title VARCHAR(255) DEFAULT NULL, ADD post_meta_title_en VARCHAR(255) DEFAULT NULL, ADD post_meta_desc VARCHAR(255) DEFAULT NULL, ADD post_meta_desc_en VARCHAR(255) DEFAULT NULL, ADD post_thumb VARCHAR(255) DEFAULT NULL, ADD status TINYINT(1) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
