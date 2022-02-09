<?php
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");
$gatewaymodule = "aamarpay";
$GATEWAY       = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"])
    die("Module Not Activated");
$status     = $_REQUEST["pay_status"];
$invoiceid  = $_REQUEST["mer_txnid"];
$transid    = $_REQUEST["pg_txnid"];
$amount     = $_REQUEST["amount"];
$amount_rec = $_REQUEST["store_amount"];
$fee        = $_REQUEST["pg_service_charge_bdt"];
$reason     = $_REQUEST["reason"];
if ($_REQUEST["opt_b"] == 'USD') {
    $amount = $_REQUEST["opt_a"];
} else {
    $amount = $_REQUEST["amount"];
}
$invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY["name"]);
checkCbTransID($transid);
if ($status == "Successful") {
    print "<center>Please Wait.....Processing....</center>";
    $description = "\n\t<br/>Status : <b style='color:#5d994f;'>" . $status . " </b>\n\t<br>Invoice ID: " . $mer_txnid . "\n\t<br>Bank Transaction ID : " . $bank_txn . "\n\t<br>Card Type : " . $card_type . "\n\t<br>Card Number : " . $card_number . "\n\t<br>Currency: " . $currency_merchant . "\n\t<br>Transaction Time :  " . $pay_time . "  \n\t";
    addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule);
    logTransaction($GATEWAY["name"], $_POST, "Successful");
?><html><head></head><body onload="document.send_process.submit()"><form action="/clientarea.php?action=invoices"method="POST"name="send_process"></form></body></html><?php
    exit;
} else {
    print "<center>Please Wait.....Processing....</center>";
    $description = "\n\t<br/>Status : <b style='color:#FF0000;'>" . $status . " </b>\n\t<br>Failed Reason : " . $reason . "\n\t<br>Invoice ID: " . $mer_txnid . "\n\t<br>Bank Transaction ID : " . $bank_txn . "\n\t<br>Card Type : " . $card_type . "\n\t<br>Card Number : " . $card_number . "\n\t<br>Currency: " . $currency_merchant . "\n\t<br>Transaction Time :  " . $pay_time . "  \n\t";
    logTransaction($GATEWAY["name"], $_POST, "Unsuccessful");
?><html><head></head><body onload="document.send_process.submit()"><form action="/clientarea.php?action=invoices"method="POST"name="send_process"></form></body></html><?php
    exit;
}
?>
