<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801075319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_list ADD author_id INT NOT NULL, ADD post_name JSON NOT NULL, ADD post_url VARCHAR(255) NOT NULL, ADD post_thumb VARCHAR(255) DEFAULT NULL, ADD post_content JSON NOT NULL, ADD post_meta_title JSON NOT NULL, ADD post_meta_desc JSON DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD online TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE posts_list ADD CONSTRAINT FK_FE98C1A1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FE98C1A1F675F31B ON posts_list (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_list DROP FOREIGN KEY FK_FE98C1A1F675F31B');
        $this->addSql('DROP INDEX IDX_FE98C1A1F675F31B ON posts_list');
        $this->addSql('ALTER TABLE posts_list DROP author_id, DROP post_name, DROP post_url, DROP post_thumb, DROP post_content, DROP post_meta_title, DROP post_meta_desc, DROP created_at, DROP updated_at, DROP online');
    }
}
