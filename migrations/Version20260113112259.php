<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113112259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coment DROP FOREIGN KEY `FK_F86E9D2166D1F9C`');
        $this->addSql('ALTER TABLE coment DROP FOREIGN KEY `FK_F86E9D2F675F31B`');
        $this->addSql('DROP TABLE coment');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coment (id INT AUTO_INCREMENT NOT NULL, contingut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, data_creacio DATE NOT NULL, author_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_F86E9D2166D1F9C (project_id), INDEX IDX_F86E9D2F675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE coment ADD CONSTRAINT `FK_F86E9D2166D1F9C` FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE coment ADD CONSTRAINT `FK_F86E9D2F675F31B` FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
