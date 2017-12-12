<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171014102621 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS command_details (
  id INT AUTO_INCREMENT NOT NULL,
  command_id INT DEFAULT NULL,
  product_id INT DEFAULT NULL,
  quantity INT NOT NULL,
  INDEX IDX_9D4C586933E1689A (command_id),
  INDEX IDX_9D4C58694584665A (product_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE command_details ADD CONSTRAINT FK_9D4C586933E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE;
ALTER TABLE command_details ADD CONSTRAINT FK_9D4C58694584665A FOREIGN KEY (product_id) REFERENCES product (id);
SQL;

        $this->addSql($sql);


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS command_details;
SQL;

        $this->addSql($sql);

    }
}
