<?php

class WalletUnitpayPS_Mobile_UnitpayController extends Application_Controller_Mobile_Default
{
    /**
     * Generate checkout data
     */
    private function getFormSignature($account, $currency, $desc, $sum, $secretKey)
    {
        $hashStr = $account . '{up}' . $currency . '{up}' . $desc . '{up}' . $sum . '{up}' . $secretKey;
        return hash('sha256', $hashStr);
    }

    public function preparepaymentAction()
    {
        $data = [];
        if ($params = Zend_Json::decode($this->getRequest()->getRawBody())) {
            $data['params'] = $params;
            $wallet = (new Wallet_Model_Wallet())->find($params['wallet_id']);

            if ($wallet->getId()) {
                //Создадим историю оплаты и вернем данные
                $model = new WalletUnitpayPS_Model_PaymentMethodsUnitpay();
                $model->find(['wallet_id' => $wallet->getId()]);

                $wallet_customer = (new Wallet_Model_Customer())->find($params['wallet_customer_id']);
                $customer = $this->getSession()->getCustomer();

                if ($model->getId() && $wallet_customer->getId()) {


                    //$data['unitpay'] = $model->getData();
                    $data['public_key'] = $model->getData('public_key');
                    $data['currency'] = $model->getData('currency');
                    $data['locale'] = $model->getData('locale');
                    $data['payment_settings'] = $model->getData();
                    unset($data['payment_settings']['secret_key']);
                    $data['params'] = $params;
                    $data['email'] = $customer->getEmail();
                    $data['name'] = trim($customer->getFirstname() . ' ' . $customer->getLastname());
                    $data['amount'] = $params['amount'];
                    //$data['signature'] = $this->getFormSignature($history->getId(),$model->getData('currency'),$data['description'],$data['amount'],$model->getData('secret_key'));

                    //Вернем и создадим виджет уже в приложении


                } else {
                    $data = ['error' => 1, 'message' => 'An error occurred during process. Please try again later.'];
                }
            } else {
                $data = ['error' => 1, 'message' => 'An error occurred during process. Please try again later.'];
            }
        } else {
            $data = ['error' => 1, 'message' => 'An error occurred during process. Please try again later.'];
        }

        $this->_sendHtml($data);
    }

    //Создадим платеж
    public function createpaymentAction()
    {
        $data = [];
        if ($params = Zend_Json::decode($this->getRequest()->getRawBody())) {
            $data['params'] = $params;
            $wallet = (new Wallet_Model_Wallet())->find($params['wallet_id']);

            if ($wallet->getId()) {
                //Создадим историю оплаты и вернем данные
                $model = new WalletUnitpayPS_Model_PaymentMethodsUnitpay();
                $model->find(['wallet_id' => $wallet->getId()]);

                $wallet_customer = (new Wallet_Model_Customer())->find($params['wallet_customer_id']);
                $customer = $this->getSession()->getCustomer();

                if ($model->getId() && $wallet_customer->getId()) {

                    //Создадим запись в истории
                    $history = new Wallet_Model_PaymentHistory();
                    $history
                        ->setWalletId($wallet->getId())
                        ->setWalletCustomerId($params['wallet_customer_id'])
                        ->setSumm($params['amount'])
                        ->setCode('unitpay')
                        ->setComplete(0)
                        ->save();


                    //$data['unitpay'] = $model->getData();
                    $data['history_id'] = $history->getId();
                    $data['public_key'] = $model->getData('public_key');
                    $data['currency'] = $model->getData('currency');
                    $data['locale'] = $model->getData('locale');
                    $data['payment_settings'] = $model->getData();
                    unset($data['payment_settings']['secret_key']);
                    $data['params'] = $params;
                    $data['email'] = $customer->getEmail();
                    $data['name'] = trim($customer->getFirstname() . ' ' . $customer->getLastname());
                    $data['description'] = __("Deposit funds in the wallet") . " #" . $history->getId();
                    $data['amount'] = $params['amount'];
                    //$data['signature'] = $this->getFormSignature($history->getId(),$model->getData('currency'),$data['description'],$data['amount'],$model->getData('secret_key'));

                    //Include library
                    require_once($_SERVER['DOCUMENT_ROOT'] . '/app/local/modules/WalletUnitpayPS/lib/Unitpay/unitpay.php');

                    //init payment
                    $unitPay = new UnitPay($model->getData('secret_key'));
                    $response = $unitPay->api('initPayment', [
                        'account'     => $data['history_id'],
                        //'account' => "test",	//test mode
                        'desc'        => $data['description'],
                        'sum'         => $params['amount'],
                        'currency'    => $model->getData('currency'),
                        'locale'      => $model->getData('locale'),
                        'paymentType' => $params['code'],
                        'currency'    => $model->getData('currency'),
                        'projectId'   => $model->getData('project_id'),
                        //'resultUrl' => $returnUrl,
                        'resultUrl'   => "",
                        "hideBackUrl" => true,
                        //'backUrl' => $returnUrl,
                        'phone'       => $params['phone'],
                        'clientId'    => $params['client_id'],
                    ]);

                    // If need user redirect on Payment Gate
                    if (isset($response->result->type)
                        && $response->result->type == 'redirect') {
                        // Url on PaymentGate
                        $redirectUrl = $response->result->redirectUrl;
                        // Payment ID in Unitpay (you can save it)
                        $paymentId = $response->result->paymentId;
                        // User redirect
                        $history->setPaymentUrl($redirectUrl)->setData('payment_id', $paymentId)->save();
                        $data['success'] = true;
                        $data['redirect_url'] = $redirectUrl;
                        $data['payment_id'] = $paymentId;


                        // If without redirect (invoice)
                    } elseif (isset($response->result->type)
                        && $response->result->type == 'invoice') {
                        // Url on receipt page in Unitpay
                        $receiptUrl = $response->result->receiptUrl;
                        // Payment ID in Unitpay (you can save it)
                        $paymentId = $response->result->paymentId;
                        // Invoice Id in Payment Gate (you can save it)
                        $invoiceId = $response->result->invoiceId;
                        // User redirect
                        $data['success'] = true;
                        $data['redirect_url'] = $receiptUrl;
                        $data['payment_id'] = $paymentId;
                        $history->setPaymentUrl($redirectUrl)->setData('payment_id', $paymentId)->save();

                        // If error during api request
                    } elseif (isset($response->error->message)) {
                        $error = $response->error->message;
                        //print 'Error: '.$error;
                        $data['success'] = false;
                        $data['error_msg'] = $error;
                        $history->setComplete(-1)->save();

                    }

                } else {
                    $data = ['error' => 1, 'message' => 'An error occurred during process. Please try again later.'];
                }
            } else {
                $data = ['error' => 1, 'message' => 'An error occurred during process. Please try again later.'];
            }
        } else {
            $data = ['error' => 1, 'message' => 'An error occurred during process. Please try again later.'];
        }

        $this->_sendHtml($data);
    }

    //проверим оплату
    public function notifyAction()
    {
        $data = ["r" => "wait"];
        if ($params = $this->getRequest()->getParams()) {
            $data['method'] = $params['method'];
            $unitpay_info = $params['params'];
            $data['unitpay_info'] = $unitpay_info;

            $history = new Wallet_Model_PaymentHistory();
            $history->find($unitpay_info['account']);
            if ($history->getId()) {
                $data['history_info'] = $history->getData();

                //Проверим получателя
                $wallet_customer = (new Wallet_Model_Customer())->find($history->getData('wallet_customer_id'));
                if ($wallet_customer->getId()) {

                    //Для проверки мы только сравним суммы
                    if ($data['method'] == "check") {
                        $data = ["result" => ["message" => "Запрос успешно обработан"]];
                    } else {
                        //Сравним сумму и номер оплаты
                        if ($history->getData('payment_id') == $unitpay_info['unitpayId'] && $history->getData('summ') == $unitpay_info['orderSum']) {

                            //Если проверка, то покажем ее
                            if ($data['method'] == "check") {
                                $data = ["result" => ["message" => "Запрос успешно обработан"]];
                            } else {
                                if ($data['method'] == "error") {
                                    //отмена платежа почемуто - переведем в статус отмены
                                    $history->setComplete(-1)->save();
                                    $data = ["result" => ["message" => "Запрос успешно обработан"]];
                                } else {
                                    if ($data['method'] == "pay") {


                                        //Платеж прошел
                                        $history->setComplete(1)->save();
                                        $wallet_customer->addTransaction($history->getSumm(),
                                            "Unitpay - " . __("Deposit funds in the wallet"), 'in', 0,
                                            $wallet_customer->getId());
                                        $data = ["result" => ["message" => "Запрос успешно обработан"]];
                                    }
                                }
                            }


                        } else {
                            $data = ["error" => ["message" => "Данные оплаты не совпадают."]];
                        }


                    }


                } else {
                    $data = ["error" => ["message" => "Номер клиента не найден."]];
                }
            } else {
                //ошибка, платеж не найден
                $data = ["error" => ["message" => "Номер оплаты не найден."]];
            }

        }
        $this->_sendHtml($data);
    }

}