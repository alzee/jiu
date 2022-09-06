<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906113840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_restaurant ADD restaurant_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_restaurant ADD CONSTRAINT FK_584FEF6AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_584FEF6AB1E7706E ON order_restaurant (restaurant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_restaurant DROP FOREIGN KEY FK_584FEF6AB1E7706E');
        $this->addSql('DROP INDEX IDX_584FEF6AB1E7706E ON order_restaurant');
        $this->addSql('ALTER TABLE order_restaurant DROP restaurant_id');
    }
}
