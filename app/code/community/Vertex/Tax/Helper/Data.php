<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if Vertex extension is active
     *
     * @param null $store
     * @return bool
     */
    public function isVertexActive($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_ENABLE_VERTEX, $store);
    }

    /**
     * @return int
     */
    public function maxAllowedShippingCode()
    {
        return Vertex_Tax_Helper_Config::MAX_CHAR_SHIPPING_CODE_ALLOWED;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getLocationCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_LOCATION_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_COMPANY_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyStreet1($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_STREET1, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyStreet2($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_STREET2, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyCity($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_CITY, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyCountry($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_COUNTRY, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyRegionId($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_REGION, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCompanyPostalCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_POSTAL_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getShippingTaxClassId($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getTrustedId($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID, $store);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_TRANSACTION_TYPE;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getVertexHost($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_API_HOST, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getVertexAddressHost($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_ADDRESS_API_HOST, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getDefaultCustomerCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCreditmemoAdjustmentFeeCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCreditmemoAdjustmentFeeClass($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCreditmemoAdjustmentPositiveCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCreditmemoAdjustmentPositiveClass($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function allowCartQuote($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getGiftWrappingOrderClass($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_GIFTWRAP_ORDER_CLASS, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getGiftWrappingOrderCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_GIFTWRAP_ORDER_CODE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getGiftWrappingItemClass($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_GIFTWRAP_ITEM_CLASS, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getGiftWrappingItemCodePrefix($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_GIFTWRAP_ITEM_CODE_PREFIX, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getPrintedGiftcardClass($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_PRINTED_GIFTCARD_CLASS, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getPrintedGiftcardCode($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::VERTEX_PRINTED_GIFTCARD_CODE, $store);
    }

    /**
     * @return boolean
     */
    public function isAllowedQuote()
    {
        $quoteAllowedControllers = Mage::helper('vertextax/config')->getQuoteAllowedControllers();
        if ($this->allowCartQuote()) {
            $quoteAllowedControllers[] = 'cart';
        }

        if (in_array(Mage::app()->getRequest()->getControllerName(), $quoteAllowedControllers)) {
            return true;
        }

        return false;
    }

    /**
     * @param null $store
     * @return string
     */
    public function showManualInvoiceButton($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON, $store);
    }

    /**
     * Is Popup Window Allowed
     *
     * @param null $store
     * @return string
     */
    public function showPopup($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_SHOW_POPUP, $store);
    }

    /**
     * @param null $store
     * @return boolean
     */
    public function requestByInvoiceCreation($store = null)
    {
        $vertexInvoiceEvent = Mage::getStoreConfig(
            Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER,
            $store
        );
        if ($vertexInvoiceEvent == 'invoice_created') {
            return true;
        }

        return false;
    }

    /**
     * @param $status
     * @return boolean
     */
    public function requestByOrderStatus($status)
    {
        $vertexInvoiceEvent = Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER);
        $vertexInvoiceOrderStatus = Mage::getStoreConfig(
            Vertex_Tax_Helper_Config::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS
        );

        if ($vertexInvoiceEvent == 'order_status' && $vertexInvoiceOrderStatus == $status) {
            return true;
        }

        return false;
    }

    /**
     * @param $classId
     * @return string
     */
    public function taxClassNameByClassId($classId)
    {
        if (!$classId) {
            $taxclassName = "None";
        } else {
            $taxclassName = Mage::getModel('tax/class')->load($classId)->getClassName();
        }

        return $taxclassName;
    }

    /**
     * @return string
     */
    public function getSourcePath()
    {
        $controller = Mage::app()->getRequest()->getControllerName();
        $module = Mage::app()->getRequest()->getModuleName();
        $action = Mage::app()->getRequest()->getActionName();
        $sourcePath = "";
        if ($controller) {
            $sourcePath .= $controller;
        }

        if ($module) {
            $sourcePath .= "_" . $module;
        }

        if ($action) {
            $sourcePath .= "_" . $action;
        }

        return $sourcePath;
    }

    /**
     * @param int $groupId
     * @return string
     */
    public function taxClassNameByCustomerGroupId($groupId)
    {
        $classId = Mage::getModel('customer/group')->getTaxClassId($groupId);
        return $this->taxClassNameByClassId($classId);
    }

    /**
     * @param int $customerId
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCustomerCodeById($customerId = 0)
    {
        $customerCode = $this->getDefaultCustomerCode(Mage::app()->getStore());
        if ($customerId) {
            $customerCode = Mage::getModel('customer/customer')->load($customerId)->getCustomerCode();
        }

        if (empty($customerCode)) {
            $customerCode = $this->getDefaultCustomerCode(Mage::app()->getStore());
        }

        return $customerCode;
    }

    /**
     * @param $store
     * @return string
     */
    public function checkCredentials($store = null)
    {
        $this->checkData($store);

        $regionId = $this->getCompanyRegionId($store);

        if (is_int($regionId)) {
            $regionModel = Mage::getModel('directory/region')->load($regionId);
            $companyState = $regionModel->getCode();
        } else {
            $companyState = $regionId;
        }

        $countryModel = Mage::getModel('directory/country')->load($this->getCompanyCountry($store));
        $countryName = $countryModel->getIso3Code();

        $address = new Varien_Object();
        $address->setStreet1($this->getCompanyStreet1($store));
        $address->setStreet2($this->getCompanyStreet2($store));
        $address->setCity($this->getCompanyCity($store));
        $address->setRegionCode($companyState);
        $address->setPostcode($this->getCompanyPostalCode($store));

        if ($countryName != 'USA') {
            return "Valid";
        }

        $requestResult = Mage::getModel('vertextax/TaxAreaRequest')->prepareRequest($address)->taxAreaLookup();

        if ($requestResult instanceof Exception) {
            return "Address Validation Error: Please check settings";
        }

        return "Valid";
    }

    /**
     * @param $store
     * @return string|null
     */
    protected function checkData($store)
    {
        if (!$this->getVertexHost($store)) {
            return "Not Valid: Missing Api Url";
        }

        if (!$this->getVertexAddressHost($store)) {
            return "Not Valid: Missing Address Validation Api Url";
        }

        if (!$this->getTrustedId($store)) {
            return "Not Valid: Missing Trusted Id";
        }

        if (!$this->getCompanyRegionId($store)) {
            return "Not Valid: Missing Company State";
        }

        if (!$this->getCompanyCountry($store)) {
            return "Not Valid: Missing Company Country";
        }

        if (!$this->getCompanyStreet1($store)) {
            return "Not Valid: Missing Company Street Address";
        }

        if (!$this->getCompanyCity($store)) {
            return "Not Valid: Missing Company City";
        }

        if (!$this->getCompanyPostalCode($store)) {
            return "Not Valid: Missing Company Postal Code";
        }

        return null;
    }

    /**
     * Company Information
     *
     * @param array $data
     * @param $store
     *
     * @return array
     */
    public function addSellerInformation($data, $store = null)
    {
        $regionId = $this->getCompanyRegionId($store);

        if (is_int($regionId)) {
            $regionModel = Mage::getModel('directory/region')->load($regionId);
            $companyState = $regionModel->getCode();
        } else {
            $companyState = $regionId;
        }

        $countryModel = Mage::getModel('directory/country')->load($this->getCompanyCountry($store));
        $countryName = $countryModel->getIso3Code();

        $data['location_code'] = $this->getLocationCode($store);
        $data['transaction_type'] = $this->getTransactionType($store);
        $data['company_id'] = $this->getCompanyCode($store);
        $data['company_street_1'] = $this->getCompanyStreet1($store);
        $data['company_street_2'] = $this->getCompanyStreet2($store);
        $data['company_city'] = $this->getCompanyCity($store);
        $data['company_state'] = $companyState;
        $data['company_postcode'] = $this->getCompanyPostalCode($store);
        $data['company_country'] = $countryName;
        $data['trusted_id'] = $this->getTrustedId($store);

        return $data;
    }

    /**
     *
     * @param array $data
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return array
     */
    public function addAddressInformation($data, $address)
    {
        $data['customer_street1'] = $address->getStreet1();
        $data['customer_street2'] = $address->getStreet2();
        $data['customer_city'] = $address->getCity();
        $data['customer_region'] = $address->getRegionCode();
        $data['customer_postcode'] = $address->getPostcode();
        $countryModel = Mage::getModel('directory/country')->load($address->getCountryId());
        $countryName = $countryModel->getIso3Code();
        $data['customer_country'] = $countryName;
        $data['tax_area_id'] = $address->getTaxAreaId();

        return $data;
    }

    /**
     * @param $originalEntity
     *
     * @return boolean
     */
    public function isFirstOfPartial($originalEntity)
    {
        if ($originalEntity instanceof Mage_Sales_Model_Order_Invoice) {
            if (!$originalEntity->getShippingTaxAmount()) {
                return false;
            }
        }

        if ($this->requestByInvoiceCreation() &&
            $originalEntity instanceof Mage_Sales_Model_Order &&
            $originalEntity->getShippingInvoiced()
        ) {
            return false;
        }

        if ($originalEntity instanceof Mage_Sales_Model_Order_Creditmemo) {
            if (!$originalEntity->getShippingAMount()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $info
     * @param $creditmemoModel
     *
     * @return array
     */
    public function addRefundAdjustments($info, $creditmemoModel)
    {
        if ($creditmemoModel->getAdjustmentPositive()) {
            $itemData = array();
            $itemData['product_class'] = $this->taxClassNameByClassId(
                $this->getCreditmemoAdjustmentPositiveClass($creditmemoModel->getStoreId())
            );
            $itemData['product_code'] = $this->getCreditmemoAdjustmentPositiveCode($creditmemoModel->getStoreId());
            $itemData['qty'] = 1;
            $itemData['price'] = -1 * $creditmemoModel->getAdjustmentPositive();
            $itemData['extended_price'] = -1 * $creditmemoModel->getAdjustmentPositive();
            $info[] = $itemData;
        }

        if ($creditmemoModel->getAdjustmentNegative()) {
            $itemData = array();
            $itemData['product_class'] = $this->taxClassNameByClassId(
                $this->getCreditmemoAdjustmentFeeClass($creditmemoModel->getStoreId())
            );
            $itemData['product_code'] = $this->getCreditmemoAdjustmentFeeCode($creditmemoModel->getStoreId());
            $itemData['qty'] = 1;
            $itemData['price'] = $creditmemoModel->getAdjustmentNegative();
            $itemData['extended_price'] = $creditmemoModel->getAdjustmentNegative();
            $info[] = $itemData;
        }

        return $info;
    }

    /**
     * @param mixed   $orderAddress
     * @param string  $originalEntity
     * @param string  $event
     *
     * @return array
     */
    public function addOrderGiftWrap($orderAddress, $originalEntity = null, $event = null)
    {
        $itemData = array();
        if (!$this->isFirstOfPartial($originalEntity)) {
            return $itemData;
        }

        $itemData['product_class'] = $this->taxClassNameByClassId(
            $this->getGiftWrappingOrderClass($orderAddress->getStoreId())
        );
        $itemData['product_code'] = $this->getGiftWrappingOrderCode($orderAddress->getStoreId());
        $itemData['qty'] = 1;
        $itemData['price'] = $orderAddress->getGwPrice();
        $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];

        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $orderAddress
     * @param string  $originalEntity
     * @param string  $event
     *
     * @return array
     */
    public function addOrderPrintCard($orderAddress, $originalEntity = null, $event = null)
    {
        $itemData = array();
        if (!$this->isFirstOfPartial($originalEntity)) {
            return $itemData;
        }

        $itemData['product_class'] = $this->taxClassNameByClassId(
            $this->getPrintedGiftcardClass($orderAddress->getStoreId())
        );
        $itemData['product_code'] = $this->getPrintedGiftcardCode($orderAddress->getStoreId());
        $itemData['qty'] = 1;
        $itemData['price'] = $orderAddress->getGwCardPrice();
        $itemData['extended_price'] = $orderAddress->getGwCardPrice();

        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     * @param Mage_Sales_Model_Order_Address|Mage_Sales_Model_Order|Mage_Sales_Model_Quote_Address $orderAddress
     * @param string  $originalEntity
     * @param string  $event
     *
     * @return array
     */
    public function addShippingInfo($orderAddress, $originalEntity = null, $event = null)
    {
        $itemData = array();
        if ($orderAddress->getShippingMethod() && $this->isFirstOfPartial($originalEntity)) {
            $itemData['product_class'] = $this->taxClassNameByClassId(
                $this->getShippingTaxClassId($orderAddress->getStoreId())
            );
            $itemData['product_code'] = substr($orderAddress->getShippingMethod(), 0, $this->maxAllowedShippingCode());
            $itemData['price'] = $orderAddress->getShippingAmount() - $orderAddress->getShippingDiscountAmount();
            $itemData['qty'] = 1;
            $itemData['extended_price'] = $itemData['price'];

            if ($originalEntity instanceof Mage_Sales_Model_Order_Creditmemo) {
                $itemData['price'] = $originalEntity->getShippingAmount();
                $itemData['extended_price'] = $itemData['price'];
            }

            if ($event == 'cancel' || $event == 'refund') {
                $itemData['price'] = -1 * $itemData['price'];
                $itemData['extended_price'] = -1 * $itemData['extended_price'];
            }
        }

        return $itemData;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function taxQuoteItems($address)
    {
        $informationArray = Mage::getModel('vertextax/taxQuote')->collectQuotedata($address);
        $information = new Varien_Object($informationArray);
        $information->setTaxAreaId();
        $taxedItemsInfo = Mage::getModel('vertextax/taxQuote')->getTaxQuote($informationArray);
        return $taxedItemsInfo;
    }

    /**
     * @return boolean
     */
    public function canQuoteTax()
    {
        if (!$this->isAllowedQuote()) {
            return false;
        }

        if ($this->getSourcePath() == 'onepage_checkout_index') {
            return false;
        }

        return true;
    }

    /**
     * Common function for item preparation
     *
     * @uses Always send discounted. Discount on TotalRowAmount
     *
     * @param $item
     * @param string  $type
     * @param string  $originalEntityItem
     * @param string  $event
     *
     * @return array
     */
    public function prepareItem($item, $type = 'ordered', $originalEntityItem = null, $event = null)
    {
        $itemData = array();

        $itemData['product_class'] = $this->taxClassNameByClassId(
            $item->getProduct()
                ->getTaxClassId()
        );
        $itemData['product_code'] = $item->getSku();
        $itemData['item_id'] = $item->getId();

        if ($type == 'invoiced') {
            $price = $originalEntityItem->getPrice();
        } else {
            $price = $item->getPrice();
        }

        $itemData['price'] = $price;
        if ($type == 'ordered' && $this->requestByInvoiceCreation()) {
            $itemData['qty'] = $item->getQtyOrdered() - $item->getQtyInvoiced();
        } elseif ($type == 'ordered') {
            $itemData['qty'] = $item->getQtyOrdered();
        } elseif ($type == 'invoiced') {
            $itemData['qty'] = $originalEntityItem->getQty();
        } elseif ($type == 'quote') {
            $itemData['qty'] = $item->getQty();
        }

        if ($type == 'invoiced') {
            $itemData['extended_price'] = $originalEntityItem->getRowTotal() - $originalEntityItem->getDiscountAmount();
        } elseif ($type == 'ordered' && $this->requestByInvoiceCreation()) {
            $itemData['extended_price'] = $item->getRowTotal() -
                $item->getRowInvoiced() -
                $item->getDiscountAmount() +
                $item->getDiscountInvoiced();
        } else {
            $itemData['extended_price'] = $item->getRowTotal() - $item->getDiscountAmount();
        }

        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     *
     * @param $item
     * @param string  $type
     * @param string  $originalEntityItem
     * @param string  $event
     *
     * @return array
     */
    public function prepareGiftWrapItem($item, $type = 'ordered', $originalEntityItem = null, $event = null)
    {
        $itemData = array();

        $itemData['product_class'] = $this->taxClassNameByClassId($this->getGiftWrappingItemClass($item->getStoreId()));
        $itemData['product_code'] = $this->getGiftWrappingItemCodePrefix($item->getStoreId()) . '-' . $item->getSku();

        if ($type == 'invoiced') {
            $price = $item->getGwPriceInvoiced();
        } else {
            $price = $item->getGwPrice();
        }

        $itemData['price'] = $price;
        if ($type == 'ordered' && $this->requestByInvoiceCreation()) {
            $itemData['qty'] = $item->getQtyOrdered() - $item->getQtyInvoiced();
        } elseif ($type == 'ordered') {
            $itemData['qty'] = $item->getQtyOrdered();
        } elseif ($type == 'invoiced') {
            $itemData['qty'] = $originalEntityItem->getQty();
        } elseif ($type == 'quote') {
            $itemData['qty'] = $item->getQty();
        }

        if ($type == 'invoiced') {
            $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];
        } elseif ($type == 'ordered' && $this->requestByInvoiceCreation()) {
            $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];
        } else {
            $itemData['extended_price'] = $itemData['qty'] * ($item->getGwPrice());
        }

        if ($event == 'cancel' || $event == 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     * @return Mage_Checkout_Model_Session | Mage_Adminhtml_Model_Session_Quote
     */
    public function getSession()
    {
        if (Mage::app()->getRequest()->getControllerName() != 'sales_order_create') {
            return Mage::getSingleton('checkout/session');
        } else {
            return Mage::getSingleton('adminhtml/session_quote');
        }
    }

    // Mackenzie Fisher @ Mediotype
    // SOAP API request bugfix for java.lang.NumberFormatException: For input string: "Array"
    // Loop line items and swap out Array type values being rejected by SOAP API endpoint
    /**
     * @param array reference &$request
     */
    public function sanitizeLineItems(&$request)
    {
        $lineItems = &$request["QuotationRequest"]["LineItem"];
        $lineItemsCount = count($lineItems);
        $isValid = $lineItems && is_array($lineItems) && !empty($lineItems);
        $removeItems = array();

        if ($isValid) {
            for ($i = 0; $i < $lineItemsCount; $i++) {
                $unitPrice = $lineItems[$i]["UnitPrice"]["_"];
                $quantity = $lineItems[$i]["Quantity"]["_"];
                $extendedPrice = $lineItems[$i]["ExtendedPrice"]["_"];
                $containsZeros = $unitPrice == 0 || $quantity == 0 || $extendedPrice == 0;

                if ($unitPrice && $quantity && $extendedPrice) {
                    $lineItems[$i]["UnitPrice"] = $unitPrice;
                    $lineItems[$i]["Quantity"] = $quantity;
                    $lineItems[$i]["ExtendedPrice"] = $extendedPrice;
                }

                if ($containsZeros) {
                    array_push($removeItems, $i);
                }
            }
        } else {
            $e = "Unable to find valid line items, check Vertex API request data";
            Mage::log(__FILE__ . ":" . __LINE__ . ": " . $e, 3);
        }

        $removeItemsCount = count($removeItems);
        for ($i = 0; $i < $removeItemsCount; $i++) {
            $item = $removeItems[$i];
            unset($lineItems[$item]);
        }

        $lineItems = array_values($lineItems);
    }

    public function getAllNonNominalItems($address)
    {
        if (method_exists($address, "getAllNonNominalItems")) {
            return $address->getAllNonNominalItems();
        }

        return $address->getAllVisibleItems();
    }

    /**
     * @param $store
     *
     * @return string
     */
    public function isLoggingEnabled($store = null)
    {
        return Mage::getStoreConfig(Vertex_Tax_Helper_Config::CONFIG_XML_PATH_ENABLE_LOGGING, $store);
    }
}
