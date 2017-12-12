<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171014102702 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS cart_detail (
  id INT AUTO_INCREMENT NOT NULL,
  cart_id INT DEFAULT NULL,
  product_id INT DEFAULT NULL,
  quantity INT NOT NULL,
  INDEX IDX_20821DCC1AD5CDBF (cart_id),
  INDEX IDX_20821DCC4584665A (product_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE cart_detail ADD CONSTRAINT FK_20821DCC1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE;
ALTER TABLE cart_detail ADD CONSTRAINT FK_20821DCC4584665A FOREIGN KEY (product_id) REFERENCES product (id);
SQL;

        $this->addSql($sql);


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS cart_detail;
SQL;

        $this->addSql($sql);

    }
}
