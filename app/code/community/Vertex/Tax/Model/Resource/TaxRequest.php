<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

spl_autoload_register(
    function ($class) {
        if (strcasecmp($class, 'Mage_Core_Model_Resource_Db_Abstract') === 0) {
            if (!class_exists('Mage_Core_Model_Resource_Db_Abstract', false)) {
                class_alias('Mage_Core_Model_Mysql4_Abstract', 'Mage_Core_Model_Resource_Db_Abstract');
            }
        }
    }, true, true
);

class Vertex_Tax_Model_Resource_TaxRequest extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('vertextax/taxrequest', 'request_id');
    }
}