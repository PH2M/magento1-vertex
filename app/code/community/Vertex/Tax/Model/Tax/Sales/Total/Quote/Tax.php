<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_Tax_Sales_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::collect($address);
            return $this;
        }

        $addressType = $address->getAddressType();

        if ($this->checkAddress($address, $addressType) !== null) {
            return $this;
        }

        Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
        $this->_roundingDeltas = array();
        $this->_baseRoundingDeltas = array();
        $this->_hiddenTaxes = array();
        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);

        $this->_store = $address->getQuote()->getStore();
        $customer = $address->getQuote()->getCustomer();

        if ($customer) {
            $this->_calculator->setCustomer($customer);
        }

        if (!$address->getAppliedTaxesReset()) {
            $address->setAppliedTaxes(array());
        }

        $items = Mage::helper('vertextax')->getAllNonNominalItems($address);
        if (empty($items)) {
            return $this;
        }

//        $request = new Varien_Object();

        $request = $this->_calculator->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $address->getQuote()->getCustomerTaxClassId(),
            $this->_store
        );

        if ($this->_config->priceIncludesTax($this->_store)) {
            $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests(
                $this->_calculator->getRateOriginRequest($this->_store),
                $request
            );
        }

        $itemsVertexTaxes = Mage::helper('vertextax')->taxQuoteItems($address);
        $request->setItemsVertexTax($itemsVertexTaxes);

        $this->_rowBaseCalculation($address, $request);
        $this->_addAmount($address->getExtraTaxAmount());
        $this->_addBaseAmount($address->getBaseExtraTaxAmount());
        $this->_calculateShippingTax($address, $request);

        if (method_exists($this, "_processHiddenTaxes")) {
            $this->_processHiddenTaxes();
        }

        $this->_roundTotals($address);

        return $this;
    }

    /**
     * Check address information
     *
     * @param $addressType
     *
     * @return $this|null
     */
    protected function checkAddress($address, $addressType)
    {
        if ($address->getQuote()->isVirtual() && $addressType == 'shipping') {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log(
                    "Quote request was not sent. Address Type: " . $addressType . ", 
                Order is virtual.", null,
                    'vertex.log',
                    true
                );
            }
            return $this;
        }

        if (!$address->getQuote()->isVirtual() && !$address->getShippingMethod()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log(
                    "Quote request was not sent. Order is not virtual and doesnt have shipping method.",
                    null,
                    'vertex.log',
                    true
                );
            }
            return $this;
        }

        if (!$address->getStreet1() && !Mage::helper('vertextax')->allowCartQuote()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Quote request was not sent. Street not specified.", null, 'vertex.log', true);
            }
            return $this;
        }

        if (!$address->getCountryId() || !$address->getRegion() ||
            !$address->getPostcode() || empty(Mage::helper('vertextax')->getAllNonNominalItems($address))) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Quote request was not sent. Address not specified. ", null, 'vertex.log', true);
            }
            return $this;
        }

        if (Mage::app()->getRequest()->getControllerName() == 'cart' && !Mage::helper('vertextax')->allowCartQuote()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Quote request was not sent. Address area id not specified yet.", null, 'vertex.log', true);
            }
            return $this;
        }

        if (!Mage::helper('vertextax')->canQuoteTax()) {
            Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Quote request not have enough information", null, 'vertex.log', true);
            }
            return $this;
        }

        return null;
    }

    /**
     * Round the total amounts in address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return Mage_Tax_Model_Sales_Total_Quote_Tax
     */
    protected function _roundTotals(Mage_Sales_Model_Quote_Address $address)
    {
        // initialize the delta to a small number to avoid non-deterministic behavior with rounding of 0.5
        $totalDelta = 0.000001;
        $baseTotalDelta = 0.000001;
        /*
         * The order of rounding is import here.
         * Tax is rounded first, to be consistent with unit based calculation.
         * Hidden tax and shipping_hidden_tax are rounded next, which are really part of tax.
         * Shipping is rounded before subtotal to minimize the chance that subtotal is
         * rounded differently because of the delta.
         * Here is an example: 19.2% tax rate, subtotal = 49.95, shipping = 9.99, discount = 20%
         * subtotalExclTax = 41.90436, tax = 7.7238, hidden_tax = 1.609128, shippingPriceExclTax = 8.38087
         * shipping_hidden_tax = 0.321826, discount = -11.988
         * The grand total is 47.952 ~= 47.95
         * The rounded values are:
         * tax = 7.72, hidden_tax = 1.61, shipping_hidden_tax = 0.32,
         * shipping = 8.39 (instead of 8.38 from simple rounding), subtotal = 41.9, discount = -11.99
         * The grand total calculated from the rounded value is 47.95
         * If we simply round each value and add them up, the result is 47.94, which is one penny off
         */
        $totalCodes = array(
            'tax',
            'hidden_tax',
            'shipping_hidden_tax',
            'shipping',
            'subtotal',
            'weee',
            'discount',
            'grand_total'
        );
        foreach ($totalCodes as $totalCode) {
            $exactAmount = $address->getTotalAmount($totalCode);
            $baseExactAmount = $address->getBaseTotalAmount($totalCode);
            if (!$exactAmount && !$baseExactAmount) {
                continue;
            }

            $roundedAmount = $this->_calculator->round($exactAmount + $totalDelta);
            $baseRoundedAmount = $this->_calculator->round($baseExactAmount + $baseTotalDelta);
            $address->setTotalAmount($totalCode, $roundedAmount);
            $address->setBaseTotalAmount($totalCode, $baseRoundedAmount);
            $totalDelta = $exactAmount + $totalDelta - $roundedAmount;
            $baseTotalDelta = $baseExactAmount + $baseTotalDelta - $baseRoundedAmount;
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $taxRateRequest
     *
     * @return Vertex_Tax_Model_Tax_Sales_Total_Quote_Tax
     */
    protected function _rowBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::_rowBaseCalculation($address, $taxRateRequest);
            return $this;
        }

        $items = Mage::helper('vertextax')->getAllNonNominalItems($address);
        $itemTaxGroups = array();

        $itemsVertexTaxes = $taxRateRequest->getItemsVertexTax();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId(
                        $child->getProduct()
                            ->getTaxClassId()
                    );
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcRowTaxAmount($child, $itemsVertexTaxes);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }

                    $this->_saveAppliedTaxes(
                        $address, $applied, $child->getTaxAmount(), $child->getBaseTaxAmount(), $rate
                    );
                    $child->setTaxRates($applied);
                }

                $this->_recalculateParent($item);
            } else {
                $taxRateRequest->setProductClassId(
                    $item->getProduct()
                        ->getTaxClassId()
                );
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcRowTaxAmount($item, $itemsVertexTaxes);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }

                $this->_saveAppliedTaxes($address, $applied, $item->getTaxAmount(), $item->getBaseTaxAmount(), $rate);
                $item->setTaxRates($applied);
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }

        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     * @param $item
     * @param $rate
     * @param string  $taxGroups
     * @param string  $taxId
     * @param bool    $recalculateRowTotalInclTax
     *
     * @return Vertex_Tax_Model_Tax_Sales_Total_Quote_Tax
     */
    protected function _calcRowTaxAmount($item, $rate, &$taxGroups = NULL, $taxId = NULL, $recalculateRowTotalInclTax = false)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::_calcRowTaxAmount($item, $rate);
            return $this;
        }

        $subtotal = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal = $baseTaxSubtotal = $item->getBaseTaxableAmount();
        $rowTax = 0;
        $baseRowTax = 0;
        $taxRate = 0;
        $itemTax = $rate[$item->getId()];

        if ($itemTax instanceof Varien_Object) {
            $rowTax = $itemTax->getTaxAmount();
            $baseRowTax = $itemTax->getBaseTaxAmount();
            $taxRate = $itemTax->getTaxPercent();
        } else {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("ItemTax is not instance of Varien_Object. ", null, 'vertex.log', true);
            }
        }

        $item->setTaxPercent($taxRate);
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));
        $rowTotalInclTax = $item->getRowTotalInclTax();

        if (!isset($rowTotalInclTax)) {
            $weeeTaxBeforeDiscount = 0;
            $baseWeeeTaxBeforeDiscount = 0;

            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal + $weeeTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseWeeeTaxBeforeDiscount);
            } else {
                $taxCompensation = $item->getDiscountTaxCompensation() ? $item->getDiscountTaxCompensation() : 0;
                $item->setRowTotalInclTax($subtotal + $rowTax + $taxCompensation);
                $item->setBaseRowTotalInclTax($baseSubtotal + $baseRowTax + $item->getBaseDiscountTaxCompensation());
            }
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $taxRateRequest
     *
     * @return Vertex_Tax_Model_Tax_Sales_Total_Quote_Tax
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::_calculateShippingTax($address, $taxRateRequest);
            return $this;
        }

        $itemsVertexTaxes = $taxRateRequest->getItemsVertexTax();
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        $tax = 0;
        $baseTax = 0;
        $rate = 0;
        $shippingTax = 0;

        if (is_array($itemsVertexTaxes) && array_key_exists('shipping', $itemsVertexTaxes)) {
            $shippingTax = $itemsVertexTaxes['shipping'];
        }

        if (Mage::helper('vertextax')->isLoggingEnabled()) {
            Mage::log($itemsVertexTaxes, null, 'vertex.log', true);
        }

        if ($shippingTax instanceof Varien_Object) {
            $tax = $shippingTax->getTaxAmount();
            $baseTax = $shippingTax->getBaseTaxAmount();
            $rate = $shippingTax->getTaxPercent();
        } else {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("calculateShippingTax::shippingTax is not instance of Varien_Object. ", null, 'vertex.log', true);
            }
        }

        $this->_addAmount(max(0, $tax));
        $this->_addBaseAmount(max(0, $baseTax));
        $address->setShippingTaxAmount(max(0, $tax));
        $address->setBaseShippingTaxAmount(max(0, $baseTax));

        $applied = $this->_calculator->getAppliedRates($taxRateRequest);
        $this->_saveAppliedTaxes($address, $applied, $tax, $baseTax, $rate);

        return $this;
    }
}
