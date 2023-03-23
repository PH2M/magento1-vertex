<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_Total_Quote_Tax_Giftwrapping extends Enterprise_GiftWrapping_Model_Total_Quote_Tax_Giftwrapping
{
    /**
     * @var Mage_Core_Model_Abstract|Mage_Tax_Model_Calculation
     */
    protected $_taxCalculationModel;

    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    public function __construct()
    {
        $this->setCode('tax_giftwrapping');
        $this->_taxCalculationModel = Mage::getSingleton('tax/calculation');
        $this->_helper = Mage::helper('enterprise_giftwrapping');
    }

    /**
     * Collect wrapping tax total for items
     *
     * @param $address
     *
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _collectWrappingForItems($address)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::_collectPrintedCard($address);
            return $this;
        }

        $items = $this->_getAddressItems($address);
        $wrappingForItemsBaseTaxAmount = false;
        $wrappingForItemsTaxAmount = false;

        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem() || !$item->getGwId()) {
                continue;
            }

            $itemsVertexTaxes = Mage::helper('vertextax')->taxQuoteItems($address);
            $wrappingTaxAmount = 0;
            $wrappingBaseTaxAmount = 0;

            if (is_array($itemsVertexTaxes) && array_key_exists('gift_wrap_' . $item->getId(), $itemsVertexTaxes)) {
                $itemTax = $itemsVertexTaxes['gift_wrap_' . $item->getId()];
            }

            if ($itemTax instanceof Varien_Object) {
                $wrappingTaxAmount = $itemTax->getTaxAmount();
                $wrappingBaseTaxAmount = $itemTax->getBaseTaxAmount();
            } else {
                if (Mage::helper('vertextax')->isLoggingEnabled()) {
                    Mage::log("itemTax for gift wrapping is not instance of Varien_Object. ", null, 'vertex.log', true);
                }
            }

            $item->setGwBaseTaxAmount($wrappingBaseTaxAmount);
            $item->setGwTaxAmount($wrappingTaxAmount);

            $wrappingForItemsBaseTaxAmount += $wrappingBaseTaxAmount;
            $wrappingForItemsTaxAmount += $wrappingTaxAmount;
        }

        $address->setGwItemsBaseTaxAmount($wrappingForItemsBaseTaxAmount);
        $address->setGwItemsTaxAmount($wrappingForItemsTaxAmount);
        return $this;
    }

    /**
     * Collect wrapping tax total for quote
     *
     * @param $address
     *
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _collectWrappingForQuote($address)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::_collectWrappingForQuote($address);
            return $this;
        }

        $wrappingBaseTaxAmount = false;
        $wrappingTaxAmount = false;
        if ($this->_quoteEntity->getGwId()) {
            $itemsVertexTaxes = Mage::helper('vertextax')->taxQuoteItems($address);
            $wrappingTaxAmount = 0;
            $wrappingBaseTaxAmount = 0;

            if (is_array($itemsVertexTaxes) && array_key_exists('gift_wrapping', $itemsVertexTaxes)) {
                $wrappingTax = $itemsVertexTaxes['gift_wrapping'];
            }

            if ($wrappingTax instanceof Varien_Object) {
                $wrappingBaseTaxAmount = $wrappingTax->getTaxAmount();
                $wrappingTaxAmount = $wrappingTax->getBaseTaxAmount();
            } else {
                if (Mage::helper('vertextax')->isLoggingEnabled()) {
                    Mage::log("wrappingTax is not instance of Varien_Object. ", null, 'vertex.log', true);
                }
            }
        }

        $address->setGwBaseTaxAmount($wrappingBaseTaxAmount);
        $address->setGwTaxAmount($wrappingTaxAmount);
        return $this;
    }

    /**
     * Collect printed card tax total for quote
     *
     * @param $address
     *
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _collectPrintedCard($address)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::_collectPrintedCard($address);
            return $this;
        }

        $printedCardBaseTaxAmount = false;
        $printedCardTaxAmount = false;
        if ($this->_quoteEntity->getGwAddCard()) {
            $itemsVertexTaxes = Mage::helper('vertextax')->taxQuoteItems($address);
            $printedCardTaxAmount = 0;
            $printedCardBaseTaxAmount = 0;

            if (is_array($itemsVertexTaxes) && array_key_exists('gift_print_card', $itemsVertexTaxes)) {
                $wrappingPrintCardTax = $itemsVertexTaxes['gift_print_card'];
            }

            if ($wrappingPrintCardTax instanceof Varien_Object) {
                $printedCardBaseTaxAmount = $wrappingPrintCardTax->getTaxAmount();
                $printedCardTaxAmount = $wrappingPrintCardTax->getBaseTaxAmount();
            } else {
                if (Mage::helper('vertextax')->isLoggingEnabled()) {
                    Mage::log("wrappingPrintCardTax is not instance of Varien_Object. ", null, 'vertex.log', true);
                }
            }
        }

        $address->setGwCardBaseTaxAmount($printedCardBaseTaxAmount);
        $address->setGwCardTaxAmount($printedCardTaxAmount);

        return $this;
    }
}
