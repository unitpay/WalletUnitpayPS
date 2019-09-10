<?php
/**
 *
 * Schema definition for 'wallet_payment_systems_unitpay'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_payment_systems_unitpay'] = [
    'wallet_payment_systems_unitpay_id' => [
        'type'           => 'int(11) unsigned',
        'auto_increment' => true,
        'primary'        => true,
    ],
    'wallet_id'                         => [
        'type'        => 'int(11) unsigned',
        'index'       => [
            'key_name'   => 'wallet_id',
            'index_type' => 'BTREE',
            'is_null'    => false,
            'is_unique'  => false,
        ],
        'foreign_key' => [
            'table'     => 'wallet',
            'column'    => 'wallet_id',
            'name'      => 'wallet_unitpay_11',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
    ],
    'title'                             => [
        'type'    => 'varchar(150)',
        'is_null' => true,
    ],
    'project_id'                        => [
        'type'    => 'varchar(14)',
        'is_null' => true,
    ],
    'public_key'                        => [
        'type'    => 'varchar(150)',
        'is_null' => true,
    ],
    'secret_key'                        => [
        'type'    => 'varchar(150)',
        'is_null' => true,
    ],
    'currency'                          => [
        'type'    => 'varchar(4)',
        'default' => 'RUB',
    ],
    'locale'                            => [
        'type'    => 'varchar(4)',
        'default' => 'ru',
    ],
    'model'                             => [
        'type' => 'varchar(150)',
    ],
    'enabled'                           => [
        'type'    => 'tinyint(1)',
        'default' => '0',
    ],
    'mc_enabled'                        => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'mc_title'                          => [
        'type'    => 'varchar(150)',
        'default' => 'Мобильный платеж',
    ],
    'sms_enabled'                       => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'sms_title'                         => [
        'type'    => 'varchar(150)',
        'default' => 'SMS-оплата',
    ],
    'card_enabled'                      => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'card_title'                        => [
        'type'    => 'varchar(150)',
        'default' => 'Пластиковые карты',
    ],
    'webmoney_enabled'                  => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'webmoney_title'                    => [
        'type'    => 'varchar(150)',
        'default' => 'WebMoney',
    ],
    'yandex_enabled'                    => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'yandex_title'                      => [
        'type'    => 'varchar(150)',
        'default' => 'Яндекс.Деньги',
    ],
    'qiwi_enabled'                      => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'qiwi_title'                        => [
        'type'    => 'varchar(150)',
        'default' => 'Qiwi',
    ],
    'paypal_enabled'                    => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'paypal_title'                      => [
        'type'    => 'varchar(150)',
        'default' => 'PayPal',
    ],
    'liqpay_enabled'                    => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'liqpay_title'                      => [
        'type'    => 'varchar(150)',
        'default' => 'LiqPay',
    ],
    'alfaclick_enabled'                 => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'alfaclick_title'                   => [
        'type'    => 'varchar(150)',
        'default' => 'Альфа-Клик',
    ],
    'cash_enabled'                      => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'cash_title'                        => [
        'type'    => 'varchar(150)',
        'default' => 'Наличные',
    ],
    'applepay_enabled'                  => [
        'type'    => 'tinyint(1)',
        'default' => '1',
    ],
    'applepay_title'                    => [
        'type'    => 'varchar(150)',
        'default' => 'Apple Pay',
    ],
    'created_at'                        => [
        'type' => 'datetime',
    ],
    'updated_at'                        => [
        'type' => 'datetime',
    ],
];	