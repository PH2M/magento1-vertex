<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

require Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';

class Vertex_Tax_OnepageController extends Mage_Checkout_OnepageController
{

    /**
     * Save shipping action
     *
     * @return Vertex_Tax_OnepageController
     */
    public function saveShippingAction()
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::saveShippingAction();
            return $this;
        }

        if (!$this->_expireAjax()) {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost('shipping', array());
                $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
                $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
                $address = $this->getOnepage()->getQuote()->getShippingAddress();

                /**
                 * Save Tax Area & Correct City | Show popup window
                 */
                if (!$this->saveTaxAreaId($address)) {
                    return $this;
                }

                /**
                 * Save Tax Area & Correct City | Show popup window
                 */
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }

        return $this;
    }

    /**
     * Save billing action
     *
     * @return Vertex_Tax_OnepageController
     */
    public function saveBillingAction()
    {
        if (!Mage::helper('vertextax')->isVertexActive()) {
            parent::saveBillingAction();
            return $this;
        }

        if (!$this->_expireAjax()) {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost('billing', array());
                $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

                if (isset($data['email'])) {
                    $data['email'] = trim($data['email']);
                }

                $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

                if (!isset($result['error'])) {
                    if ($this->getOnepage()->getQuote()->isVirtual()
                    ) {
                        $result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml()
                        );
                        $address = $this->getOnepage()->getQuote()->getBillingAddress();

                        /**
                         * Save Tax Area & Correct City | Show popup window
                         */
                        if (!$this->saveTaxAreaId($address)) {
                            return $this;
                        }

                        /**
                         * Save Tax Area & Correct City | Show popup window
                         */
                    } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                        $result['goto_section'] = 'shipping_method';
                        $result['update_section'] = array(
                            'name' => 'shipping-method',
                            'html' => $this->_getShippingMethodsHtml()
                        );

                        $result['allow_sections'] = array(
                            'shipping'
                        );
                        $result['duplicateBillingInfo'] = 'true';
                        $address = $this->getOnepage()->getQuote()->getShippingAddress();

                        /**
                         * Save Tax Area & Correct City | Show popup window
                         */
                        if (!$this->saveTaxAreaId($address)) {
                            return $this;
                        }

                    /**
                     * Save Tax Area & Correct City | Show popup window
                     */
                    } else {
                        $result['goto_section'] = 'shipping';
                    }
                }

                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }

        return $this;
    }

    /**
     * Save tax area action
     *
     * @return void
     */
    public function saveTaxAreaAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $taxAreaId = $this->getRequest()->getPost('tax_area_id', 0);
            $newCity = $this->getRequest()->getPost('tax_new_city', 0);

            if ($this->getOnepage()
                ->getQuote()
                ->isVirtual()
            ) {
                $address = $this->getOnepage()
                    ->getQuote()
                    ->getBillingAddress();
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
                $shippingAddress = $this->getOnepage()
                    ->getQuote()
                    ->getShippingAddress();
                $oldCity = $shippingAddress->getCity();
                if (strtolower($oldCity) != strtolower($newCity)) {
                    $shippingAddress->setCity($newCity);
                }

                $shippingAddress->setTaxAreaId($taxAreaId)->save();
            } else {
                $address = $this->getOnepage()
                    ->getQuote()
                    ->getShippingAddress();
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );

                $oldCity = $address->getCity();
                if (strtolower($oldCity) != strtolower($newCity)) {
                    $address->setCity($newCity);
                }
            }

            $address->setTaxAreaId($taxAreaId)->save();
            $this->getOnepage()
                ->getQuote()
                ->collectTotals()
                ->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Refreshes the previous step
     * Loads the block corresponding to the current step and sets it
     * in to the response body
     *
     * This function is called from the reloadProgessBlock
     * function from the javascript
     *
     * @return null|string|void
     * @throws Mage_Core_Exception
     */
    public function progressAction()
    {
        $prevStep = $this->getRequest()->getParam('prevStep', false);

        if ($this->_expireAjax() || !$prevStep) {
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }

        if ($prevStep == 'selectaddress') {
            $prevStep = 'shipping';
        }

        if ($prevStep == 'selectaddress' && $this->getOnepage()->getQuote()->isVirtual()
        ) {
            $prevStep = 'billing';
        }

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_progress_' . $prevStep);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        $this->getResponse()->setBody($output);

        return $output;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return bool
     */
    public function saveTaxAreaId($address)
    {
        if ($address->getCountryId() !== 'US') {
            return true;
        }

        $taxAreaModel = Mage::getModel('vertextax/TaxAreaRequest');
        $requestResult = $taxAreaModel->prepareRequest($address)->taxAreaLookup();
        $addressChanged = false;

        if ($requestResult instanceof Exception) {
            if (Mage::app()->getRequest()->getControllerName() == 'onepage') {
                Mage::log(
                    "Quote Request Error: " . $requestResult->getMessage() .
                    "Controller:  " . Mage::helper('tax')->getSourcePath(),
                    null,
                    'vertex.log'
                );
                $result = array(
                    'error'   => 1,
                    'message' => "Tax Calculation Request Error. Please check your address"
                );
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            }

            return false;
        }

        $taxAreaResposeModel = $taxAreaModel->getResponse();

        if ($taxAreaResposeModel->getResultsCount() > 1 && Mage::helper('vertextax')->showPopup()) {
            $taxAreaInfoCollection = $taxAreaResposeModel->getTaxAreaLocationsCollection();

            $block = Mage::app()->getLayout()
                ->createBlock('core/template')
                ->setTemplate('vertex/popup-content.phtml')
                ->setData('items_collection', $taxAreaInfoCollection)
                ->setData('is_multiple', 1)
                ->toHtml();
            $result['goto_section'] = 'selectaddress';
            $result['update_section'] = array(
                'name' => 'selectaddress',
                'html' => $block
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return false;
        } else {
            $firstTaxArea = $taxAreaResposeModel->getFirstTaxAreaInfo();

            if (strcmp(strtolower($address->getCity()), strtolower($firstTaxArea->getTaxAreaCity())) !== 0) {
                Mage::log(
                    "Original City: " . $address->getCity() .
                    " - New City: " . $firstTaxArea->getTaxAreaCity(),
                    null,
                    'vertex.log'
                );
                $addressChanged = true;
                $blockAddressUpdate = Mage::app()->getLayout()
                    ->createBlock('core/template')
                    ->setTemplate('vertex/popup-content.phtml')
                    ->setData('is_multiple', 0)
                    ->setFirstItem($firstTaxArea)
                    ->toHtml();
            }

            $address->setCity($firstTaxArea->getTaxAreaCity());

            $address->setTaxAreaId($firstTaxArea->getTaxAreaId())
                ->save();
            $this->getOnepage()
                ->getQuote()
                ->collectTotals()
                ->save();
        }

        if ($addressChanged) {
            $result['goto_section'] = 'selectaddress';
            $result['update_section'] = array(
                'name' => 'selectaddress',
                'html' => $blockAddressUpdate
            );

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return false;
        }

        return true;
    }
}
