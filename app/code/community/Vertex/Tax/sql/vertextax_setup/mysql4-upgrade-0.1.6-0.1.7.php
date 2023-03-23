<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup **/

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('vertextax/taxrequest');

if ($installer->tableExists($table)
    && !$installer->getConnection()->tableColumnExists($table, "tax_area_id")
) {
    $installer->getConnection()
        ->addColumn(
            $installer->getTable($table),
            'tax_area_id',
            array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'length' => 255,
            'comment' => 'Tax Jurisdictions Id'
            )
        );
}

$this->endSetup();
