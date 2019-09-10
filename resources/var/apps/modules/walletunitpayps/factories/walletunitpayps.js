App.factory('WalletUnitpayPSFactory', function ($http, Url, $session, $window, $pwaRequest) {

    var factory = {};
    factory.value_id = null;


    factory.preparePayment = function (data) {

        if (!this.value_id) {
            return $pwaRequest.reject("[WalletUnitpayPS::createform] missing value_id.");
        }

        return $pwaRequest.post("walletunitpayps/mobile_unitpay/preparepayment", {
            urlParams: {
                value_id: this.value_id
            },
            data: data,
            cache: false,
            refresh: true
        });
    };

    factory.createPayment = function (code, amount, phone, client_id, wallet_id, wallet_customer_id) {

        if (!this.value_id) {
            return $pwaRequest.reject("[WalletUnitpayPS::createPayment] missing value_id.");
        }

        return $pwaRequest.post("walletunitpayps/mobile_unitpay/createpayment", {
            urlParams: {
                value_id: this.value_id
            },
            data: {
                "code": code,
                "amount": amount,
                "wallet_id": wallet_id,
                "wallet_customer_id": wallet_customer_id,
                "phone": phone,
                "client_id": client_id
            },
            cache: false,
            refresh: true
        });
    };

    return factory;
}); 