<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230802091815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_link ADD parent_id INT DEFAULT NULL, ADD menu_link_id INT DEFAULT NULL, CHANGE cus_name cus_name JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BF727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_link (id)');
        $this->addSql('ALTER TABLE menu_link ADD CONSTRAINT FK_FEE369BF257F1FCF FOREIGN KEY (menu_link_id) REFERENCES menu_link (id)');
        $this->addSql('CREATE INDEX IDX_FEE369BF727ACA70 ON menu_link (parent_id)');
        $this->addSql('CREATE INDEX IDX_FEE369BF257F1FCF ON menu_link (menu_link_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BF727ACA70');
        $this->addSql('ALTER TABLE menu_link DROP FOREIGN KEY FK_FEE369BF257F1FCF');
        $this->addSql('DROP INDEX IDX_FEE369BF727ACA70 ON menu_link');
        $this->addSql('DROP INDEX IDX_FEE369BF257F1FCF ON menu_link');
        $this->addSql('ALTER TABLE menu_link DROP parent_id, DROP menu_link_id, CHANGE cus_name cus_name VARCHAR(255) DEFAULT NULL');
    }
}
