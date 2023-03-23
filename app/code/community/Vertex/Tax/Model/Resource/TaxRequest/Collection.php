<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

spl_autoload_register(
    function ($class) {
        if (strcasecmp($class, 'Mage_Core_Model_Resource_Db_Collection_Abstract') === 0) {
            if (!class_exists('Mage_Core_Model_Resource_Db_Collection_Abstract', false)) {
                class_alias(
                    'Mage_Core_Model_Mysql4_Collection_Abstract',
                    'Mage_Core_Model_Resource_Db_Collection_Abstract'
                );
            }
        }
    }, true, true
);

class Vertex_Tax_Model_Resource_Taxrequest_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('vertextax/taxrequest');
    }

    /**
     * @param $requestType
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setRequestTypeFilter($requestType)
    {
        return $this->addFieldToFilter('main_table.request_type', $requestType);
    }
}