<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113110933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comentari');
        $this->addSql('ALTER TABLE coment ADD project_id INT NOT NULL, DROP autor, CHANGE projecte author_id INT NOT NULL');
        $this->addSql('ALTER TABLE coment ADD CONSTRAINT FK_F86E9D2F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE coment ADD CONSTRAINT FK_F86E9D2166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_F86E9D2F675F31B ON coment (author_id)');
        $this->addSql('CREATE INDEX IDX_F86E9D2166D1F9C ON coment (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comentari (id INT AUTO_INCREMENT NOT NULL, contingut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, autor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, projecte VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, data_creacio DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE coment DROP FOREIGN KEY FK_F86E9D2F675F31B');
        $this->addSql('ALTER TABLE coment DROP FOREIGN KEY FK_F86E9D2166D1F9C');
        $this->addSql('DROP INDEX IDX_F86E9D2F675F31B ON coment');
        $this->addSql('DROP INDEX IDX_F86E9D2166D1F9C ON coment');
        $this->addSql('ALTER TABLE coment ADD autor VARCHAR(255) NOT NULL, ADD projecte INT NOT NULL, DROP author_id, DROP project_id');
    }
}
