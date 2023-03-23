<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_RequestItem extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('vertextax/requestItem');
    }

    /**
     * @return Vertex_Tax_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('vertextax');
    }

    /**
     * @return array
     */
    public function exportAsArray()
    {
        $request = array(
            'Login' => array(
                'TrustedId' => $this->getTrustedId()
            ),
            $this->getRequestType() => array(
                'documentDate' => $this->getDocumentDate(),
                'postingDate' => $this->getPostingDate(),
                'transactionType' => $this->getTransactionType(),
                'documentNumber' => $this->getDocumentNumber(),
                'LineItem' => array()
            )
        );

        if ($this->getDocumentNumber()) {
            $request[$this->getRequestType()]['documentNumber'] = $this->getDocumentNumber();
        }

        $orderItems = $this->getOrderItems();
        $request[$this->getRequestType()]['LineItem'] = $this->addItems($orderItems);

        return $request;
    }

    /**
     * @param $items
     *
     * @return array
     */
    public function addItems($items)
    {
        $queryItems = array();
        $i = 1;

        /** lineItemNumber */
        foreach ($items as $key => $item) {
            /** $key - quote_item_id */
            $tmpItem = array(
                'lineItemNumber' => $i,
                'lineItemId' => $key,
                'locationCode' => $this->getLocationCode(),
                'Seller' => array(
                    'Company' => $this->getCompanyId(),
                    'PhysicalOrigin' => array(
                        'StreetAddress1' => $this->getData('company_street_1'),
                        'StreetAddress2' => $this->getData('company_street_2'),
                        'City' => $this->getCompanyCity(),
                        'Country' => $this->getCompanyCountry(),
                        'MainDivision' => $this->getCompanyState(),
                        'PostalCode' => $this->getCompanyPostcode()
                    )
                ),
                'Customer' => array(
                    'CustomerCode' => array(
                        'classCode' => $this->getCustomerClass(),
                        '_' => $this->getCustomerCode()
                    ),
                    'Destination' => array(
                        'StreetAddress1' => $this->getCustomerStreet1(),
                        'StreetAddress2' => $this->getCustomerStreet2(),
                        'City' => $this->getCustomerCity(),
                        'MainDivision' => $this->getCustomerRegion(),
                        'PostalCode' => $this->getCustomerPostcode(),
                        'Country' => $this->getCustomerCountry()
                    )
                ),
                'Product' => array(
                    'productClass' => $item['product_class'],
                    '_' => $item['product_code']
                ),
                'UnitPrice' => array(
                    '_' => $item['price']
                ),
                'Quantity' => array(
                    '_' => $item['qty']
                ),
                'ExtendedPrice' => array(
                    '_' => $item['extended_price']
                )
            );

            if ($this->getCustomerCountry() == 'CAN') {
                $tmpItem['deliveryTerm'] = 'SUP';
            }

            $queryItems[] = $tmpItem;
            $i++;
        }

        return $queryItems;
    }
}
