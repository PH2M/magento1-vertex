<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_TaxQuote extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('vertextax/taxquote');
    }

    /**
     * @return Vertex_Tax_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('vertextax');
    }

    /**
     *
     * @param $information
     *
     * @return boolean
     */
    public function getTaxQuote($information)
    {
        if (Mage::helper('vertextax')->isLoggingEnabled()) {
            Mage::log("Vertex_Tax_Model_TaxQuote::getTaxQuote", null, 'vertex.log', true);
        }

        if ($this->getHelper()->getSourcePath() == 'cart_checkout_index' ||
            $this->getHelper()->getSourcePath() == 'cart_checkout_couponPost') {
            $information['tax_area_id'] = '';
            $information['customer_street1'] = '';
            $information['customer_street2'] = '';
        }

        $information['request_type'] = 'QuotationRequest';
        $request = Mage::getModel('vertextax/requestItem')->setData($information)->exportAsArray();
        $this->getHelper()->sanitizeLineItems($request);
        $taxQuoteResult = Mage::getModel('vertextax/vertex')->sendApiRequest($request, 'quote');

        if ($taxQuoteResult instanceof Exception) {
            if (Mage::app()->getRequest()->getControllerName() == 'onepage' ||
                Mage::app()->getRequest()->getControllerName() == 'sales_order_create') {
                if (Mage::helper('vertextax')->isLoggingEnabled()) {
                    Mage::log(
                        "Quote Request Error: " . $taxQuoteResult->getMessage() .
                        "Controller:  " . $this->getHelper()->getSourcePath(),
                        null,
                        'vertex.log',
                        true
                    );
                }
                $result = array(
                    'error'   => 1,
                    'message' => "Tax calculation request error. Please check your address"
                );

                $action = Mage::app()->getRequest()->getActionName();
                Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                if (Mage::helper('vertextax')->isLoggingEnabled()) {
                    Mage::log("Controller action to dispatch " . $action, null, 'vertex.log', true);
                }
                Mage::app()->getFrontController()
                    ->getAction()
                    ->setFlag($action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

                return false;
            }

            if ($this->getHelper()->getSourcePath() == 'cart_checkout_index' ||
                $this->getHelper()->getSourcePath() == 'cart_checkout_couponPost') {
                $this->getHelper()->getSession()->addError(
                    Mage::helper('core')->escapeHtml("Tax Calculation Request Error. Please check your address")
                );
            }

            return false;
        }

        $responseModel = Mage::getModel('vertextax/TaxQuoteResponse')->parseResponse($taxQuoteResult);
        $this->setResponse($responseModel);
        $quoteTaxedItems = $responseModel->getQuoteTaxedItems();

        return $quoteTaxedItems;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function collectQuotedata(Mage_Sales_Model_Quote_Address $address)
    {
        $information = array();
        $information = $this->getHelper()->addSellerInformation($information, $address->getQuote()->getStore());
        $customerClassName = $this->getHelper()->taxClassNameByClassId(
            $address->getQuote()
                ->getCustomerTaxClassId()
        );

        $customerCode = $this->getHelper()->getCustomerCodeById(
            $address->getQuote()->getCustomer()->getId()
        );

        $information['customer_code'] = $customerCode;
        $information['customer_class'] = $customerClassName;
        $information = $this->getHelper()->addAddressInformation($information, $address);
        $information['store_id'] = $address->getQuote()->getStore()->getId();
        $date = Mage::getSingleton('core/date')->date();
        $information['posting_date'] = $date;
        $information['document_date'] = $date;
        $information['order_items'] = array();
        $items = Mage::helper('vertextax')->getAllNonNominalItems($address);

        foreach ($items as $item) {
            if ($this->checkConfig('Enterprise_CatalogPermissions')) {
                if ($item->getDisableAddToCart() && !$item->isDeleted()) {
                    continue;
                }
            }

            if (!$item->getParentItem() && $item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $information['order_items'][$child->getId()] = $this->getHelper()->prepareItem($child, 'quote');

                    if ($this->checkConfig('Enterprise_GiftWrapping')) {
                        $information['order_items']['gift_wrap_' . $child->getId()] = $this->getHelper()
                            ->prepareGiftWrapItem($child, 'quote');
                    }
                }
            } else {
                $information['order_items'][$item->getId()] = $this->getHelper()->prepareItem($item, 'quote');

                if ($this->checkConfig('Enterprise_GiftWrapping')) {
                    $information['order_items']['gift_wrap_' . $item->getId()] = $this->getHelper()
                        ->prepareGiftWrapItem($item, 'quote');
                }
            }
        }

        if (!empty($this->getHelper()->addShippingInfo($address))) {
            $information['order_items']['shipping'] = $this->getHelper()->addShippingInfo($address);
        }

        $quoteData = $this->checkGiftWrapping($address, $information);

        return $quoteData;
    }

    /**
     * Check Gift Wrapping
     *
     * @param $address
     * @param $information
     *
     * @return array
     */
    protected function checkGiftWrapping($address, $information)
    {
        if ($this->checkConfig('Enterprise_GiftWrapping')) {
            if ($address->getGwPrice()) {
                $information['order_items']['gift_wrapping'] = $this->getHelper()->addOrderGiftWrap($address);
            }

            if ($address->getGwCardPrice()) {
                $information['order_items']['gift_print_card'] = $this->getHelper()->addOrderPrintCard($address);
            }
        }

        return $information;
    }

    /**
     * Check config status for given string
     *
     * @param string $config
     *
     * @return bool
     */
    protected function checkConfig($config)
    {
        return Mage::getConfig()->getModuleConfig($config) &&
            Mage::getConfig()->getModuleConfig($config)->is('active', 'true');
    }
}
