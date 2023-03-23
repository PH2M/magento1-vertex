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
        'request_id',
        'request_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
            'length' => 20,
            'unsigned' => true,
            'nullable' => false,
            'identity' => true,
            'primary' => true
        )
    );
}

$this->endSetup();
