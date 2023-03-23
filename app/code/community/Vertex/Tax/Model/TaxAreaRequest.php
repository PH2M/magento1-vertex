<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_TaxAreaRequest extends Mage_Core_Model_Abstract
{

    /**
     * Prepare request
     *
     * @param $address
     * @return $this
     */
    public function prepareRequest($address)
    {
        $request = array(
            'Login'          => array(
                'TrustedId' => $this->getHelper()->getTrustedId($address->getStoreId())
            ),
            'TaxAreaRequest' => array(
                'TaxAreaLookup' => array(
                    'PostalAddress' => array(
                        'StreetAddress1' => $address->getStreet1(),
                        'StreetAddress2' => $address->getStreet2(),
                        'City'           => $address->getCity(),
                        'MainDivision'   => $address->getRegionCode(),
                        'PostalCode'     => $address->getPostcode()
                    )
                )
            )
        );

        $this->setRequest($request);
        return $this;
    }

    /**
     * Tax area lookup
     *
     * @return bool
     */
    public function taxAreaLookup()
    {
        if (!$this->getRequest()) {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Tax area lookup error: request information not exist", null, 'vertex.log', true);
            }
            return false;
        }

        $requestData = $this->getRequest();

        $requestResult = Mage::getModel('vertextax/vertex')->sendApiRequest($requestData, 'tax_area_lookup');
        if ($requestResult instanceof Exception) {
            if (Mage::helper('vertextax')->isLoggingEnabled()) {
                Mage::log("Tax Area Lookup Error: " . $requestResult->getMessage(), null, 'vertex.log', true);
            }
            return $requestResult;
        }

        $response = Mage::getModel('vertextax/TaxAreaResponse')->parseResponse($requestResult);
        $response->setRequestCity($requestData['TaxAreaRequest']['TaxAreaLookup']['PostalAddress']['City']);
        $this->setResponse($response);

        return $requestResult;
    }

    /**
     * Get helper
     *
     * @return Vertex_Tax_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('vertextax');
    }
}
