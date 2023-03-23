<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Block_Adminhtml_System_Config_Form_Field_VertexStatus
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Adds Enabled/Disabled check for admin panel
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if (!empty($code = Mage::getSingleton('adminhtml/config_data')->getStore())) {
            $storeId = Mage::getModel('core/store')->load($code)->getId();
        } elseif (!empty($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) {
            $websiteId = Mage::getModel('core/website')->load($code)->getId();
            $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
        } else {
            $storeId = 0;
        }

        $helper = Mage::helper('vertextax');
        if (!$helper->isVertexActive($storeId)) {
            $status = "Disabled";
            $state = "critical";
        } else {
            $status = $helper->checkCredentials($storeId);
            if ($status == 'Valid') {
                $state = "notice";
            } else {
                $state = "minor";
            }
        }

        return '<span class="grid-severity-' . $state . '">
                    <span style=" background-color: #FAFAFA;">' . $status . '</span>
                </span>';
    }
}
