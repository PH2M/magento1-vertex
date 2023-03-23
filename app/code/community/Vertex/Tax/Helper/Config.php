<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Helper_Config extends Mage_Core_Helper_Abstract
{
    const CONFIG_XML_PATH_ENABLE_VERTEX = 'tax/vertex_settings/enable_vertex';
    const CONFIG_XML_PATH_ENABLE_LOGGING = 'tax/vertex_settings/enable_logging';
    const CONFIG_XML_PATH_DEFAULT_TAX_CALCULATION_ADDRESS_TYPE = 'tax/calculation/based_on';
    const CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE = 'tax/classes/default_customer_code';
    const VERTEX_API_HOST = 'tax/vertex_settings/api_url';
    const CONFIG_XML_PATH_VERTEX_API_USER = 'tax/vertex_settings/login';
    const CONFIG_XML_PATH_VERTEX_API_KEY = 'tax/vertex_settings/password';
    const CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID = 'tax/vertex_settings/trustedId';
    const CONFIG_XML_PATH_VERTEX_COMPANY_CODE = 'tax/vertex_seller_info/company';
    const CONFIG_XML_PATH_VERTEX_LOCATION_CODE = 'tax/vertex_seller_info/location_code';
    const CONFIG_XML_PATH_VERTEX_STREET1 = 'tax/vertex_seller_info/streetAddress1';
    const CONFIG_XML_PATH_VERTEX_STREET2 = 'tax/vertex_seller_info/streetAddress2';
    const CONFIG_XML_PATH_VERTEX_CITY = 'tax/vertex_seller_info/city';
    const CONFIG_XML_PATH_VERTEX_COUNTRY = 'tax/vertex_seller_info/country_id';
    const CONFIG_XML_PATH_VERTEX_REGION = 'tax/vertex_seller_info/region_id';
    const CONFIG_XML_PATH_VERTEX_POSTAL_CODE = 'tax/vertex_seller_info/postalCode';
    const CONFIG_XML_PATH_VERTEX_INVOICE_DATE = 'tax/vertex_settings/invoice_tax_date';
    const CONFIG_XML_PATH_VERTEX_TRANSACTION_TYPE = 'SALE';
    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER = 'tax/vertex_settings/invoice_order';
    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS = 'tax/vertex_settings/invoice_order_status';
    const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';
    const VERTEX_ADDRESS_API_HOST = 'tax/vertex_settings/address_api_url';
    const VERTEX_CREDITMEMO_ADJUSTMENT_CLASS = 'tax/classes/creditmemo_adjustment_class';
    const VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE = 'tax/classes/creditmemo_adjustment_negative_code';
    const VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE = 'tax/classes/creditmemo_adjustment_positive_code';
    const VERTEX_GIFTWRAP_ORDER_CLASS = 'tax/classes/giftwrap_order_class';
    const VERTEX_GIFTWRAP_ORDER_CODE = 'tax/classes/giftwrap_order_code';
    const VERTEX_GIFTWRAP_ITEM_CLASS = 'tax/classes/giftwrap_item_class';
    const VERTEX_GIFTWRAP_ITEM_CODE_PREFIX = 'tax/classes/giftwrap_item_code';
    const VERTEX_PRINTED_GIFTCARD_CLASS = 'tax/classes/printed_giftcard_class';
    const VERTEX_PRINTED_GIFTCARD_CODE = 'tax/classes/printed_giftcard_code';
    const CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE = 'tax/vertex_settings/allow_cart_request';
    const CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON = 'tax/vertex_settings/show_manual_button';
    const CONFIG_XML_PATH_VERTEX_SHOW_POPUP = 'tax/vertex_settings/show_tarequest_popup';
    const MAX_CHAR_SHIPPING_CODE_ALLOWED = 40;

    /**
     * Returns an array with the allowed quote controllers
     *
     * @return array
     */
    public function getQuoteAllowedControllers()
    {
        $quoteAllowedControllers = array(
            'onepage',
            'multishipping',
            'sales_order_create',
            'express' // PayPal
        );

        return $quoteAllowedControllers;
    }
}
