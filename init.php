<?php

$init = function ($bootstrap) {

    $base = Core_Model_Directory::getBasePathTo("/app/local/modules/WalletUnitpayPS");
    # Register assets
    Nwicode_Assets::registerAssets("WalletUnitpayPS", "/app/local/modules/WalletUnitpayPS/resources/var/apps/");
    Nwicode_Assets::addJavascripts([
        "modules/walletunitpayps/controllers/walletunitpayps.js",
        "modules/walletunitpayps/factories/walletunitpayps.js",
    ]);

};