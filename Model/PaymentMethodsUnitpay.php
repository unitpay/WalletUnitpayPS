<?php

/**
 * Class WalletYandexPS_Model_PaymentMethodsYandex
 *
 */
class WalletUnitpayPS_Model_PaymentMethodsUnitpay extends Core_Model_Default
{
    /**
     * WalletUnitpayPS_Model_PaymentMethodsUnitpay constructor.
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'WalletUnitpayPS_Model_Db_Table_PaymentMethodsUnitpay';
    }
}