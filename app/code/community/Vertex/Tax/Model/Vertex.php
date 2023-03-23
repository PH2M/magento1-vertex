<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_vertex extends Mage_Core_Model_Abstract
{
    /**
     * @return Mage_Core_Helper_Abstract|Vertex_Tax_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('vertextax');
    }

    /**
     * @param $request
     * @param $type
     * @param null $order
     *
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    public function sendApiRequest($request, $type, $order = null)
    {
        $objectId = null;
        if (strpos($type, 'invoice') === 0) {
            $objectId = $order->getId();
            $storeId = $order->getStoreId();
        } elseif ($type == 'quote') {
            $quote = $this->getHelper()->getSession()->getQuote();
            $objectId = $quote->getId();
            $storeId = $quote->getStoreId();
        } elseif ($type == 'tax_area_lookup') {
            $objectId = 0;
            if (is_object(
                $this->getHelper()->getSession()->getQuote()
            )) {
                $quote = $this->getHelper()->getSession()->getQuote();
                $objectId = $quote->getId();
                $storeId = $quote->getStoreId();
            } else {
                $storeId = Mage::app()->getStore()->getStoreId();
            }
        }

        try {
            $apiUrl = $this->getHelper()->getVertexHost($storeId);

            if ($type == 'tax_area_lookup') {
                $apiUrl = $this->getHelper()->getVertexAddressHost($storeId);
            }

            $soapParams = array(
                'connection_timeout' => 300,
                'trace'              => true,
                'soap_version'       => SOAP_1_1
            );

            if (stripos($apiUrl, "wsdl") === false) {
                $apiUrl .= "?wsdl";
            }

            if (stripos($apiUrl, "60") !== false) {
                $code = "60";
            } else {
                $code = "70";
            }

            $context = array(
                'ssl_method'     => SOAP_SSL_METHOD_TLS,
                'cache_wsdl'     => WSDL_CACHE_NONE,
                'stream_context' => stream_context_create(
                    array(
                        'ssl' => array(
                            'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                            'ciphers'       => 'SHA2',
                        )
                    )
                )
            );

            $soapParams['stream_context'] = $context; // for TLS 1.2
            $client = new SoapClient($apiUrl, $soapParams);

            if ($type == 'tax_area_lookup') {
                $lookupFunc = "LookupTaxAreas" . $code;
                $taxRequestResult = $client->$lookupFunc($request);
            } else {
                $calculateFunc = "calculateTax" . $code;
                $taxRequestResult = $client->$calculateFunc($request);
            }
        } catch (Exception $e) {
            if (isset($client) && $client instanceof SoapClient) {
                $this->logRequest($type, $objectId, $client->__getLastRequest(), $client->__getLastResponse());
            } else {
                $this->logRequest($type, $objectId, $e->getMessage(), $e->getMessage());
            }

            return $e;
        }

        $totalTax = 0;
        $taxAreaId = 0;

        if (strpos($type, 'invoice') === 0) {
            $totalTax = $taxRequestResult->InvoiceResponse->TotalTax->_;
            $lineItem = $taxRequestResult->InvoiceResponse->LineItem;
            if (is_array($lineItem)) {
                $taxAreaId = $lineItem[0]->Customer->Destination->taxAreaId;
            } else {
                $taxAreaId = $lineItem->Customer->Destination->taxAreaId;
            }
        } elseif ($type == 'quote') {
            $totalTax = $taxRequestResult->QuotationResponse->TotalTax->_;
            $lineItem = $taxRequestResult->QuotationResponse->LineItem;
            if (is_array($lineItem)) {
                $taxAreaId = $lineItem[0]->Customer->Destination->taxAreaId;
            } else {
                $taxAreaId = $lineItem->Customer->Destination->taxAreaId;
            }
        } elseif ($type == 'tax_area_lookup') {
            $taxAreaResults = $taxRequestResult->TaxAreaResponse->TaxAreaResult;
            if (is_array($taxAreaResults)) {
                $taxAreaResIds = array();
                foreach ($taxAreaResults as $taxAreaResult) {
                    $taxAreaResIds[] = $taxAreaResult->taxAreaId;
                }

                $taxAreaId = implode(',', $taxAreaResIds);
            } else {
                $taxAreaId = $taxAreaResults->taxAreaId;
            }
        }

        $this->logRequest(
            $type,
            $objectId,
            $client->__getLastRequest(),
            $client->__getLastResponse(),
            $totalTax,
            $taxAreaId
        );

        return $taxRequestResult;
    }

    /**
     * @param $type
     * @param $objectId
     * @param $requestXml
     * @param $responseXml
     * @param int $totalTax
     * @param int $taxAreaId
     */
    public function logRequest($type, $objectId, $requestXml, $responseXml, $totalTax = 0, $taxAreaId = 0)
    {
        $taxRequest = Mage::getModel('vertextax/taxRequest');
        $taxRequest->setRequestType($type);
        $taxRequest->setRequestDate(Mage::getSingleton('core/date')->date());

        if (strpos($type, 'invoice') === 0) {
            $taxRequest->setOrderId($objectId);
        } elseif ($type == 'quote' || $type = 'tax_area_lookup') {
            $taxRequest->setQuoteId($objectId);
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($requestXml);
        $dom->formatOutput = true;

        if ($dom->saveXml()) {
            $requestXml = $dom->saveXml();
        }

        $dom->loadXML($responseXml);
        $dom->formatOutput = true;

        if ($dom->saveXml()) {
            $responseXml = $dom->saveXml();
        }

        $totalNode = $dom->getElementsByTagName('Total');
        $subtotalNode = $dom->getElementsByTagName('SubTotal');
        $lookupResultNode = $dom->getElementsByTagName('Status');
        $addressLookupFaultNode = $dom->getElementsByTagName('exceptionType');
        $total = 0;
        $subtotal = 0;
        $lookupResult = "";

        if ($totalNode->length > 0) {
            $total = $totalNode->item(0)->nodeValue;
        }

        if ($subtotalNode->length > 0) {
            $subtotal = $subtotalNode->item(0)->nodeValue;
        }

        if ($lookupResultNode->length > 0) {
            $lookupResult = $lookupResultNode->item(0)->getAttribute('lookupResult');
        }

        if (!$lookupResult && $addressLookupFaultNode->length > 0) {
            $lookupResult = $addressLookupFaultNode->item(0)->nodeValue;
        }

        $sourcePath = $this->getHelper()->getSourcePath();
        $taxRequest->setSourcePath($sourcePath);
        $taxRequest->setTotalTax($totalTax);
        $taxRequest->setRequestXml($requestXml);
        $taxRequest->setResponseXml($responseXml);
        $taxRequest->setTaxAreaId($taxAreaId);
        $taxRequest->setTotal($total);
        $taxRequest->setSubTotal($subtotal);
        $taxRequest->setLookupResult($lookupResult);

        try {
            $taxRequest->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'exception.log');
        }
    }
}
