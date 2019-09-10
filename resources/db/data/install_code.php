<?php

$datas = [
    [
        'title'      => 'Unitpay',
        'model'      => 'WalletUnitpayPS_Model_PaymentMethodsUnitpay',
        'type'       => 'url',
        'state_name' => 't',
        'url'        => 'walletunitpayps/mobile_walletunitpay/find',
        'code'       => 'WalletUnitpayPS',
    ],
];

foreach ($datas as $data) {
    $method = new Wallet_Model_PaymentSystems();
    $method
        ->setData($data)
        ->insertOnce(['code']);
}
