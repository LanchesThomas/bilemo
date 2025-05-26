<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250331110044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables customer, user, et product';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE customer (id SERIAL NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        
        $this->addSql('CREATE TABLE product (
            id SERIAL NOT NULL,
            name VARCHAR(255) NOT NULL,
            color VARCHAR(255) NOT NULL,
            price DOUBLE PRECISION NOT NULL,
            description TEXT DEFAULT NULL,
            brand VARCHAR(255) NOT NULL,
            stock INT NOT NULL,
            created_at DATE NOT NULL,
            updatedat DATE NOT NULL,
            PRIMARY KEY(id)
        )');

        $this->addSql('ALTER TABLE "user" ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6499395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6499395C3F3 ON "user" (customer_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6499395C3F3');
        $this->addSql('DROP INDEX IDX_8D93D6499395C3F3');
        $this->addSql('ALTER TABLE "user" DROP customer_id');

        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE customer');

        $this->addSql('CREATE SCHEMA public');
    }
}
