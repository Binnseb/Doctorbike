<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180807153401 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE marque ADD image_id INT DEFAULT NULL, DROP logo');
        $this->addSql('ALTER TABLE marque ADD CONSTRAINT FK_5A6F91CE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A6F91CE3DA5256D ON marque (image_id)');
        $this->addSql('ALTER TABLE user ADD image_id INT DEFAULT NULL, DROP avatar');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6493DA5256D ON user (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE marque DROP FOREIGN KEY FK_5A6F91CE3DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493DA5256D');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP INDEX UNIQ_5A6F91CE3DA5256D ON marque');
        $this->addSql('ALTER TABLE marque ADD logo VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP image_id');
        $this->addSql('DROP INDEX UNIQ_8D93D6493DA5256D ON user');
        $this->addSql('ALTER TABLE user ADD avatar VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP image_id, CHANGE access access SMALLINT NOT NULL');
    }
}
