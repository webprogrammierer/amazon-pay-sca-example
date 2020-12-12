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
$requestParameters['success_url'] = 'http://localhost/amazon-pay-sdk-samples/SCA/ScaSuccess.php';
$requestParameters['failure_url'] = 'http://localhost/amazon-pay-sdk-samples/SCA/ScaFailure.php';

// Confirm the order by making the ConfirmOrderReference API call
$response = $client->confirmOrderReference($requestParameters);
echo json_encode($response);
