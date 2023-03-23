<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

$installer = $this;
$installer->startSetup();
$table = $installer->getTable('sales/quote_address');

/** @var $installer Mage_Catalog_Model_Resource_Setup **/

if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/quote_address'), "tax_area_id")) {
    $installer->getConnection()->addColumn(
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

if (version_compare(Mage::getVersion(), "1.4.1.0", ">")) {
    if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_address'), "tax_area_id")) {
        $installer->getConnection()->addColumn(
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
}

$this->endSetup();
