<?php
    // Be sure your webserver is configured to never display the contents of this file under any circumstances.
    // The secret_key value below should be protected and never shared with anyone.

   $amazonpay_config = array(
        'merchant_id'   => 'A...', // Merchant/SellerID
        'access_key'    => 'AKIA...', // MWS Access Key
        'secret_key'    => 'SECRET_KEY_HERE', // MWS Secret Key
        'client_id'     => 'amzn1.application-oa2-client.XXXXX', // Login With Amazon Client ID
        'region'        => 'uk',  // uk (for UK/GB) or de (for EU)
        'currency_code' => 'GBP', // GBP (for UK/GB) or EUR (for EU)
        'sandbox'       => true); // Use sandbox test mode

function getWidgetsJsURL($config)
{
    if ($config['sandbox'])
        $sandbox = "sandbox/";
    else
        $sandbox = "";

    switch (strtolower($config['region'])) {
        case "us":
            return "https://static-na.payments-amazon.com/OffAmazonPayments/us/" . $sandbox . "js/Widgets.js";
            break;
        case "uk":
            return "https://static-eu.payments-amazon.com/OffAmazonPayments/gbp/" . $sandbox . "lpa/js/Widgets.js";
            break;
        case "jp":
            return "https://static-fe.payments-amazon.com/OffAmazonPayments/jp/" . $sandbox . "lpa/js/Widgets.js";
            break;
        default:
            return "https://static-eu.payments-amazon.com/OffAmazonPayments/eur/" . $sandbox . "lpa/js/Widgets.js";
            break;
    }
}

?>
