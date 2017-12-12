<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171014101657 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS command (
  id INT AUTO_INCREMENT NOT NULL,
  user_id INT DEFAULT NULL,
  delivery_type_id INT DEFAULT NULL,
  ordered_at DATETIME NOT NULL,
  total DECIMAL(20,9) NOT NULL,
  delivery_status VARCHAR(255) NOT NULL,
  facture_file VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  delivered_at DATETIME DEFAULT NULL,
  created_by VARCHAR(255) NOT NULL,
  updated_by VARCHAR(255) DEFAULT NULL,
  INDEX IDX_8ECAEAD4A76ED395 (user_id),
  INDEX IDX_8ECAEAD4CF52334D (delivery_type_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4CF52334D FOREIGN KEY (delivery_type_id) REFERENCES delivery_type (id);
SQL;

        $this->addSql($sql);


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS command;
SQL;

        $this->addSql($sql);

    }
}
