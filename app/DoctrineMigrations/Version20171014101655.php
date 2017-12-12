<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171014101655 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS product (
  id INT AUTO_INCREMENT NOT NULL,
  product_type_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(20,9) NOT NULL,
  description TEXT NOT NULL,
  photo_file VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  created_by VARCHAR(255) NOT NULL,
  updated_by VARCHAR(255) DEFAULT NULL,
  INDEX IDX_D34A04AD14959723 (product_type_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE product ADD CONSTRAINT FK_D34A04AD14959723 FOREIGN KEY (product_type_id) REFERENCES product_type (id);
SQL;

        $this->addSql($sql);


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS product;
SQL;

        $this->addSql($sql);

    }
}
