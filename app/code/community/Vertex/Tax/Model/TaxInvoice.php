<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_TaxInvoice extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('vertextax/taxinvoice');
    }

    /**
     * @return Vertex_Tax_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('vertextax');
    }

    /**
     * @param $entityItem
     * @param null $event
     *
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    public function prepareInvoiceData($entityItem, $event = null)
    {
        $info = array();
        $info = $this->getHelper()->addSellerInformation($info, $entityItem->getStore());

        $order = $this->checkEntityItem($entityItem);

        $info['order_id'] = $order->getIncrementId();
        $info['document_number'] = $order->getIncrementId();
        $info['document_date'] = date("Y-m-d", strtotime($order->getCreatedAt()));
        $info['posting_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

        $customerClass = $this->getHelper()->taxClassNameByCustomerGroupId($order->getCustomerGroupId());
        $customerCode = $this->getHelper()->getCustomerCodeById($order->getCustomerId());

        $info['customer_class'] = $customerClass;
        $info['customer_code'] = $customerCode;

        $address = $this->checkIsVirtual($order);

        $info = $this->getHelper()->addAddressInformation($info, $address);

        $orderItems = array();
        $orderedItems = $entityItem->getAllItems();

        foreach ($orderedItems as $item) {
            $originalItem = $item;
            if ($entityItem instanceof Mage_Sales_Model_Order_Invoice) {
                $item = $item->getOrderItem();
            } elseif ($entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
                $item = $item->getOrderItem();
            }

            if ($item->getParentItem()) {
                continue;
            }

            if (!$item->getProduct()) {
                $item->setProduct(Mage::getModel("catalog/product")->load($item->getProductId()));
            }

            if ($item->getHasChildren() &&
                $item->getProduct()->getPriceType() !== null &&
                (int)$item->getProduct()->getPriceType() === Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD
            ) {
                foreach ($item->getChildrenItems() as $child) {
                    if ($entityItem instanceof Mage_Sales_Model_Order_Invoice ||
                        $entityItem instanceof Mage_Sales_Model_Order_Creditmemo
                    ) {
                        $orderItems[] = $this->getHelper()->prepareItem($child, 'invoiced', $originalItem, $event);
                        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') &&
                            Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') &&
                            $child->getGwId()
                        ) {
                            $orderItems[] = $this->getHelper()->prepareGiftWrapItem(
                                $child,
                                'invoiced',
                                $originalItem,
                                $event
                            );
                        }
                    } elseif ($entityItem instanceof Mage_Sales_Model_Order) {
                        $orderItems[] = $this->getHelper()->prepareItem($child, 'ordered', $originalItem, $event);
                        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') &&
                            Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') &&
                            $child->getGwId()
                        ) {
                            $orderItems[] = $this->getHelper()->prepareGiftWrapItem(
                                $child,
                                'ordered',
                                $originalItem,
                                $event
                            );
                        }
                    }
                }
            } else {
                if ($entityItem instanceof Mage_Sales_Model_Order_Invoice ||
                    $entityItem instanceof Mage_Sales_Model_Order_Creditmemo
                ) {
                    $orderItems[] = $this->getHelper()->prepareItem($item, 'invoiced', $originalItem, $event);
                    if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') &&
                        Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') &&
                        $item->getGwId()
                    ) {
                        $orderItems[] = $this->getHelper()->prepareGiftWrapItem(
                            $item,
                            'invoiced',
                            $originalItem,
                            $event
                        );
                    }
                } elseif ($entityItem instanceof Mage_Sales_Model_Order) {
                    $orderItems[] = $this->getHelper()->prepareItem($item, 'ordered', $originalItem, $event);
                    if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') &&
                        Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true') &&
                        $item->getGwId()
                    ) {
                        $orderItems[] = $this->getHelper()->prepareGiftWrapItem(
                            $item,
                            'ordered',
                            $originalItem,
                            $event
                        );
                    }
                }
            }
        }

        if (!$order->getIsVirtual() &&
            !empty($this->getHelper()->addShippingInfo($order, $entityItem, $event))
        ) {
            $orderItems[] = $this->getHelper()->addShippingInfo($order, $entityItem, $event);
        }

        if ($entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
            $orderItems = $this->getHelper()->addRefundAdjustments($orderItems, $entityItem);
        }

        $orderItemsArray = $this->checkGiftWrapping($entityItem, $event, $order, $orderItems);

        $info['request_type'] = 'InvoiceRequest';
        $info['order_items'] = $orderItemsArray;
        $request = Mage::getModel('vertextax/requestItem')->setData($info)->exportAsArray();

        return $request;
    }

    /**
     * Check if order is virtual
     *
     * @param $order
     *
     * @return mixed
     */
    protected function checkIsVirtual($order)
    {
        if ($order->getIsVirtual()) {
            return $address = $order->getBillingAddress();
        } else {
            return $address = $order->getShippingAddress();
        }
    }

    /**
     * Check entity item
     *
     * @param $entityItem
     *
     * @return Mage_Sales_Model_Order|null
     */
    protected function checkEntityItem($entityItem)
    {
        if ($entityItem instanceof Mage_Sales_Model_Order) {
            return $order = $entityItem;
        } elseif ($entityItem instanceof Mage_Sales_Model_Order_Invoice) {
            return $order = $entityItem->getOrder();
        } elseif ($entityItem instanceof Mage_Sales_Model_Order_Creditmemo) {
            return $order = $entityItem->getOrder();
        }

        return null;
    }

    /**
     * @param $data
     * @param string  $order
     *
     * @return boolean
     */
    public function sendInvoiceRequest($data, $order = null)
    {
        if ($order == null) {
            $order = Mage::registry('current_order');
        }

        $requestResult = Mage::getModel('vertextax/vertex')->sendApiRequest($data, 'invoice', $order);
        if ($requestResult instanceof Exception) {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Invoice Request Error: " . $requestResult->getMessage(), null, 'vertex.log', true);
            }
            Mage::getSingleton('adminhtml/session')->addError($requestResult->getMessage());
            return false;
        }

        $order->addStatusHistoryComment(
            'Vertex Invoice sent successfully. Amount: $' . $requestResult->InvoiceResponse->TotalTax->_,
            false
        )->save();
        return true;
    }

    /**
     * @param $data
     * @param string  $order
     *
     * @return boolean
     */
    public function sendRefundRequest($data, $order = null)
    {
        if ($order == null) {
            $order = Mage::registry('current_order');
        }

        $requestResult = Mage::getModel('vertextax/vertex')->sendApiRequest($data, 'invoice_refund', $order);
        if ($requestResult instanceof Exception) {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Refund Request Error: " . $requestResult->getMessage(), null, 'vertex.log', true);
            }
            Mage::getSingleton('adminhtml/session')->addError($requestResult->getMessage());
            return false;
        }

        $order->addStatusHistoryComment(
            'Vertex Invoice refunded successfully. Amount: $' . $requestResult->InvoiceResponse->TotalTax->_,
            false
        )->save();
        return true;
    }

    /**
     * @param $entityItem
     * @param $event
     * @param $order
     * @param $orderItems
     *
     * @return array
     */
    protected function checkGiftWrapping($entityItem, $event, $order, $orderItems)
    {
        if (Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping') &&
            Mage::getConfig()->getModuleConfig('Enterprise_GiftWrapping')->is('active', 'true')
        ) {
            if (!empty($this->getHelper()->addOrderGiftWrap($order, $entityItem, $event))) {
                $orderItems[] = $this->getHelper()->addOrderGiftWrap($order, $entityItem, $event);
            }

            if (!empty($this->getHelper()->addOrderPrintCard($order, $entityItem, $event))) {
                $orderItems[] = $this->getHelper()->addOrderPrintCard($order, $entityItem, $event);
            }
        }

        return $orderItems;
    }
}
