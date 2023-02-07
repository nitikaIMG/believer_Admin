<?php
/*
 This script performs 4 major payout operations
  1) Check Balance
  2) Add Beneficiary
  3) Make Transfer
  4) Remove Beneficiary
  Comment out the operation(s) you don't wish to execute

  How to get started?
  1) Copy clientId/clientSecret from your Cashfree Merchant Dashboard (Go to Smart Payout -> Access Control -> API Keys)
  2) Whitelist the IP of the system this script is going to be run on (IP Whitelist tab)
  3) Run this by executing : `php execute.php`
*/

include("cfpayout.inc.php");

		$clientId = "CF9171ELC3FAVZ448Y6QY";
		$clientSecret = "a9c4e24426865b5d5511cdf2985e97974717745c";
		$stage = "TEST"; //use "PROD" for testing with live credentials

$authParams["clientId"] = $clientId;
$authParams["clientSecret"] = $clientSecret;
$authParams["stage"] = $stage;

try {

  $payout = new CfPayout($authParams);
} catch (Exception $e) {
  echo $e->getMessage();
  echo "\n";  
  die();
}

echo "--------------Fetching Balance---------------\n";
$balance = $payout->getBalance();
echo "Ledger balance is : " .$balance["ledger"];
echo "\n";
echo "Available balance is : " .$balance["available"];
echo "\n";

echo "--------------Adding Beneficiary---------------\n";
$beneficiary = [];


$alphaNumChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

$beneficiary["beneId"] = substr(str_shuffle($alphaNumChars), 0, 8);
$beneficiary["name"] = $bank_detail->accountholder;
$beneficiary["email"] = $user->email;
$beneficiary["phone"] = $user->mobile;
$beneficiary["bankAccount"] = $bank_detail->accno;
$beneficiary["ifsc"] = $bank_detail->ifsc;
$beneficiary["address1"] = "";
$beneficiary["city"] = "";
$beneficiary["state"] = "";
$beneficiary["pincode"] = "";

$response = $payout->addBeneficiary($beneficiary);

if ($response["status"] == "SUCCESS") {
  echo "Beneficary has been added successfully";
}  else {
  echo "Beneficary addition failed. ";
  echo "Reason - ".$response["message"];
}
echo "\n";


echo "--------------Requesting Transfer---------------\n";
$transfer = [];
$transfer["beneId"] = $beneficiary["beneId"];
$transfer["amount"] = $amount;
$transfer["transferId"] = time();
$transfer["remarks"] = "Transfer request from Payout kit";
$response = $payout->requestTransfer($transfer);

if ($response["status"] == "SUCCESS") {
  echo "Transfer processed successfully\n";
  echo "Cashfree reference id is ". $response["data"]["referenceId"];
  echo "\n";
  echo "Bank reference number is ". $response["data"]["utr"]; 
} else if ($response["status"] == "PENDING") {
  echo "Transfer request being processed at bank. Check the status after few minutes.\n";
  echo "Cashfree reference id is ". $response["data"]["referenceId"];
} else if ($response["status"] == "ERROR") {
  echo "Transfer request failed\n";
  echo "Reason - ". $response["message"];
}
echo "\n";

echo "--------------Removing Beneficiary---------------\n";
// $response = $payout->removeBeneficiary($beneficiary["beneId"]);
if ($response["status"] == "SUCCESS") {
 echo "Beneficiary with id ". $beneficiary["beneId"]. " has been removed";
} else {
  echo "Beneficiary removal failed. Please try again";
}
echo "\n";

?>
