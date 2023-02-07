<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
// following files need to be included
require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");
$checkSum = "";
// below code snippet is mandatory, so that no one can use your checksumgeneration url for other purpose .
$paramList = array();
$paramList["MID"] = $_POST["MID"];;                             //Provided by Paytm
$paramList["ORDER_ID"] = $_POST["ORDER_ID"];                    //unique OrderId for every request
$paramList["CUST_ID"] = $_POST["CUST_ID"];                      // unique customer identifier
$paramList["INDUSTRY_TYPE_ID"] = $_POST["INDUSTRY_TYPE_ID"];    //Provided by Paytm
$paramList["CHANNEL_ID"] = $_POST["CHANNEL_ID"];                //Provided by Paytm
$paramList["TXN_AMOUNT"] = $_POST["TXN_AMOUNT"];                // transaction amount
$paramList["WEBSITE"] = $_POST["WEBSITE"];                      //Provided by Paytm
$paramList["CALLBACK_URL"] = $_POST["CALLBACK_URL"];            //Provided by Paytm
$paramList["EMAIL"] = $_POST["EMAIL"];                          // customer email id
$paramList["MOBILE_NO"] = $_POST["MOBILE_NO"];                  // customer 10 digit mobile no.
$checkSum = getChecksumFromArray($paramList,"bKMfNxPPf_QdZppa");
$paramList["CHECKSUMHASH"] = $checkSum;
echo json_encode($paramList);
            die;
?>