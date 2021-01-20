<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108093034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Ajout du champs commentaire';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings ADD comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE bookings RENAME INDEX idx_e00cedde8b7e4006 TO IDX_7A853C358B7E4006');
        $this->addSql('ALTER TABLE bookings RENAME INDEX idx_e00cedde4f34d596 TO IDX_7A853C354F34D596');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP comment');
        $this->addSql('ALTER TABLE bookings RENAME INDEX idx_7a853c354f34d596 TO IDX_E00CEDDE4F34D596');
        $this->addSql('ALTER TABLE bookings RENAME INDEX idx_7a853c358b7e4006 TO IDX_E00CEDDE8B7E4006');
    }
}
