<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup **/

$installer = $this;
$installer->startSetup();

if ($installer->tableExists($installer->getTable('vertextax/taxrequest'))) {
    $installer->getConnection()->changeColumn(
        $installer->getTable('vertextax/taxrequest'),
        'quote_id',
        'quote_id',
        "bigint(20) NOT NULL DEFAULT '0'"
    );
    $installer->getConnection()->changeColumn(
        $installer->getTable('vertextax/taxrequest'),
        'order_id',
        'order_id',
        "bigint(20) NOT NULL DEFAULT '0'"
    );
}

$this->endSetup();
