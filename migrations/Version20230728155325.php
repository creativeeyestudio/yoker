<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230728155325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pages_list ADD menu_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pages_list ADD CONSTRAINT FK_F02B2989AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('CREATE INDEX IDX_F02B2989AB44FE0 ON pages_list (menu_item_id)');
        $this->addSql('ALTER TABLE posts_list ADD menu_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts_list ADD CONSTRAINT FK_FE98C1A19AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('CREATE INDEX IDX_FE98C1A19AB44FE0 ON posts_list (menu_item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pages_list DROP FOREIGN KEY FK_F02B2989AB44FE0');
        $this->addSql('DROP INDEX IDX_F02B2989AB44FE0 ON pages_list');
        $this->addSql('ALTER TABLE pages_list DROP menu_item_id');
        $this->addSql('ALTER TABLE posts_list DROP FOREIGN KEY FK_FE98C1A19AB44FE0');
        $this->addSql('DROP INDEX IDX_FE98C1A19AB44FE0 ON posts_list');
        $this->addSql('ALTER TABLE posts_list DROP menu_item_id');
    }
}
