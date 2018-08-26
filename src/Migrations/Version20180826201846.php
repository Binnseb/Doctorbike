<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180826201846 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE historique (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, scenario_id INT DEFAULT NULL, solution_id INT DEFAULT NULL, vote_reponse TINYINT(1) NOT NULL, INDEX IDX_EDBFD5ECA76ED395 (user_id), INDEX IDX_EDBFD5ECE04E49DF (scenario_id), INDEX IDX_EDBFD5EC1C0BE183 (solution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5ECE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC1C0BE183 FOREIGN KEY (solution_id) REFERENCES question_reponse (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE historique');
    }
}
