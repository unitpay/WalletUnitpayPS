App.config(function ($stateProvider, HomepageLayoutProvider) {

    $stateProvider.state('walletunitpay-payment', {
        url: BASE_PATH + "/walletunitpayps/mobile_walletunitpay/find/value_id/:value_id/wallet_id/:wallet_id/wallet_customer_id/:wallet_customer_id/amount/:amount",
        controller: 'WalletUnitpayPSController',
        templateUrl: "modules/walletunitpayps/templates/l1/payment.html"
    });
    $stateProvider.state('walletunitpay-payment-result', {
        url: BASE_PATH + "/walletunitpayps/mobile_yandex/result/value_id/:value_id/wallet_id/:wallet_id/wallet_customer_id/:wallet_customer_id/status/:status",
        controller: 'WalletUnitpayPSResultController',
        templateUrl: "modules/walletunitpayps/templates/l1/payment.html"
    });

}).controller('WalletUnitpayPSController', function ($scope, $state, $rootScope, $stateParams, $timeout, $translate, WalletUnitpayPSFactory, Loader, $window, Dialog, $ionicHistory) {

    console.log("WalletUnitpayPSController fired!");

    $scope.data = {};
    WalletUnitpayPSFactory.value_id = $stateParams.value_id;
    $scope.value_id = $stateParams.value_id;
    $scope.data.value_id = $stateParams.value_id;
    $scope.data.amount = $stateParams.amount;
    $scope.data.wallet_id = $stateParams.wallet_id;
    $scope.data.wallet_customer_id = $stateParams.wallet_customer_id;
    $scope.is_loading = true;
    $scope.payment_data = {};
    $scope.formData = {};
    $scope.old_style = true;
    $scope.stage = 1;
    $scope.amount = 0;
    $scope.selected_code = "";
    $scope.selected_method = "";
    $scope.data.phone = "";
    $scope.data.client_id = "";
    $scope.alfaclick_id = "";
    console.log("WalletUnitpayPSController:");
    console.log($scope.data);
    Loader.show();


    $scope.startWidget = function () {
        WalletUnitpayPSFactory.preparePayment($scope.data).then(function (formData) {
            console.log("WalletUnitpayPSFactory.preparePayment return:");
            console.log(formData);
            console.log($scope.data);
            $scope.payment_data = formData.payment_settings;
            $scope.formData = formData;
            $scope.amount = formData.amount;
            Loader.hide();
            $scope.is_loading = false;
        }, function (error_data) {
            console.log("WalletUnitpayPSFactory.preparePayment return ERROR:");
            console.log(error_data);
            Loader.hide();
            $scope.is_loading = false;
        });
    }


    $scope.doPayment = function (code) {
        $scope.selected_code = code;
        console.log("doPayment: " + $scope.selected_code);
        //Запросим поля
        if ($scope.selected_code == "qiwi" || $scope.selected_code == "mc") {
            if ($scope.selected_code == "qiwi") $scope.selected_method = $scope.payment_data.qiwi_title;
            if ($scope.selected_code == "mc") $scope.selected_method = $scope.payment_data.mc_title;
            $scope.stage = 2;
        } else if ($scope.selected_code == "alfaClick") {
            if ($scope.selected_code == "alfaClick") $scope.selected_method = $scope.payment_data.alfaclick_title;
            $scope.stage = 2;

        } else {
            Loader.show();
            $scope.is_loading = false;
            WalletUnitpayPSFactory.createPayment($scope.selected_code, $scope.data.amount, $scope.data.phone, $scope.data.client_id, $scope.data.wallet_id, $scope.data.wallet_customer_id).then(function (data) {
                console.log("WalletUnitpayPSFactory.createPayment return:");
                console.log(data);
                console.log($rootScope);
                console.log($scope);
                Loader.hide();
                $scope.is_loading = false;
                if (data.success) {
                    if ($rootScope.isOverview) window.open(data.redirect_url, "_blank", "location=yes"); else window.open(data.redirect_url, "_system", "location=yes");
                    $scope.stage = 3;

                } else {
                    Dialog.alert($translate.instant("Error"), data.error_msg, "OK");

                }

            }, function (error_data) {
                console.log("WalletUnitpayPSFactory.createPayment return ERROR:");
                console.log(error_data);
                Loader.hide();
                $scope.is_loading = false;
            });
        }
    }

    $scope.doPayment2 = function () {
        Loader.show();
        $scope.is_loading = false;
        WalletUnitpayPSFactory.createPayment($scope.selected_code, $scope.data.amount, $scope.data.phone, $scope.data.client_id, $scope.data.wallet_id, $scope.data.wallet_customer_id).then(function (data) {
            console.log("WalletUnitpayPSFactory.createPayment return:");
            console.log(data);
            Loader.hide();
            $scope.is_loading = false;
            if (data.success) {
                if ($rootScope.isOverview) window.open(data.redirect_url, "_blank", "location=yes"); else window.open(data.redirect_url, "_system", "location=yes");
                $scope.stage = 3;

            } else {
                Dialog.alert($translate.instant("Error"), data.error_msg, "OK");

            }

        }, function (error_data) {
            console.log("WalletUnitpayPSFactory.createPayment return ERROR:");
            console.log(error_data);
            Loader.hide();
            $scope.is_loading = false;
        });
    }


    $scope.setStage = function (stage) {
        $scope.stage = stage;
    }

    $scope.closeWindow = function () {
        $ionicHistory.nextViewOptions({
            historyRoot: true,
            disableAnimate: false
        });
        $state.go('home').then(function () {
            $state.go('wallet-view', {
                value_id: $scope.value_id
            });
        });

    }

    $scope.startWidget();


}).controller('WalletUnitpayPSResultController', function ($scope, $state, $stateParams, $timeout, WalletUnitpayPSFactory, Loader, $window, Dialog, HomepageLayout, $ionicHistory) {
    angular.extend($scope, {
        value_id: $stateParams.value_id,
        layout: HomepageLayout
    });
    console.log("WalletUnitpayPSResultController fired!");

    $scope.data = {};
    WalletUnitpayPSFactory.value_id = $stateParams.value_id;
    $scope.value_id = $stateParams.value_id;
    $scope.data.value_id = $stateParams.value_id;
    $scope.data.status = $stateParams.status;
    $scope.data.wallet_id = $stateParams.wallet_id;
    $scope.data.wallet_customer_id = $stateParams.wallet_customer_id;
    $scope.is_loading = false;
    $scope.old_style = true;
    console.log("WalletUnitpayPSResultController:");
    console.log($scope.data);

    $scope.closeWindow = function () {
        $ionicHistory.nextViewOptions({
            historyRoot: true,
            disableAnimate: false
        });
        $state.go('home').then(function () {
            $state.go('wallet-view', {
                value_id: $scope.value_id
            });
        });

    }

});