<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223104024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP illustration');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7E12469DE2');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7EB281BE2E');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7EB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick ADD illustration VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7EB281BE2E');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7E12469DE2');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7EB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }
}
