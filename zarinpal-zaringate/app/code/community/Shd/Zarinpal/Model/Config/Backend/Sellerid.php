<?php
/**
 * Magento
 * @category   Payment
 * @package    Shd_Zarinpal
 * @copyright  Copyright (c) 2013 Shayan Davarzani (shayandavarzani@gmail.com)
 * @see https://github.com/shayand
 */
class Shd_Zarinpal_Model_Config_Backend_Sellerid extends Mage_Core_Model_Config_Data
{
    /**
     * Verify seller id in ClickandBuy registration system to reduce configuration failures (experimental)
     *
     * @return Shd_Zarinpal_Model_Zarinpal_Config_Backend_Sellerid
     */
    protected function _beforeSave()
    {
    	try {
    	    if ($this->getValue()) {
    			$client = new Varien_Http_Client();
    			$client->setUri((string)Mage::getConfig()->getNode('shd/zarinpal/verify_url'))
    				->setConfig(array('timeout'=>10,))
    				->setHeaders('accept-encoding', '')
    				->setParameterPost('seller_id', $this->getValue())
    				->setMethod(Zend_Http_Client::POST);
    			$response = $client->request();
//    			$responseBody = $response->getBody();
//    			if (empty($responseBody) || $responseBody != 'VERIFIED') {
    				// verification failed. throw error message (not implemented yet).
//    			}

    			// okay, seller id verified. continue saving.
    	    }
		} catch (Exception $e) {
			// verification system unavailable. no further action.
		}

        return $this;
    }
}
