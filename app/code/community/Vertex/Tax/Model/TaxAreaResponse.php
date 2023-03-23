<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_TaxAreaResponse extends Mage_Core_Model_Abstract
{
    /**
     * @param $responseObject
     *
     * @return Vertex_Tax_Model_TaxAreaResponse
     */
    public function parseResponse(stdClass $responseObject)
    {
        if (is_array($responseObject->TaxAreaResponse->TaxAreaResult)) {
            $taxAreaResults = $responseObject->TaxAreaResponse->TaxAreaResult;
        } else {
            $taxAreaResults[] = $responseObject->TaxAreaResponse->TaxAreaResult;
        }

        $this->setTaxAreaResults($taxAreaResults);
        $this->setResultsCount(count($taxAreaResults));
        return $this;
    }

    public function getFirstTaxAreaInfo()
    {
        $collection = $this->getTaxAreaLocationsCollection();

        return $collection->setPageSize(1)->getFirstItem();
    }

    /**
     * Used for popup window frontend/adminhtml
     *
     * @return Varien_Data_Collection
     */
    public function getTaxAreaLocationsCollection()
    {
        $taxAreaInfoCollection = new Varien_Data_Collection();

        if (!$this->getTaxAreaResults()) {
            return $taxAreaInfoCollection;
        }

        $taxAreaResults = $this->getTaxAreaResults();

        foreach ($taxAreaResults as $taxResponse) {
            $taxJurisdictions = $taxResponse->Jurisdiction;
            krsort($taxJurisdictions);
            $areaNames = array();

            foreach ($taxJurisdictions as $areaJursdiction) {
                $areaNames[] = $areaJursdiction->_;
            }

            $areaName = ucwords(strtolower(implode(', ', $areaNames)));
            $taxAreaInfo = new Varien_Object();
            $taxAreaInfo->setAreaName($areaName);
            $taxAreaInfo->setTaxAreaId($taxResponse->taxAreaId);

            if (property_exists($taxResponse, "PostalAddress")) {
                $taxAreaInfo->setTaxAreaCity($taxResponse->PostalAddress->City);
            } else {
                $taxAreaInfo->setTaxAreaCity($this->getRequestCity());
            }

            $taxAreaInfo->setRequestCity($this->getRequestCity());

            try {
                $taxAreaInfoCollection->addItem($taxAreaInfo);
            } catch (Exception $e) {
                if (Mage::helper('vertextax')->isLoggingEnabled()) {
                    Mage::log($e->getMessage(), null, 'vertex.log', true);
                }
            }
        }

        return $taxAreaInfoCollection;
    }
}
