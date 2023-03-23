<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function invoiceCreated(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if (!$this->_getHelper()->isVertexActive($invoice->getStore()) ||
            !$this->_getHelper()->requestByInvoiceCreation($invoice->getStore())
        ) {
            return $this;
        }

        /** @var $order Mage_Sales_Model_Order_Invoice **/
        $invoiceRequestData = Mage::getModel('vertextax/taxInvoice')->prepareInvoiceData($invoice, 'invoice');

        if ($invoiceRequestData &&
            Mage::getModel('vertextax/taxInvoice')->sendInvoiceRequest($invoiceRequestData, $invoice->getOrder())
        ) {
            $this->_getSession()->addSuccess(
                $this->_getHelper()
                    ->__('The Vertex invoice has been sent.')
            );
        }

        return $this;
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vertex_Tax_Model_Observer
     */
    public function orderSaved(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$this->_getHelper()->isVertexActive($order->getStore()) ||
            !$this->_getHelper()->requestByOrderStatus($order->getStatus())
        ) {
            return $this;
        }

        $invoiceRequestData = Mage::getModel('vertextax/taxInvoice')->prepareInvoiceData($order);
        if ($invoiceRequestData &&
            Mage::getModel('vertextax/taxInvoice')->sendInvoiceRequest($invoiceRequestData, $order)
        ) {
            $this->_getSession()->addSuccess(
                $this->_getHelper()
                    ->__('The Vertex invoice has been sent.')
            );
        }

        return $this;
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vertex_Tax_Model_Observer
     */
    public function orderCreditmemoRefund(Varien_Event_Observer $observer)
    {
        $creditMemo = $observer->getCreditmemo();
        $order = $creditMemo->getOrder();
        $invoicedTax = Mage::getModel('vertextax/taxRequest')->getTotalInvoicedTax($order->getId());
        if (!$this->_getHelper()->isVertexActive($order->getStore()) || !$invoicedTax) {
            return $this;
        }

        $creditmemoRequestData = Mage::getModel('vertextax/taxInvoice')->prepareInvoiceData($creditMemo, 'refund');
        if ($creditmemoRequestData &&
            Mage::getModel('vertextax/taxInvoice')->sendRefundRequest($creditmemoRequestData, $order)
        ) {
            $this->_getSession()->addSuccess(
                $this->_getHelper()
                    ->__('The Vertex invoice has been refunded.')
            );
        }

        return $this;
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Vertex_Tax_Model_Observer
     */
    public function changeSystemConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getConfig();
        $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/algorithm')->show_in_store = 0;

        $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/based_on')->show_in_store = 0;

        $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/discount_tax')->show_in_store = 0;

        $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_website = 0;
        $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_default = 0;
        $config->getNode('sections/tax/groups/calculation/fields/apply_tax_on')->show_in_store = 0;

        $config->getNode('sections/tax/groups/weee')->show_in_website = 0;
        $config->getNode('sections/tax/groups/weee')->show_in_default = 0;
        $config->getNode('sections/tax/groups/weee')->show_in_store = 0;

        $config->getNode('sections/tax/groups/defaults')->show_in_website = 0;
        $config->getNode('sections/tax/groups/defaults')->show_in_default = 0;
        $config->getNode('sections/tax/groups/defaults')->show_in_store = 0;

        if (!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') ||
            !Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')->is('active', 'true')
        ) {
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_class')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_order_code')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_class')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/giftwrap_item_code')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_class')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/printed_giftcard_code')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/reward_points_class')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/reward_points_class')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/reward_points_class')->show_in_store = 0;

            $config->getNode('sections/tax/groups/classes/fields/reward_points_code')->show_in_website = 0;
            $config->getNode('sections/tax/groups/classes/fields/reward_points_code')->show_in_default = 0;
            $config->getNode('sections/tax/groups/classes/fields/reward_points_code')->show_in_store = 0;
        }

        return $this;
    }

    /**
     * @return Vertex_Tax_Model_Observer
     */
    public function cleanLogs()
    {
        $requestModel = Mage::getModel('vertextax/taxRequest');
        $requestModel->removeQuotesLookupRequests();
        $requestModel->removeInvoicesforCompletedOrders();
        return $this;
    }

    /**
     *
     * @return Vertex_Tax_Helper_Data
     */
    public function _getHelper()
    {
        return Mage::helper('vertextax');
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
