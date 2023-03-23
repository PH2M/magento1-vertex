<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup * */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'vertextax/taxrequest'
 */
try {
    $table = $installer->getConnection()->newTable($installer->getTable('vertextax/taxrequest'))
        ->addColumn(
            'request_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
            ), 'Request Id'
        )
        ->addColumn(
            'request_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false
            ), 'Request Type'
        )
        ->addColumn(
            'quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
            'nullable' => false,
            'default' => '0'
            ), 'Quote Id'
        )
        ->addColumn(
            'order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
            'nullable' => false,
            'default' => '0'
            ), 'Order Id'
        )
        ->addColumn(
            'total_tax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
            'default' => '0'
            ), 'Total Tax'
        )
        ->addColumn(
            'request_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false,
            ), 'Request Date'
        )
        ->addColumn(
            'request_xml', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            ), 'Request XML'
        )
        ->addColumn(
            'response_xml', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            ), 'Response XML'
        )
        ->setComment('Log of requests to Vertex SMB');
    $installer->getConnection()->createTable($table);
} catch (Exception $e) {
    if (Mage::helper('vertextax')->isLoggingEnabled()) {
        Mage::log($e->getMessage(), null, 'vertex.log', true);
    }
}

/**
 * Customer Attribute
 */
$entity = $this->getEntityTypeId('customer');

if (!$this->attributeExists($entity, 'customer_code')) {
    $this->addAttribute(
        $entity,
        'customer_code',
        array(
            'type' => 'text',
            'label' => 'Vertex Customer Code',
            'input' => 'text',
            'visible' => true,
            'required' => false,
            'default_value' => '',
            'user_defined' => true
        )
    );

    $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_code');
    $attribute->setData(
        'used_in_forms',
        array(
            'adminhtml_customer'
        )
    )->save();
}

$this->endSetup();

