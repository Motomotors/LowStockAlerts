<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `ucommerz_stockreport`;
CREATE TABLE IF NOT EXISTS `ucommerz_stockreport` (
  `record_id` int(11) NOT NULL auto_increment,
  `sku` VARCHAR(100),
  `timestamp` INT(20),
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ucommerz Stockreport table';
");

$installer->endSetup();
