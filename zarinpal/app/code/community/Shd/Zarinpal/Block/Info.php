<?php
/**
 * Magento
 * @category   Payment
 * @package    Shd_Zarinpal
 * @copyright  Copyright (c) 2013 Shayan Davarzani (shayandavarzani@gmail.com)
 * @see https://github.com/shayand
 */
class Shd_Zarinpal_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('zarinpal/info.phtml');
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }

    public function toPdf()
    {
        $this->setTemplate('zarinpal/pdf/info.phtml');
        return $this->toHtml();
    }
}