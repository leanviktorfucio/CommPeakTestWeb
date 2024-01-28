<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128154024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE call_data (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, datetime DATETIME NOT NULL, duration INT NOT NULL, phone_number VARCHAR(20) NOT NULL, ip VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_9A72943F9395C3F393F3C6CA (customer_id, datetime), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE call_data_statistics (customer_id INT NOT NULL, number_of_calls_within_same_continent INT NOT NULL, duration_of_calls_within_same_continent INT NOT NULL, total_number_of_calls INT NOT NULL, total_duration_of_calls INT NOT NULL, PRIMARY KEY(customer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE call_data');
        $this->addSql('DROP TABLE call_data_statistics');
    }
}
