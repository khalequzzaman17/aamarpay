<?php
function aamarpay_config()
{
    $configarray = array(
        "FriendlyName" => array(
            "Type" => "System",
            "Value" => "aamarPay"
        ),
        "username" => array(
            "FriendlyName" => "Merchant ID",
            "Type" => "text",
            "Size" => "20"
        ),
        "transmethod" => array(
            "FriendlyName" => "Signature Key",
            "Type" => "text",
            "Size" => "30"
        ),
        "additional_per_fee" => array(
            "FriendlyName" => "Additional Service Charge %",
            "Type" => "text",
            "Size" => "30",
            "Description" => "<br>If You Want to add Additional Service Charge Then Enter The Ratio in Integer Format. Like for 3% input value will be 3 and For No Additional Charge input Value 0"
        ),
        "additional_fixed_fee" => array(
            "FriendlyName" => "Additional Service Charge",
            "Type" => "text",
            "Size" => "30",
            "Description" => "<br>If You Want to add Additional Service Charge Then Enter The Fixed Amount in Integer Format. Like for 20. and For No Additional Charge input Value 0"
        ),
        "testmode" => array(
            "FriendlyName" => "Test Mode",
            "Type" => "yesno",
            "Description" => "Tick this to Run on test MODE"
        )
    );
    return $configarray;
}
function aamarpay_link($params)
{
    $gatewayusername      = $params['username'];
    $gatewaytransmethod   = $params['transmethod'];
    $invoiceid            = $params['invoiceid'];
    $description          = $params["description"];
    $amount               = $params['amount'];
    $currency             = $params['currency'];
    $additional_per_fee   = $params['additional_per_fee'];
    $additional_fixed_fee = $params['additional_fixed_fee'];
    $additonal_service    = ($amount * $additional_per_fee) / 100;
    $total_amount         = $amount + $additonal_service;
    $firstname            = $params['clientdetails']['firstname'];
    $lastname             = $params['clientdetails']['lastname'];
    $email                = $params['clientdetails']['email'];
    $address1             = $params['clientdetails']['address1'];
    $address2             = $params['clientdetails']['address2'];
    $city                 = $params['clientdetails']['city'];
    $state                = $params['clientdetails']['state'];
    $postcode             = $params['clientdetails']['postcode'];
    $country              = $params['clientdetails']['country'];
    $phone                = $params['clientdetails']['phonenumber'];
    $companyname          = $params['companyname'];
    $systemurl            = $params['systemurl'];
    $currency             = $params['currency'];
    $basecurrencyamount   = $params['basecurrencyamount'];
    $basecurrency         = $params['basecurrency'];
    $cus_name             = $firstname . ' ' . $lastname;
    $success_url          = $params['systemurl'] . 'modules/gateways/callback/aamarpay.php';
    $failed_url           = $params['systemurl'] . 'modules/gateways/callback/aamarpay.php';
    $cancel_url           = $params['systemurl'] . '/viewinvoice.php?id=' . $invoiceid;
    $url                  = "https://secure.aamarpay.com/request.php";
    $fields               = array(
        'store_id' => $gatewayusername,
        'amount' => $total_amount,
        'currency' => $currency,
        'tran_id' => $invoiceid,
        'cus_name' => $cus_name,
        'cus_email' => $email,
        'cus_add1' => $address1,
        'cus_add2' => $address2,
        'cus_city' => $city,
        'cus_state' => $state,
        'cus_postcode' => $postcode,
        'cus_country' => $country,
        'cus_phone' => $phone,
        'ship_name' => $companyname,
        'ship_add1' => $systemurl,
        'desc' => $description,
        'success_url' => $success_url,
        'fail_url' => $failed_url,
        'cancel_url' => $cancel_url,
        'opt_a' => $amount,
        'opt_b' => $currency,
        'opt_c' => '',
        'opt_d' => '',
        'signature_key' => $gatewaytransmethod
    );
    $domain               = $_SERVER["SERVER_NAME"];
    $ip                   = $_SERVER["SERVER_ADDR"];
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "REMOTE_ADDR: $ip",
        "HTTP_X_FORWARDED_FOR: $ip"
    ));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $domain);
    curl_setopt($ch, CURLOPT_INTERFACE, $ip);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result     = curl_exec($ch);
    $url_decode = json_decode($result);
    $webaddr    = "https://secure.aamarpay.com";
    curl_close($ch);
    $code .= '<form action="' . $webaddr . '' . $url_decode . '" method="post">';
    $code .= '<input type="hidden" name="store_id" value="' . $gatewayusername . '">';
    $code .= '<input type="hidden" name="tran_id" value="' . $invoiceid . '">';
    $code .= '<input type="hidden" name="amount" value="' . $total_amount . '" >';
    $code .= '<input type="hidden" name="success_url"  value="' . $params['systemurl'] . '/modules/gateways/callback/aamarpay.php" />';
    $code .= '<input type="hidden" name="fail_url" value="' . $params['systemurl'] . '/modules/gateways/callback/aamarpay.php" />';
    $code .= '<input type="hidden" name="cancel_url" value="' . $params['systemurl'] . '/clientarea.php?action=invoices" />';
    $code .= '<input type="hidden" name="currency" value="' . $currency . '" >';
    $code .= '<input type="hidden" name="cus_name" value="' . $firstname . ' ' . $lastname . '">';
    $code .= '<input type="hidden" name="cus_add1" value="' . $address1 . '" >';
    $code .= '<input type="hidden" name="cus_add2" value="' . $address2 . '" >';
    $code .= '<input type="hidden" name="cus_city" value="' . $city . '" >';
    $code .= '<input type="hidden" name="cus_state" value="' . $state . '">';
    $code .= '<input type="hidden" name="cus_postcode" value="' . $postcode . '">';
    $code .= '<input type="hidden" name="cus_country" value="' . $country . '">';
    $code .= '<input type="hidden" name="cus_phone" value="' . $phone . '" >';
    $code .= '<input type="hidden" name="cus_email" value="' . $email . '" >';
    $code .= '<input type="hidden" name="ship_name" value="' . $companyname . '" >';
    $code .= '<input type="hidden" name="ship_add1" value="' . $systemurl . '" >';
    $code .= '<input type="hidden" name="signature_key" value="' . $gatewaytransmethod . '">';
    $code .= '<input type="hidden" name="opt_a" value="' . $amount . '">';
    $code .= '<input type="hidden" name="opt_b" value="' . $currency . '">';
    $code .= '<input type="hidden" name="desc" value="' . $description . '">';
    $code .= '<input type="submit" class="btn btn-success" value="Pay Now" />
</form>';
    return $code;
}
?>
