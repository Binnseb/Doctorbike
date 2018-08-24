<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180809160451 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mot_cle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_reponse (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, scenario_id INT NOT NULL, question VARCHAR(255) NOT NULL, aide VARCHAR(255) NOT NULL, est_solution TINYINT(1) NOT NULL, est_premiere_question TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_516A0BDA3DA5256D (image_id), INDEX IDX_516A0BDAE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, est_termine TINYINT(1) NOT NULL, est_valide TINYINT(1) NOT NULL, INDEX IDX_3E45C8D8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_mot_cle (scenario_id INT NOT NULL, mot_cle_id INT NOT NULL, INDEX IDX_B054122AE04E49DF (scenario_id), INDEX IDX_B054122AFE94535C (mot_cle_id), PRIMARY KEY(scenario_id, mot_cle_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDA3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDAE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE scenario ADD CONSTRAINT FK_3E45C8D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scenario_mot_cle ADD CONSTRAINT FK_B054122AE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_mot_cle ADD CONSTRAINT FK_B054122AFE94535C FOREIGN KEY (mot_cle_id) REFERENCES mot_cle (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scenario_mot_cle DROP FOREIGN KEY FK_B054122AFE94535C');
        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDAE04E49DF');
        $this->addSql('ALTER TABLE scenario_mot_cle DROP FOREIGN KEY FK_B054122AE04E49DF');
        $this->addSql('DROP TABLE mot_cle');
        $this->addSql('DROP TABLE question_reponse');
        $this->addSql('DROP TABLE scenario');
        $this->addSql('DROP TABLE scenario_mot_cle');
    }
}
