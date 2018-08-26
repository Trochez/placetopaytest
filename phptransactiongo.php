<?php

$DEBUG = 0;

$params = array();


foreach ($_POST as $key => $value) {
	
	if($DEBUG == 1)echo "POST $key $value <br>";
	
	$params[$key] = $value;
}

$seed = date('c');

if($DEBUG == 1)echo "seed $seed<br>";

$tranKey = '024h1IlD';
$login = '6dd490faf9cb87a9862245da41170ff2';
$hashString = sha1( $seed
. $tranKey , false );


ini_set('soap.wsdl_cache_enable', '0');

if($DEBUG == 1)echo "bf create client<br>";

$client = new SoapClient('https://test.placetopay.com/soap/pse/?wsdl');

if($DEBUG == 1)echo "bf set params<br>";


$auth = array('login' => $login, 'tranKey' => $hashString, 'seed' => $seed, 'aditional'=>array('item' => array('name' => 'Juan Diego','value' => '0')));

$transaction = array();
$person = array();

foreach ($params as $key => $value) {
	if(strpos($key,"_person")!==false){

		if($DEBUG == 1)echo " person $key <br>";
		
		$person[str_replace("_person", "", $key)] = $value;	

	}
	else{
		
		if($DEBUG == 1)echo " transaction $key <br>";
		
		$transaction[$key] = $value;
		
	}

}

$transaction['payer']=$person;
$transaction['buyer']=$person;
$transaction['shipping']=$person;
$transaction['reference'] = $transaction['ipAddress'].",".$person['documentType'].",".$person['document']."<br>";

$data ="";

if($DEBUG == 1)echo "bf create transaction <br>";

if($DEBUG == 1)echo "<br> **********auth ".json_encode($auth)."<br>";

if($DEBUG == 1)echo "<br> **********person ".json_encode($person)."<br>";

if($DEBUG == 1)echo "<br> **********transaction ".json_encode($transaction)."<br>";


if($DEBUG == 1)echo "<br> **********final ".json_encode(array('auth' => $auth,'transaction' => $transaction))."<br>";

$data = $client->createTransaction(array('auth' => $auth,'transaction' => $transaction));

if($DEBUG == 1)echo "data <br>";

if($DEBUG == 1)echo json_encode($data);

$id = $data->createTransactionResult->transactionID;
$state = $data->createTransactionResult->returnCode;
$totalAmount = $params['totalAmount'];
$tip = $params['tipAmount'];
$totalAmount = $params['totalAmount'];
$bankCode = $params['bankCode'];

if($DEBUG == 1)exit();

header('Location: testpay.php?transaction='.$id.'&state='.$state."&totalAmount=".$totalAmount."&tip=".$tip);


?>