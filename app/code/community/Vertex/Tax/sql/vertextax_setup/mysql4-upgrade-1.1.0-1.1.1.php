<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

$installer = $this;
$installer->startSetup();

$taxRequestTable = $this->getTable('vertextax/taxrequest');

/** @var $installer Mage_Catalog_Model_Resource_Setup **/

if ($installer->tableExists($taxRequestTable)) {
    if (!$installer->getConnection()->tableColumnExists($taxRequestTable, "sub_total")) {
        $installer->getConnection()->addColumn(
            $installer->getTable($taxRequestTable),
            'sub_total',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 255,
                'comment' => 'Response Subtotal Amount'
            )
        );
    }

    if (!$installer->getConnection()->tableColumnExists($taxRequestTable, "total")) {
        $installer->getConnection()->addColumn(
            $installer->getTable($taxRequestTable),
            'total',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 255,
                'comment' => 'Response Total Amount'
            )
        );
    }

    if (!$installer->getConnection()->tableColumnExists($taxRequestTable, "lookup_result")) {
        $installer->getConnection()->addColumn(
            $installer->getTable($taxRequestTable),
            'lookup_result',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 255,
                'comment' => 'Tax Area Response Lookup Result'
            )
        );
    }
}

$this->endSetup();
