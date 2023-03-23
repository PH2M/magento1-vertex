<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_VertexController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('Vertex_Tax');
    }

    /**
     * Check is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/view');
    }

    /**
     * Init order
     *
     * @return bool|Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);

        return $order;
    }

    /**
     * Invoice tax action
     *
     * @throws Mage_Core_Exception
     */
    public function invoiceTaxAction()
    {
        if ($order = $this->_initOrder()) {
            $invoiceRequestData = Mage::getModel('vertextax/taxInvoice')->PrepareInvoiceData($order);
            if ($invoiceRequestData &&
                Mage::getModel('vertextax/taxInvoice')->SendInvoiceRequest($invoiceRequestData)
            ) {
                $this->_getSession()->addSuccess($this->__('The Vertex SMB invoice has been sent.'));
            }
        }

        $this->_redirect(
            '*/sales_order/view',
            array(
                'order_id' => $order->getId()
            )
        );
    }

    /**
     * Tax area action
     *
     * @return boolean
     */
    public function taxAreaAction()
    {
        $orderCreateModel = Mage::getSingleton('adminhtml/sales_order_create');
        $addressChanged = false;

        if ($orderCreateModel->getQuote()->isVirtual() || $orderCreateModel->getQuote()
                ->getShippingAddress()
                ->getSameAsBilling()
        ) {
            $address = $orderCreateModel->getQuote()->getBillingAddress();
        } else {
            $address = $orderCreateModel->getQuote()->getShippingAddress();
        }

        if (!$address->getStreet1() || !$address->getCity() || !$address->getRegion() || !$address->getPostcode()) {
            $result['message'] = 'address_not_complete';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);

            return false;
        }

        if ($address->getCountryId() !== 'US') {
            $result['message'] = 'not_usa_address';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);

            return false;
        }

        $taxAreaModel = Mage::getModel('vertextax/TaxAreaRequest');
        $requestResult = $taxAreaModel->prepareRequest($address)->taxAreaLookup();
        if ($requestResult instanceof Exception) {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Admin Tax Area Lookup Error: " . $requestResult->getMessage(), null, 'vertex.log', true);
            }
            $result['message'] = $requestResult->getMessage();
            $result['error'] = 1;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);

            return false;
        }

        $taxAreaResposeModel = $taxAreaModel->getResponse();

        if ($taxAreaResposeModel->getResultsCount() > 1 && Mage::helper('vertextax')->showPopup()) {
            $taxAreaInfoCollection = $taxAreaResposeModel->getTaxAreaLocationsCollection();

            $block = Mage::app()->getLayout()
                ->createBlock('page/html')
                ->setTemplate('vertex/popup-content.phtml')
                ->setData('is_multiple', 1)
                ->setData('items_collection', $taxAreaInfoCollection)
                ->toHtml();
            $result['message'] = "show_popup";
            $result['html'] = $block;
        } else {
            $firstTaxArea = $taxAreaResposeModel->getFirstTaxAreaInfo();
            $result['message'] = 'tax_area_id_found';
            if (strtolower($address->getCity()) != strtolower($firstTaxArea->getTaxAreaCity())) {
                $addressChanged = true;
                $blockAddressUpdate = Mage::app()->getLayout()
                    ->createBlock('page/html')
                    ->setData('is_multiple', 0)
                    ->setFirstItem($firstTaxArea)
                    ->setTemplate('vertex/popup-content.phtml')
                    ->toHtml();
            }

            $address->setCity($firstTaxArea->getTaxAreaCity());
            $address->setTaxAreaId($firstTaxArea->getTaxAreaId())
                ->save();
            $orderCreateModel->saveQuote();
        }

        if ($addressChanged && !$address->getQuote()->isVirtual()) {
            $result['message'] = "show_popup";
            $result['html'] = $blockAddressUpdate;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        $this->setFlag('', self::FLAG_NO_DISPATCH, true);

        return true;
    }

    /**
     * Save tax area action
     */
    public function saveTaxAreaAction()
    {
        $orderCreateModel = Mage::getSingleton('adminhtml/sales_order_create');

        if ($orderCreateModel->getQuote()->isVirtual() || $orderCreateModel->getQuote()
                ->getShippingAddress()
                ->getSameAsBilling()
        ) {
            $address = $orderCreateModel->getQuote()->getBillingAddress();
        } else {
            $address = $orderCreateModel->getQuote()->getShippingAddress();
        }

        $taxAreaId = $this->getRequest()->getParam('tax_area_id');
        $city = $this->getRequest()->getPost('new_city', 0);

        $address->setTaxAreaId($taxAreaId);
        $oldCity = $address->getCity();
        if (strtolower($oldCity) != strtolower($city)) {
            $address->setCity($city);
        }

        $orderCreateModel->saveQuote();
        $result['message'] = 'ok';

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        $this->setFlag('', self::FLAG_NO_DISPATCH, true);
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
