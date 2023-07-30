<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230729124247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BFADA40271');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP INDEX IDX_FEE369BFADA40271 ON menu_link');
        $this->addSql('ALTER TABLE menu_link ADD post_id INT DEFAULT NULL, CHANGE link_id page_id INT DEFAULT NULL, CHANGE link_order order_link INT NOT NULL');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BFC4663E4 FOREIGN KEY (page_id) REFERENCES pages_list (id)');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BF4B89032C FOREIGN KEY (post_id) REFERENCES posts_list (id)');
        $this->addSql('CREATE INDEX IDX_FEE369BFC4663E4 ON menu_link (page_id)');
        $this->addSql('CREATE INDEX IDX_FEE369BF4B89032C ON menu_link (post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, url INT NOT NULL, link_type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BFC4663E4');
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BF4B89032C');
        $this->addSql('DROP INDEX IDX_FEE369BFC4663E4 ON menu_link');
        $this->addSql('DROP INDEX IDX_FEE369BF4B89032C ON menu_link');
        $this->addSql('ALTER TABLE menu_link ADD link_id INT DEFAULT NULL, DROP page_id, DROP post_id, CHANGE order_link link_order INT NOT NULL');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BFADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FEE369BFADA40271 ON menu_link (link_id)');
    }
}
