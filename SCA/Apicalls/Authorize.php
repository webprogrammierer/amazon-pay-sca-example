<?php

session_start();

include '../../amazon-pay.phar';
require_once '../../config.php';

// Instantiate the client object with the configuration
$client = new AmazonPay\Client($amazonpay_config);

// Create the parameters array to set the order
$requestParameters = array();
$requestParameters['amazon_order_reference_id'] = $_SESSION['amazon_order_reference_id'];
$requestParameters['mws_auth_token'] = null;

    $requestParameters['authorization_amount'] = '19.95';
    $requestParameters['authorization_reference_id'] = uniqid();
    $requestParameters['seller_authorization_note'] = 'Authorizing and capturing the payment';
    $requestParameters['transaction_timeout'] = 0;
    
    // For physical goods the capture_now is recommended to be set to false
    // When set to false, you will need to make a separate Capture API call in order to get paid
    // If you are selling digital goods or plan to ship the physical good immediately, set it to true
    $requestParameters['capture_now'] = true; // false;
    $requestParameters['soft_descriptor'] = null;

    $response = $client->authorize($requestParameters);

// Pretty print the Json and then echo it for the Ajax success to take in
$json = json_decode($response->toJson());
echo json_encode($json, JSON_PRETTY_PRINT);
