<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{

    /**
     * Check whether or not to show manual Vertex invoice button on order view page
     */
    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('vertextax')->isVertexActive()) {
            if (Mage::helper('vertextax')->showManualInvoiceButton()) {
                $this->_addButton(
                    'vertex_invoice',
                    array(
                        'label'   => Mage::helper('vertextax')->__("Vertex SMB Invoice"),
                        'onclick' => 'setLocation(\'' . $this->getVertexInvoiceUrl() . '\')',
                        'class'   => 'go'
                    )
                );
            }
        }
    }

    /**
     * Vertex Invoice Url
     *
     * @return string
     */
    public function getVertexInvoiceUrl()
    {
        return $this->getUrl('*/vertex/invoicetax');
    }
}
