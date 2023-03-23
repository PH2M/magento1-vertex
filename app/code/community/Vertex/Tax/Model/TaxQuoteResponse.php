<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_TaxQuoteResponse extends Mage_Core_Model_Abstract
{
    /**
     * @param $responseObject
     *
     * @return Vertex_Tax_Model_TaxQuoteResponse
     */
    public function parseResponse(stdClass $responseObject)
    {
        if (is_array($responseObject->QuotationResponse->LineItem)) {
            $taxLineItems = $responseObject->QuotationResponse->LineItem;
        } else {
            $taxLineItems[] = $responseObject->QuotationResponse->LineItem;
        }

        $this->setTaxLineItems($taxLineItems);
        $this->setLineItemsCount(count($taxLineItems));
        $this->prepareQuoteTaxedItems($taxLineItems);

        return $this;
    }

    /**
     * @param array $itemsTax
     */
    public function prepareQuoteTaxedItems(array $itemsTax)
    {
        $quoteTaxedItems = array();

        foreach ($itemsTax as $item) {
            $itemTotalTax = $item->TotalTax->_;
            $taxPercent = 0;
            foreach ($item->Taxes as $key => $taxValue) {
                if (is_object($taxValue) && property_exists($taxValue, "EffectiveRate")) {
                    $taxPercent += (float) $taxValue->EffectiveRate;
                } elseif ($key == "EffectiveRate") {
                    $taxPercent += (float) $taxValue;
                }
            }

            $taxPercent = $taxPercent * 100;
            $quoiteItemId = $item->lineItemId;
            $taxItemInfo = new Varien_Object();
            $taxItemInfo->setProductClass($item->Product->productClass);
            $taxItemInfo->setProductSku($item->Product->_);

            if (property_exists($item, "Quantity")) {
                $taxItemInfo->setProductQty($item->Quantity->_);
            }

            if (property_exists($item, "UnitPrice")) {
                $taxItemInfo->setUnitPrice($item->UnitPrice->_);
            }

            $taxItemInfo->setTaxPercent($taxPercent);
            $taxItemInfo->setBaseTaxAmount($itemTotalTax);
            $taxItemInfo->setTaxAmount($itemTotalTax);
            $quoteTaxedItems[$quoiteItemId] = $taxItemInfo;
        }

        $this->setQuoteTaxedItems($quoteTaxedItems);
    }

}
