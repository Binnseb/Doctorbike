<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180830140717 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scenario_mot_cle DROP FOREIGN KEY FK_B054122AFE94535C');
        $this->addSql('DROP TABLE mot_cle');
        $this->addSql('DROP TABLE scenario_mot_cle');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mot_cle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_mot_cle (scenario_id INT NOT NULL, mot_cle_id INT NOT NULL, INDEX IDX_B054122AE04E49DF (scenario_id), INDEX IDX_B054122AFE94535C (mot_cle_id), PRIMARY KEY(scenario_id, mot_cle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scenario_mot_cle ADD CONSTRAINT FK_B054122AE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_mot_cle ADD CONSTRAINT FK_B054122AFE94535C FOREIGN KEY (mot_cle_id) REFERENCES mot_cle (id) ON DELETE CASCADE');
    }
}
