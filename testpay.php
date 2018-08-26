<?php

$DEBUG = 0	;

if($DEBUG == 1)error_reporting(E_ALL);

$seed = date('c');
$tranKey = '024h1IlD';
$login = '6dd490faf9cb87a9862245da41170ff2';
$hashString = sha1( $seed
. $tranKey , false );

ini_set('soap.wsdl_cache_enable', '0');

$client = new SoapClient('https://test.placetopay.com/soap/pse/?wsdl');

$data ="";


if(function_exists('apcu_fetch')){
	if($DEBUG == 1)echo "enter to apcu <br>";
	$data = apcu_fetch("bancos");
	if($DEBUG == 1)echo "data ".json_encode($data)."<br>";


	if ( $data !== false ){

		if(date('Y-m-d H:i:s', strtotime(apcu_fetch("date") . ' +1 day'))<date('Ymd H:m:s')){
			if($DEBUG == 1)echo "enter to cache banks<br>";
		}
		else{
			if($DEBUG == 1)echo "enter to expired cache banks<br>";
			$data = $client->getBankList(array('auth'=>array('login' => $login, 'tranKey' => $hashString, 'seed' => $seed, 'aditional'=>array('item' => array('name' => 'Juan Diego','value' => '0')))));
			apcu_clear_cache();
			apcu_add('bancos', $data);
			apcu_add('date', date('Ymd H:m:s'));
		}
	}
	else{

		if($DEBUG == 1)echo "enter empty cache banks<br>";

		$data = $client->getBankList(array('auth'=>array('login' => $login, 'tranKey' => $hashString, 'seed' => $seed, 'aditional'=>array('item' => array('name' => 'Juan Diego','value' => '0')))));
		apcu_clear_cache();
		apcu_add('bancos', $data);
		apcu_add('date', date('Ymd H:m:s'));


	}
}
else{
	if($DEBUG == 1)echo "enter to no apcu<br>";


	if ( isset($_COOKIE['bancos']) ){

		if(date('Y-m-d H:i:s', strtotime($_COOKIE["date"] . ' +1 day'))<date('Ymd H:m:s')){
			if($DEBUG == 1)echo "enter to cookies banks<br>";
			$data = json_decode($_COOKIE['bancos']);
		}
		else{
			if($DEBUG == 1)echo "enter to expired cookies banks<br>";
			$data = $client->getBankList(array('auth'=>array('login' => $login, 'tranKey' => $hashString, 'seed' => $seed, 'aditional'=>array('item' => array('name' => 'Juan Diego','value' => '0')))));
			setcookie("bancos", json_encode($data));
			setcookie("date", date('Ymd H:m:s'));
			
		}
	}
	else{

		if($DEBUG == 1)echo "enter empty cookies banks<br>";

		$data = $client->getBankList(array('auth'=>array('login' => $login, 'tranKey' => $hashString, 'seed' => $seed, 'aditional'=>array('item' => array('name' => 'Juan Diego','value' => '0')))));
		setcookie("bancos", json_encode($data));
		setcookie("date", date('Ymd H:m:s'));

	}

}


$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$ipaddress = '127.0.0.1';


$transaction_state = "";
$transaction_id = "";

if(isset($_GET['transaction'])){

	if ($DEBUG == 1) echo json_encode($_GET) ."<br>";
	$transaction_id = $_GET['transaction'];
	$transaction_state = $_GET['state'];
	$transaction_amount = $_GET['totalAmount'];
	$transaction_tip = $_GET['tip'];

	if(isset($_COOKIE["list"])){
		if($DEBUG == 1)echo "enter to cookie list<br>";
		$transaction_list=$_COOKIE["list"];
		$transaction_list = json_decode($transaction_list);
	}
	else{
		if($DEBUG == 1)echo "enter to new list<br>";
		$transaction_list = array();
	}

	if($transaction_state == 'SUCCESS')$transaction_state="Exitosa";
	else $transaction_state = "No Exitosa";

	if(!in_array(array($transaction_id,$transaction_state,$transaction_amount,$transaction_tip) , $transaction_list))array_push($transaction_list, array($transaction_id,$transaction_state,$transaction_amount,$transaction_tip));

	


	$transaction_list = json_encode($transaction_list);
	setcookie("list", $transaction_list);
}
else{
	if($DEBUG == 1)echo "enter to clean cookie list<br>";
	setcookie("list", null);
	$transaction_list = "";

}

if ($DEBUG == 1)echo "transaction_list ".$transaction_list."<br>";

?>
<!DOCTYPE html>
<html>
<head>
	<title>
		Transacción de prueba
	</title>
</head>
<style type="text/css">
	input[type="text"]{
		opacity: 0.7;
	}
	select{
		opacity: 0.7;
	}
	label{
		color: white;
		font-size: 18pt;
	}
</style>
<body style="width: 90%;height: 100%; background-position: center;text-align: center;vertical-align: middle;background: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url('images/moscow.jpg');background-repeat: no-repeat;background-size: cover;">

	<div style="height: 10%;width:110%;text-align: center;background: white;opacity: 0.5;margin-top: 0%;padding-top: 1%;padding-bottom: 1%">
		<h1 style="color: #F25B4A">Bienvenido a su plataforma de pago feliz</h1>
	</div>

	<div style="z-index: 1;height: 100%">
		<form id = "form" method="POST" action = "phptransactiongo.php">

			<div style="height: 10%;width:110%;text-align: left;margin-top: 1%;background-color: #F25B4A;opacity: 0.8;text-align: center;">
				<label style="color: white;font-size: 18pt">Datos Persona</label>
			</div>
			<div style="height: 50%;width: 90%;margin-top: 1%;margin-left: 5%">

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right" >Tipo de documento :</label>
					</div>
					<div style="width:25%;float:left">
						<select id = "documentType_person" name = "documentType_person" style="width: 64%">
							
							<option>CC</option>
							<option>CE</option>
							<option>TI</option>

							<option>PPN</option>
							<option>NIT</option>
							<option>SSN</option>
						</select>
					</div>
					<div style="width:25%;float:left">
						<label style="float:right" >Número :</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="document_person" id="document_person"/>
					</div>
				</div>
				<br>
				<br>

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right" >Nombre:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="firstName_person" id="firstName_person" />
					</div>
					<div style="width:25%;float:left">
						<label style="float:right" >Apellido:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="lastName_person" id="lastName_person" />
					</div>
				</div>
				<br>
				<br>

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right" >E-mail:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="emailAddress_person" id="emailAddress_person" />
					</div>
					<div style="width:25%;float:left">
						<label style="float:right" >Compañía donde trabaja:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="company_person" id="company_person" />
					</div>
				</div>
				<br>
				<br>

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right" >Ciudad:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="city_person" id="city_person" />
					</div>
					<div style="width:25%;float:left">
						<label style="float:right" >Dirección:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="address_person" id="address_person" />
					</div>
				</div>
				<br>
				<br>

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right" >Provincia/Departamento:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="province_person" id="province_person" />
					</div>
					<div style="width:25%;float:left">
						<label style="float:right" >Código postal:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="postalCode_person" id="postalCode_person" />
					</div>
				</div>
				<br>
				<br>

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right" >Teléfono:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="phone_person" id="phone_person" />
					</div>
					<div style="width:25%;float:left">
						<label style="float:right" >Celular:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="mobile_person" id="mobile_person" />
					</div>
				</div>
			
			
			</div>

			<div style="height: 10%;width:110%;text-align: left;margin-top: 9.3%;background-color: #F25B4A;opacity: 0.8;text-align: center;">
				<label style="color: white;font-size: 18pt">Datos Transacción</label>
			</div>

			<div style="height: 40%;width: 90%;margin: 2% 0% 0% 5%">

				<div style="width: 100%">
					<div style="width:25%;float:left">
						<label style="float:right">Banco:</label>
					</div>
					<div style="width:25%;float:left">
						<select id = "bankCode" name = "bankCode" id = "bankCode">
							<?php
								foreach ($data as $key => $value) {
									foreach ($value as $keyy => $valuee) {
										foreach ($valuee as $ky => $val) {
											echo '<option value = "'.$val->bankCode.'" >'.$val->bankName.'</option>';
										}
									}
								}
							?>
						</select>
					</div>
					<div style="width:25%;float:left">
						<label style="float:right">Tipo de interfaz:</label>
					
					</div>
					<div style="width:25%;float:left">
						<select id = "bankInterface" name = "bankInterface" id = "bankInterface" style="width:64%">
							<option value = "0">Persona</option>
							<option value = "1">Empresa</option>
						</select>
					</div>
				</div>
				<br>
				<br>

				<div style="width: 100%">
					<div style="width:25%;float:left;margin-top: -1%;">
						<label style="float:right">Valor a pagar:</label>
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="totalAmount" id="totalAmount"/>
					</div>
					<div style="width:25%;float:left;margin-top: -1%;">
						<label style="float:right">Valor propina:</label>
					
					</div>
					<div style="width:25%;float:left">
						<input type="text" name="tipAmount" id="tipAmount" value ="0"/>
					</div>
				</div>
			
				

			</div>

			<input type="hidden" name="description" value ="Transacción de prueba para placetopay"/>

			<input type="hidden" name="taxAmount" value = "0">
			<input type="hidden" name="devolutionBase" value = "0">
			<input type="hidden" name="language" value = "es">
			<input type="hidden" name="returnURL" value = "<?php echo $actual_link; ?>">
			<input type="hidden" name="userAgent" value = "AGENTE NAVEGADOR UTILIZADO POR EL CLIENTE">
			<input type="hidden" name="ipAddress" value = "<?php echo $ipaddress; ?>">
			<input type="hidden" name="currency" value = "COP">

			
		</form>

		<div style="height: 10%;width: 100%;margin: 4% 0% 0% 5%;text-align: center;">
			<input type="submit" name="enviar" value = "PAGAR" style="  border-radius: 15px;font-size:12pt;width: 20%;background-color:#F25B4A;color: white;height: 40px;border-width: 0px;cursor: pointer;" onclick="checkfields()">
		</div>

	</div>

</body>

<script type="text/javascript">
	console.log("inisica js");
	var transaction_state = "<?php echo $transaction_state; ?>";
	var transaction_id = "<?php echo $transaction_id; ?>";
	var list_transaction = '<?php echo $transaction_list; ?>';
	var list_string = "";
</script>

<script>

	

	
	

	if(list_transaction !=""){

		var list_string = "ID Transacción  |  Estado  |  Cantidad pagada  |  Cantidad propina\n\n";

		list_transaction = JSON.parse(list_transaction);

		for(k in list_transaction){
			list_string+= list_transaction[k][0]+ "  |  "+list_transaction[k][1]+"  |  $"+list_transaction[k][2]+"  |  $"+list_transaction[k][3]+"\n";
		}

	}

	console.log("transaction_id "+transaction_id);


	if(transaction_state != ""){
		if(list_string === "")
			alert("Ultima transacción: "+transaction_id+", estado: "+transaction_state);
		else
			alert("Ultima transacción: "+transaction_id+", estado: "+transaction_state+"\n\n"+list_string);
	}

	function checkfields(){

		var params = {};

		params ["document_person"] = document.getElementById("document_person");
		params ["firstName_person"] = document.getElementById("firstName_person");
		params ["lastName_person"] = document.getElementById("lastName_person");
		params ["emailAddress_person"] = document.getElementById("emailAddress_person");
		params ["company_person"] = document.getElementById("company_person");
		params ["city_person"] = document.getElementById("city_person");
		params ["address_person"] = document.getElementById("address_person");
		params ["province_person"] = document.getElementById("province_person");
		params ["postalCode_person"] = document.getElementById("postalCode_person");
		params ["phone_person"] = document.getElementById("phone_person");
		params ["mobile_person"] = document.getElementById("mobile_person");
		params ["totalAmount"] = document.getElementById("totalAmount");
		params ["tipAmount"] = document.getElementById("tipAmount");


		var cnt = 0;

		for(k in params){

			console.log(k);

			if(typeof params[k].value == 'undefined'){
				params[k].style = 'background-color:red;';
				cnt++;
			}
			else if(params[k].value == ""){
				params[k].style = 'background-color:red;';
				cnt++;
			}

			else if(k == "document_person" || k == "postalCode_person" || k == "phone_person" || k == "mobile_person" || k == "totalAmount" || k == "tipAmount"){

				if(isNaN(parseInt(params[k].value))){
					console.log("cathc");
					params[k].style = 'background-color:red;';
					alert("Revisar campo en rojo, su valor sólo puede ser numperico");
					return;
				}
				else{
					params[k].style = 'background-color:white;';

				}
				

			}
			else if(k == "emailAddress_person"){
				if((params[k].value.indexOf(".") == -1) && (params[k].value.indexOf("@") == -1)){
					params[k].style = 'background-color:red;';
					alert("Digite un correo valido");
					return;
				}
			}

			console.log(document.getElementById(k).value);

		}


		if(cnt == 0)document.getElementById('form').submit();
		else{
			alert(" Se encontraron "+cnt+" campos sin digitar, verifique los campos en rojo");
			return;
		} 

		if(document.getElementById("bankCode").options[document.getElementById("bankCode").selectedIndex].value == "0"){
			alert("Debe seleccionar un banco");
			return;
		}

	}
</script>
</html>
