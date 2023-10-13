<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009201853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE sales_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sales_items_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sales (id INT NOT NULL, distance INT NOT NULL, total DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sales.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sales.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sales_items (id INT NOT NULL, wine_id INT NOT NULL, quantity INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, sale_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE sales_items ADD CONSTRAINT FK_sale_item_wines FOREIGN KEY (wine_id) REFERENCES wines (id)');
        $this->addSql('ALTER TABLE sales_items ADD CONSTRAINT FK_sale_item_sales FOREIGN KEY (sale_id) REFERENCES sales (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE sales_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sales_items_id_seq CASCADE');
        $this->addSql('ALTER TABLE sales_items DROP CONSTRAINT FK_sale_item_sales');
        $this->addSql('ALTER TABLE sales_items DROP CONSTRAINT FK_sale_item_wines');
        $this->addSql('DROP TABLE sales');
        $this->addSql('DROP TABLE sales_items');
    }
}
