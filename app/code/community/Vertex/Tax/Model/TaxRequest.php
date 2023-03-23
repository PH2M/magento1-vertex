<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_TaxRequest extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('vertextax/taxRequest');
    }

    /**
     * @param int $orderId
     *
     * @return number
     */
    public function getTotalInvoicedTax($orderId)
    {
        $totalTax = 0;
        $invoices = $this->getCollection()
            ->addFieldToSelect('total_tax')
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('request_type', 'invoice');
        foreach ($invoices as $invoice) {
            $totalTax += $invoice->getTotalTax();
        }

        return $totalTax;
    }

    /**
     * @return Vertex_Tax_Model_TaxRequest
     */
    public function removeQuotesLookupRequests()
    {
        $this->getCollection()
            ->addFieldToSelect('request_id')
            ->addFieldToFilter(
                'request_type',
                array(
                    'in' => array(
                        'quote',
                        'tax_area_lookup'
                    )
                )
            )->walk('delete');

        return $this;
    }

    /**
     * @return Vertex_Tax_Model_TaxRequest
     */
    public function removeInvoicesforCompletedOrders()
    {
        $invoices = $this->getCollection()
            ->addFieldToSelect('order_id')
            ->addFieldToFilter('request_type', 'invoice');

        $invoices->getSelect()->join(
            array(
                'order' => 'sales_flat_order'
            ),
            'order.entity_id = main_table.order_id',
            array(
                'order.state'
            )
        );

        $invoices->addFieldToFilter(
            'order.state',
            array(
                'in' => array(
                    'complete',
                    'canceled',
                    'closed'
                )
            )
        );

        $completedOrderIds = array();
        foreach ($invoices as $invoice) {
            $completedOrderIds[] = $invoice->getOrderId();
        }

        $this->getCollection()
            ->addFieldToSelect('request_id')
            ->addFieldToFilter(
                'order_id',
                array(
                    'in' => $completedOrderIds
                )
            )->walk('delete');

        return $this;
    }
}
