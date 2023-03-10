<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Habbim\IdToUuid\IdToUuidMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310201101 extends IdToUuidMigration
{
    public function postUp(Schema $schema): void
    {
        $this->migrate('book');
        $this->migrate('category');
    }
}
