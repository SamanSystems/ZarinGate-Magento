<?php
/**
 * Magento
 * @category   Payment
 * @package    Shd_Zarinpal
 * @copyright  Copyright (c) 2013 Shayan Davarzani (shayandavarzani@gmail.com)
 * @see https://github.com/shayand
 */

class Shd_Zarinpal_Model_Zarinpal extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    **/
    protected $_code = 'zarinpal';

    protected $_formBlockType = 'zarinpal/form';
    protected $_infoBlockType = 'zarinpal/info';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order;
	
	 public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('zarinpal/processing/redirect', array('_secure'=>true));
	  #return "http://Acquirer.sb24.com/ref-payment/ws/ReferencePayment?WSDL";
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    public function getUrl()
    {
    	$premiumLink = $this->getConfigData('premium_link');
    	if (empty($premiumLink))
    		return '';

		// do some manual processing for backwards compatibility
    	if (substr($premiumLink, -1) != '/') {
    		$premiumLink .= '/';
    	}
		$pStartPos = strpos($premiumLink, '://premium');
		$pEndPos = strpos($premiumLink, '/', $pStartPos+3);
 		$premiumPart = substr($premiumLink, 0, $pEndPos+1);

		// add url part for the callback controller
 		preg_match('/^http[s]?:\/\/[a-z0-9._-]*\/(.*)$/i', Mage::getUrl('zarinpal/processing/response', array('_secure'=>true)), $matches);
 		$url = $premiumPart . $matches[1];

        return $url;
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
    	if ($this->getConfigData('use_store_currency')) {
        	$price      = number_format($this->getOrder()->getGrandTotal()*100,0,'.','');
        	$currency   = $this->getOrder()->getOrderCurrencyCode();
    	} else {
        	$price      = number_format($this->getOrder()->getBaseGrandTotal()*100,0,'.','');
        	$currency   = $this->getOrder()->getBaseCurrencyCode();
    	}

        $params = array(
                   'price'					=>	$price,
                   'cb_currency'			=>	$currency,
                   'cb_content_name_utf'	=>	Mage::helper('zarinpal')->__('Your purchase at') . ' ' . Mage::app()->getStore()->getName(),
                   'externalBDRID'			=>	$this->getOrder()->getRealOrderId() . '-' . $this->getOrder()->getQuoteId(),
                 );

        return $params;
    }
    
}
