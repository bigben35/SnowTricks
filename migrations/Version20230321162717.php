<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321162717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_trick DROP FOREIGN KEY FK_716A31EFB281BE2E');
        $this->addSql('ALTER TABLE comment_trick ADD CONSTRAINT FK_716A31EFB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_trick DROP FOREIGN KEY FK_716A31EFB281BE2E');
        $this->addSql('ALTER TABLE comment_trick ADD CONSTRAINT FK_716A31EFB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
    }
}
