<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_Source_Taxinvoice
{

    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options[] = array(
                'label' => Mage::helper('vertextax')->__("When Invoice Created"),
                'value' => 'invoice_created'
            );
            $this->_options[] = array(
                'label' => Mage::helper('vertextax')->__("When Order Status Is"),
                'value' => 'order_status'
            );
        }

        $options = $this->_options;

        return $options;
    }
}
