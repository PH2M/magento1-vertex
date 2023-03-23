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
    if (!$installer->getConnection()->raw_fetchRow(
        "SHOW KEYS FROM `{$taxRequestTable}` 
        WHERE `key_name`='IDX_VERTEX_VERTEXTAXREQUEST_REQUEST_ID';"
    )
    ) {
        $installer->getConnection()->addKey(
            $taxRequestTable,
            "IDX_VERTEX_VERTEXTAXREQUEST_REQUEST_ID",
            array(
                'request_id'
            ),
            "unique"
        );
    }

    if (!$installer->getConnection()->raw_fetchRow(
        "SHOW KEYS FROM `{$taxRequestTable}` 
        WHERE `key_name`='IDX_VERTEX_VERTEXTAXREQUEST_REQUEST_TYPE';"
    )
    ) {
        $installer->getConnection()->addKey(
            $taxRequestTable,
            "IDX_VERTEX_VERTEXTAXREQUEST_REQUEST_TYPE",
            array(
                'request_type'
            )
        );
    }

    if (!$installer->getConnection()->raw_fetchRow(
        "SHOW KEYS FROM `{$taxRequestTable}` 
        WHERE `key_name`='IDX_VERTEX_VERTEXTAXREQUEST_ORDER_ID';"
    )
    ) {
        $installer->getConnection()->addKey(
            $taxRequestTable,
            "IDX_VERTEX_VERTEXTAXREQUEST_ORDER_ID",
            array(
                'order_id'
            )
        );
    }
}

$this->endSetup();
