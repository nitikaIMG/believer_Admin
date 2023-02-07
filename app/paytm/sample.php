<?php

require_once("./PaytmChecksum.php");

/* initialize an array */
$paytmParams = array();

/* add parameters in Array */
$paytmParams["MID"] = "XcpurJ11272747141801";
$paytmParams["ORDERID"] = "V11-add-16353205976";

/**
* Generate checksum by parameters we have
* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
*/
$paytmChecksum = PaytmChecksum::generateSignature($paytmParams, 'HNzGBKb@BKQ2SOA_');
$verifySignature = PaytmChecksum::verifySignature($paytmParams, 'HNzGBKb@BKQ2SOA_', $paytmChecksum);
echo sprintf("generateSignature Returns: %s\n", $paytmChecksum);
echo sprintf("verifySignature Returns: %b\n\n", $verifySignature);


/* initialize JSON String */ 
$body = "{\"mid\":\"XcpurJ11272747141801\",\"orderId\":\"S11-add-16353205976\"}";

/**
* Generate checksum by parameters we have in body
* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
*/
$paytmChecksum = PaytmChecksum::generateSignature($body, 'HNzGBKb@BKQ2SOA_');
$verifySignature = PaytmChecksum::verifySignature($body, 'HNzGBKb@BKQ2SOA_', $paytmChecksum);
echo sprintf("generateSignature Returns: %s\n", $paytmChecksum);
echo sprintf("verifySignature Returns: %b\n\n", $verifySignature);