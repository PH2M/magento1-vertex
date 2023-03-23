<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup **/

$installer = $this;
$installer->startSetup();

$data = array(
    array(
        'class_name' => 'Refund Adjustments',
        'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
    ),
    array(
        'class_name' => 'Gift Options',
        'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
    )
);

if (Mage::getConfig()->getModuleConfig('Enterprise_Reward') &&
    Mage::getConfig()->getModuleConfig('Enterprise_Reward')->is('active', 'true')
) {
    array_push(
        $data,
        array(
            'class_name' => 'Order Gift Wrapping',
            'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
        ),
        array(
            'class_name' => 'Item Gift Wrapping',
            'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
        ),
        array(
            'class_name' => 'Printed Gift Card',
            'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
        ),
        array(
            'class_name' => 'Reward Points',
            'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
        )
    );
}

foreach ($data as $row) {
    if (!$installer->getConnection()->fetchOne(
        $installer->getConnection()
            ->select()
            ->from($installer->getTable('tax/tax_class'))
            ->where("class_name = ?", $row["class_name"])
    )
    ) {
        $installer->getConnection()->insert(
            $installer->getTable('tax/tax_class'),
            $row
        );
    }
}

$installer->endSetup();