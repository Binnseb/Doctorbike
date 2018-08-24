<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180823173527 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDA936C30EF');
        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDAA85749DF');
        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDAD48C2DE0');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDA936C30EF FOREIGN KEY (id_question_si_jenesaispas_id) REFERENCES question_reponse (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDAA85749DF FOREIGN KEY (id_question_si_oui_id) REFERENCES question_reponse (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDAD48C2DE0 FOREIGN KEY (id_question_si_non_id) REFERENCES question_reponse (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDAA85749DF');
        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDAD48C2DE0');
        $this->addSql('ALTER TABLE question_reponse DROP FOREIGN KEY FK_516A0BDA936C30EF');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDAA85749DF FOREIGN KEY (id_question_si_oui_id) REFERENCES question_reponse (id)');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDAD48C2DE0 FOREIGN KEY (id_question_si_non_id) REFERENCES question_reponse (id)');
        $this->addSql('ALTER TABLE question_reponse ADD CONSTRAINT FK_516A0BDA936C30EF FOREIGN KEY (id_question_si_jenesaispas_id) REFERENCES question_reponse (id)');
    }
}
