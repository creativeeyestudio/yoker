<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230728203149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_list DROP FOREIGN KEY FK_FE98C1A19AB44FE0');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_link (id INT AUTO_INCREMENT NOT NULL, menu_id INT DEFAULT NULL, link_id INT DEFAULT NULL, INDEX IDX_FEE369BFCCD7E912 (menu_id), INDEX IDX_FEE369BFADA40271 (link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BFCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BFADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('DROP TABLE menu_item');
        $this->addSql('DROP TABLE menus_list');
        $this->addSql('DROP TABLE menus_order');
        $this->addSql('DROP INDEX IDX_FE98C1A19AB44FE0 ON posts_list');
        $this->addSql('ALTER TABLE posts_list DROP menu_item_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_item (id INT AUTO_INCREMENT NOT NULL, link INT NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE menus_list (id INT AUTO_INCREMENT NOT NULL, menu_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, menu_pos JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE menus_order (id INT AUTO_INCREMENT NOT NULL, menu_id INT NOT NULL, link_id INT NOT NULL, link_order INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BFCCD7E912');
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BFADA40271');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_link');
        $this->addSql('ALTER TABLE posts_list ADD menu_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts_list ADD CONSTRAINT FK_FE98C1A19AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FE98C1A19AB44FE0 ON posts_list (menu_item_id)');
    }
}
