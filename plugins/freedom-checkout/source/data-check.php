<?php

if (!defined('FC_DEBUG')) define('FC_DEBUG', true );
//define('STAGING', true);

session_start();
require_once('../../../../wp-load.php');
require_once('includes/freedom.class.php');
require_once('includes/freedom.autoship.class.php');
//include('../includes/config.php');
//


$request = FreedomDataCheck::get_request_vars();
if( isset($request['action'])){
	switch($request['action']){
		case 'login':
		FreedomDataCheck::checkLogin($request['username'], $request['password']);
		break;
		case 'usernametest':
		FreedomDataCheck::checkUsername($request['username']);
		break;
		case 'emailtest':
		FreedomDataCheck::checkEmail($request['email']);
		break;
		case 'ziptest':
		FreedomDataCheck::checkZipcode($request['zip']);
		break;
		case 'register':
		FreedomDataCheck::validate_registration($request);
		break;
		case 'update':
		FreedomDataCheck::validate_update($request);
		break;
		case 'createtemporder':
		FreedomDataCheck::create_temp_order();
		break;
		case 'createorder':
		FreedomDataCheck::create_temp_order();
		break;
		case 'addtocart':
		FreedomDataCheck::add_items_to_cart($request);
		break;
		case 'gettotals':
		FreedomDataCheck::get_totals($request);
		break;
		case 'cctest':
		FreedomDataCheck::validate_cc_number($request['cc']);
		break;
		case 'exptest':
		FreedomDataCheck::validate_cc_exp($request['month'], $request['year']);
		break;
		case 'cvctest':
		FreedomDataCheck::validate_cvc($request['cvc']);
		break;
		case 'addpayment':
		FreedomDataCheck::add_cc($request);
		break;
		case 'finalizeorder':
		FreedomDataCheck::create_order($request);
		break;
		case 'redirecttest':
		FreedomDataCheck::redirect_test();
		break;
		case 'shippingestimate':
		FreedomDataCheck::shipping_estimate($request);
		break;
		case 'neworder':
		FreedomDataCheck::new_order($request);
		break;
		case 'newsignuporder':
		FreedomDataCheck::new_signup_order($request);
		break;
		case 'resetorder':
		FreedomDataCheck::reset_order($request);
		break;
		case 'logerror':
		FreedomDataCheck::log_error($request);
		break;
		case 'check_sponsor_id':
		FreedomDataCheck::check_sponsor_id($request['sponsor_id']);
		break;
	}
}
class FreedomDataCheck{
	function log_error($request){
		$errorfile = "errorlog.txt"; 
		$fh = fopen($errorfile, 'a+');
		$errortext = "\r\n".date(DATE_ATOM).' : '.$request['DataResponse'];
		fwrite($fh, $errortext);
		fclose($fh);
	}
	static function check_sponsor_id( $sponsor_id ){
		$data = array('success'=> false);
		if (function_exists('get_sponsor_info')){
			$sponsor = get_sponsor_info($sponsor_id);
			if (1 == $sponsor['Success']){
				$userinfo = (array) $sponsor['UserInfo'];
				$data = array('success'=> true, 'message' => '', 'sponsor_id' => $sponsor['RepDID'], 'name' => $userinfo['FirstName'].' '.$userinfo['LastName']);
			}else{
				$data = array('success' => false, 'message' => 'Can\'t find referrer');
			}
		}else{
			$data = array('success' => false, 'message' => 'Can\'t find DSF replicated plugin');
		}
		self::returnJSON($data);
	}
	static function checkLogin($username, $password){
		$username = filter_var( trim($username), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$password = trim($password);
		$freedom = new Freedom();
		$data = array('success'=> false);
		if( !empty($username) && !empty($password)){
			$freedomdata = $freedom->LoginCheck_Rep($username,$password);
			if(!$freedomdata['Success']){
				$replookup = $freedom->GetRepInfo($username);
				if($replookup['Success'] == 1){
					$freedomdata = $freedom->LoginCheck_Rep($replookup['RepDID'],$password);
				}
			}
			if($freedomdata['Success']){
				$_SESSION['formfill'] = true;
				$user = $_SESSION['user'];
				$user['LoggedIn']  = true;
				$user['RepNumber'] = $freedomdata['RepNumber'];
				$user['Firstname'] = $freedomdata['Firstname'];
				$user['Lastname'] = $freedomdata['Lastname'];
				$user['Email'] = $freedomdata['Email'];
				$user['Phone1'] = $freedomdata['Phone1'];
				$user['BillStreet1'] = $freedomdata['BillStreet1'];
				$user['BillStreet2'] = $freedomdata['BillStreet2'];
				$user['BillCity'] = $freedomdata['BillCity'];
				$user['BillState'] = $freedomdata['BillState'];
				$user['BillPostalCode'] = $freedomdata['BillPostalCode'];
				$user['BillCountry'] = $freedomdata['BillCountry'];
				$user['ShipStreet1'] = $freedomdata['ShipStreet1'];
				$user['ShipStreet2'] = $freedomdata['ShipStreet2'];
				$user['ShipCity'] = $freedomdata['ShipCity'];
				$user['ShipState'] = $freedomdata['ShipState'];
				$user['ShipPostalCode'] = $freedomdata['ShipPostalCode'];
				$user['ShipCountry'] = $freedomdata['ShipCountry'];
				$_SESSION['user'] = $user;
				$data = array('success'=> true, 'user' => $user);
			}
		}
		self::returnJSON($data);
	}

	static function checkUsername($username){
		$freedom = new Freedom();
		$success = false;
		$message = '';
		$extrainfo = "";
		if($username == ''){
			$username = "No Username provided.";
		}else{
			$result = $freedom->CheckRepURL($username);
			if($result == 0){
				//check the repId here
				$result = $freedom->GetRepInfo($username);
				if ( isset($result['Success']) && $result['Success'] == 0 ) {
					$success = true;
				} else {
					$message = "Username not available.";	
					$extrainfo = "Used RepId as username ";
				}

			}else{
				$message = "Username already registered. \r\nPlease select a different username.";
			}
		}
		$data = array('success'=> $success, 'message' => $message, 'extrainfo' => $extrainfo);
		self::returnJSON($data);
	}

	static function checkEmail($email){
		$freedom = new Freedom();
		$success = false;
		$message = '';
		if($email == ''){
			$message = "No E-mail provided.";
		}else{
			$success = true;
			/*$result = $freedom->CheckEmail($email);
			if($result != 0){
				$message = "E-mail already registered. \r\nPlease login or contact Customer Service for additional assistance.".$success;
			}else{
				$success = true;
			}*/
		}
		$data = array('success'=> $success, 'message' => $message);
		self::returnJSON($data);
	}
	static function checkZipcode($zipcode){
		$freedom = new Freedom();
		$city = '';
		$state = '';
		$data = array('success'=> false);
		if($zipcode != ''){
			$result = $freedom->ZipLookup($zipcode);
			if (is_array($result)){
				$zip = $result['ZipBlock_v2'];
				if( !empty($zip['City']) || !empty($zip[0]['City'])){
					$success = true;
					$city = $result['ZipBlock_v2'];
					if( isset($zip[1]['City'])){
						$data = array('success'=> $success, 'city-state' => $zip);
					}else{
						$city = $zip['City'];
						$state = $zip['State'];
						$data = array('success'=> $success, 'city' => $city, 'state' => $state);
					}
				}
			}
		}
		self::returnJSON($data);
	}

	function validate_registration($request){
		$_SESSION['user']['SponsorRepNumber'] = $request['SponsorRepNumber'];
		$_SESSION['user']['Firstname'] = $request['Firstname'];
		$_SESSION['user']['Lastname'] = $request['Lastname'];
		$_SESSION['user']['Username'] = $request['Username'];
		$_SESSION['user']['Email'] = $request['Email'];
	//$password1 = $request['Password1'];
	//$pasword2 = $request['Password2'];
		$tempdata['Password'] = $request['Password1'];
		$_SESSION['user']['Phone1'] = $request['Phone1'];
		$_SESSION['user']['BillStreet1'] = $request['BillStreet1'];
		$_SESSION['user']['BillStreet2'] = $request['BillStreet2'];
		$_SESSION['user']['BillCity'] = $request['BillCity'];
		$_SESSION['user']['BillState'] = $request['BillState'];
		$_SESSION['user']['BillPostalCode'] = $request['BillPostalCode'];
		$_SESSION['user']['BillCountry'] = $request['BillCountry'];
		$_SESSION['user']['ShipStreet1'] = (!$request['SameShipping']) ? $request['ShipStreet1'] : $request['BillStreet1'];
		$_SESSION['user']['ShipStreet2'] = (!$request['SameShipping']) ? $request['ShipStreet2'] : $request['BillStreet2'];
		$_SESSION['user']['ShipCity'] = (!$request['SameShipping']) ? $request['ShipCity'] : $request['BillCity'];
		$_SESSION['user']['ShipState'] = (!$request['SameShipping']) ? $request['ShipState'] : $request['BillState'];
		$_SESSION['user']['ShipPostalCode'] = (!$request['SameShipping']) ? $request['ShipPostalCode'] : $request['BillPostalCode'];
		$_SESSION['user']['ShipCountry'] = (!$request['SameShipping']) ? $request['ShipCountry'] : $request['BillCountry'];
		$_SESSION['user']['Terms'] = $request['Terms'];
		self::create_temp_user($tempdata);
	}

	function validate_update($request, $signup = false){
		$user = $_SESSION['user'];
		if($signup==false) {
			$user['RepNumber'] = self::compare_update_user_info($user['RepNumber'], $request['RepNumber']);
		}
		$user['Firstname'] = self::compare_update_user_info($user['Firstname'], $request['Firstname']);
		$user['Lastname'] = self::compare_update_user_info($user['Lastname'], $request['Lastname']);
		$user['Phone1'] = self::compare_update_user_info($user['Phone1'], $request['Phone1']);
		$user['BillStreet1'] = self::compare_update_user_info($user['BillStreet1'], $request['BillStreet1']);
		$user['BillStreet2'] = self::compare_update_user_info($user['BillStreet2'], $request['BillStreet2']);
		$user['BillCity'] = self::compare_update_user_info($user['BillCity'], $request['BillCity']);
		$user['BillState'] = self::compare_update_user_info($user['BillState'], $request['BillState']);
		$user['BillPostalCode'] = self::compare_update_user_info($user['BillPostalCode'], $request['BillPostalCode']);
		$user['BillCountry'] = self::compare_update_user_info($user['BillCountry'], $request['BillCountry']);
		$user['SameShip'] = $request['SameShipping'];
		$sameship = ($request['SameShipping']==1) ? true : false;
		if(!$sameship){
			$user['ShipStreet1'] = self::compare_update_user_info($user['ShipStreet1'], $request['ShipStreet1']);
			$user['ShipStreet2'] = self::compare_update_user_info($user['ShipStreet2'], $request['ShipStreet2']);
			$user['ShipCity'] = self::compare_update_user_info($user['ShipCity'], $request['ShipCity']);
			$user['ShipState'] = self::compare_update_user_info($user['ShipState'], $request['ShipState']);
			$user['ShipPostalCode'] = self::compare_update_user_info($user['ShipPostalCode'], $request['ShipPostalCode']);
			$user['ShipCountry'] = self::compare_update_user_info($user['ShipCountry'], $request['ShipCountry']);
		}else{
			$user['ShipStreet1'] = $user['BillStreet1'];
			$user['ShipStreet2'] = $user['BillStreet2'];
			$user['ShipCity'] = $user['BillCity'];
			$user['ShipState'] = $user['BillState'];
			$user['ShipPostalCode'] = $user['BillPostalCode'];
			$user['ShipCountry'] = $user['BillCountry'];
		}
		$user['Terms'] = $request['Terms'];
		$_SESSION['user'] = $user;
		if($user['Terms'] == 1){
			self::create_temp_order();
		}else{
			$data = array('success' => false, 'message' => 'Error validating order information.');
			self::returnJSON($data);
		}
	}

	function compare_update_user_info($input1, $input2){
	//$input1  = trim_input(urldecode($input1));
	//$input2 = trim_input(urldecode($input2));
		if($input1 != $input2) return $input2;
		else return $input1;
	}

	function trim_input($input){
		$trimmed = trim($input, '\"');
		return $trimmed;
	}

	function create_temp_user($tempdata){
		$freedom = new Freedom();
		$freedomdata = $freedom->CreateOnlineSignUp($_SESSION['user'], $tempdata);
		if( ! empty($freedomdata)){
			$_SESSION['user']['SignUpID'] = $freedomdata;
			self::create_user();
		}
		else {
			$data = array('success' => false, 'message' => 'Sign up failed. Missing user information.');
			self::returnJSON($data);
		}
	}

	function create_user(){
		$freedom = new Freedom();
		$repnumber = $freedom->CreateRep($_SESSION['user']['SignUpID']);
		if( is_numeric($repnumber) && $repnumber > 0){
			$freedom->GenerateCustomerAR($repnumber);
			$_SESSION['formfill'] = true;
			$_SESSION['user']['RepNumber'] = $repnumber;
			$data = array('success' => true, 'userid' => $repnumber);
		}else{
			$data = array('success' => false);
		}
		self::returnJSON($data);
	}

	function create_temp_order(){
		$freedom = new Freedom();
		$tempordernumber = $freedom->CreateOnlineOrder($_SESSION['user']);
		if( is_numeric($tempordernumber) && $tempordernumber > 0){
			$_SESSION['cart']['tempordernumber'] = $tempordernumber;
			$_SESSION['user']['LoggedIn']  = true;
			$data = array('success' => true, 'tempordernumber' => $tempordernumber);
		}else{
			$data = array('success' => false, 'message' => $tempordernumber);
		}
		self::returnJSON($data);
	}

	function add_items_to_cart($request){
		$freedom = new Freedom();
		$items = json_decode($request['sorteditems'], TRUE);
		$tempordernumber = $request['tempordernumber'];
		foreach($items as $itemid => $qty){
			$freedomdata = $freedom->OnlineOrder_AddItem($tempordernumber, $itemid, $qty);
			if( is_numeric($freedomdata) && $freedomdata > 0 && !$itemsincart) $itemsincart = true;
		}
		if( isset($itemsincart)){
			self::set_ship_method_default($tempordernumber);
		}else{
			$data = array('success' => false, 'message' => 'Cannot add items to order.', 'items' => $items);
			self::returnJSON($data);
		}
	}

	function set_ship_method_default($tempordernumber){
		$freedom = new Freedom();
		$freedomdata = $freedom->OnlineOrder_GetShipMethods_v2($tempordernumber);
		if( ! empty($freedomdata)){
			$shipmethod = (isset($freedomdata['ShipMethods_v2']['0']['ShipMethodID'])) ? $freedomdata['ShipMethods_v2'][0] : $freedomdata['ShipMethods_v2'];
			$_SESSION['cart']['shipmethod'] = $shipmethod['Description'];
			$_SESSION['cart']['shipmethodid'] = $shipmethod['ShipMethodID'];
			$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($tempordernumber, $shipmethod['ShipMethodID']);
		}else{
			$_SESSION['cart']['shipmethod'] = 'US Mail';
			$_SESSION['cart']['shipmethodid'] = 1012;
			$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($tempordernumber, 1012);	
		}
		if( isset($updateshipmethodid) && is_numeric($updateshipmethodid) && $updateshipmethodid > 0){
			$data = array('success' => true, 'shipmethod' => $_SESSION['cart']['shipmethod'], 'shipmethodid' => $_SESSION['cart']['shipmethodid']);
		}else{
			$data = array('success' => false, 'message' => 'Cannot set default shipping method.');
		}
		self::returnJSON($data);
	}

	function get_totals($request){
		$freedom = new Freedom();
		$freedomdata = $freedom->OnlineOrder_GetTotals($request['tempordernumber']);
		if( ! empty($freedomdata)){
			$_SESSION['cart']['shiptotal'] = number_format($freedomdata['ShippingTotal'], 2, '.', ',');
			$_SESSION['cart']['taxtotal'] = number_format($freedomdata['TaxTotal'], 2, '.', ',');
			$_SESSION['cart']['total'] = number_format($freedomdata['OrderTotal'], 2, '.', ',');
			$data = array('success' => true, 'shiptotal' => $_SESSION['cart']['shiptotal'], 'taxtotal' => $_SESSION['cart']['taxtotal'], 'total' => $_SESSION['cart']['total']);
		}else{
			$data = array('success' => false);
		}
		self::returnJSON($data);
	}

	function validate_cc_number($ccnumber){
		$freedom = new Freedom();
		$success = true;
		$message = '';
		$cctest = $freedom->ValidateCCNumber($ccnumber);
		if(!$cctest) $success = false;
		$data = array('success' => $success);
		self::returnJSON($data);
	}

	function validate_cc_exp($ccmonth, $ccyear){
		$month = date('m',time());
		$year = date('y',time());
		$success = true;
		$message = '';
		if($ccyear == $year && $ccmonth < $month) $success = false;
		elseif($ccyear < $year) $success = false;
		$data = array('success' => $success);
		self::returnJSON($data);
	}

	function validate_cvc($cvc){
		$success = (preg_match('/(^\d{3}$)|(^\d{4}$)/', $cvv)) ? true : false;
		$data = array('success' => $success);
		self::returnJSON($data);
	}

	function add_cc($request){
		$freedom = new Freedom();
		$totals = $freedom->OnlineOrder_GetTotals($request['tempordernumber']);
		$freedomdata = $freedom->Payment_CreditCard($request['tempordernumber'],$request['Firstname'],$request['Lastname'],$request['BillStreet1'],$request['BillPostalCode'],$request['month'], $request['year'],$request['cvc'],'',$request['cc'],$totals['OrderTotal']);
		if($freedomdata['TransactionCompleted'] == true){
			$data = array('success' => true);
		}else{
			$data = array('success' => false, 'message' => $freedomdata['Description']);
		}
		self::returnJSON($data);
	}

	function create_order($request){
		$freedom = new Freedom();
		$freedomdata = $freedom->CreateOrder($request['tempordernumber']);
		if(is_numeric($freedomdata['OrderID']) && $freedomdata['OrderID'] > 0){
			$_SESSION['cart']['ordernumber'] = $freedomdata['OrderID'];
			$freedom->GenerateOrderAR($freedomdata['OrderID']);
			$data = array('success' => true, 'orderid' => $freedomdata['OrderID']);
		}else{
			$data = array('success' => false, 'message' => $freedomdata['ErrorMsg']);
		}
		self::returnJSON($data);
	}

	function shipping_estimate($request){
		$freedom = new Freedom();
		$request['RepNumber'] = '9999';
		$request['BillStreet1'] = '2400 Boswell Rd';
		$request['BillStreet2'] = '';
		$request['BillCity'] = 'CHULA VISTA';
		$request['BillState'] = 'CA';
		$request['BillPostalCode'] = '91914';
		$request['BillCountry'] = 'USA';
		$request['Firstname'] = 'Shipping';
		$request['Lastname'] = 'Estimate';
		$request['ShipStreet2'] = '';
		$request['Phone1'] = '555-555-5555';
		$request['Email'] = 'no@email.com';
		$request['OnlineOrderID'] = $freedom->CreateOnlineOrder($request);
		if( is_numeric($request['OnlineOrderID']) && $request['OnlineOrderID'] > 0){
			$items = json_decode($request['Cart'], TRUE);
			foreach($items as $itemid => $qty){
				$additemresult = $freedom->OnlineOrder_AddItem($request['OnlineOrderID'], $itemid, $qty);
				if( is_numeric($additemresult) && $additemresult > 0 && !$itemsincart) $itemsincart = true;
			}
			if( isset($itemsincart)){
				if($request['EnrollmentType'] == 'customer') {
					//$freedom->OnlineOrder_AddItem($request['OnlineOrderID'], 90102, 1);
				}else {
					if($request['EnrollmentItem'] == false) {
						$freedom->OnlineOrder_AddItem($request['OnlineOrderID'], 90101, 1);
					}
				}
				$freedomdata = $freedom->OnlineOrder_GetShipMethods_v2($request['OnlineOrderID']);
				if( ! empty($freedomdata)){
					$shipmethod = (isset($freedomdata['ShipMethods_v2']['0']['ShipMethodID'])) ? $freedomdata['ShipMethods_v2'][0] : $freedomdata['ShipMethods_v2'];
					$request['Description'] = $shipmethod['Description'];
					$request['ShipMethodID'] = $shipmethod['ShipMethodID'];
					$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($request['OnlineOrderID'], $shipmethod['ShipMethodID']);
				}else{
					$request['Description'] = 'US Mail';
					$request['ShipMethodID'] = 1012;
					$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($request['OnlineOrderID'], 1012);	
				}
				if( isset($updateshipmethodid) && is_numeric($updateshipmethodid) && $updateshipmethodid > 0){
					$totals = $freedom->OnlineOrder_GetTotals($request['OnlineOrderID']);
					if( ! empty($totals)){
						$request['ShippingTotal'] = number_format($totals['ShippingTotal'], 2, '.', ',');
						$request['TaxTotal'] = number_format($totals['TaxTotal'], 2, '.', ',');
						$request['OrderTotal'] = number_format($totals['OrderTotal'], 2, '.', ',');
						$data = array('success' => true, 'info' => $request);
					}else{
						$data = array('success' => false, 'message' => 'Could not calculate totals.');
					}
				}else{
					$data = array('success' => false, 'message' => 'Cannot set shipping method.');
				}
			}else{
				$data = array('success' => false, 'message' => 'Cannot add items to order.');
			}
		}else{
			$data = array('success' => false, 'message' => 'Could not create cart.');
		}
		self::returnJSON($data);	
	}

	function new_order($request){
		$request['DatabaseID'] = self::saveInitialRequest($request);
		$freedom = new Freedom();
		self::saveSignupDetails($request['DatabaseID'], $request['RepNumber'], 'YGYID');
		$request['OnlineOrderID'] = $freedom->CreateOnlineOrder($request);
		if( is_numeric($request['OnlineOrderID']) && $request['OnlineOrderID'] > 0){
			self::saveOrderDetails($request['DatabaseID'], $request['OnlineOrderID'], 'OnlineOrderID');
			$items = json_decode($request['Cart'], TRUE);
			$asitems = json_decode($request['ASItems'], TRUE);
			foreach($items as $itemid => $qty){
				$additemresult = $freedom->OnlineOrder_AddItem($request['OnlineOrderID'], $itemid, $qty);
				if( is_numeric($additemresult) && $additemresult > 0 && !$itemsincart) $itemsincart = true;
			}	
			if(!empty($asitems)){
				$noautoship = false;
				$freedomas = new FreedomAS();
				$asprofiles = $freedomas->GetAutoshipProfiles($request['RepNumber']);
				if($asprofiles['AutoshipProfiles']['Success']==1 && $asprofiles['AutoshipProfiles']['Message']=='No AutoShip Items Found' && $asprofiles['AutoshipProfiles']['ProfileID'] == 0){
					$noautoship = true;
				}else if($asprofiles['AutoshipProfiles']['Success']==1 && is_numeric($asprofiles['AutoshipProfiles']['ProfileID']) && $asprofiles['AutoshipProfiles']['ProfileID'] > 0){
					$profileid = $asprofiles['AutoshipProfiles']['ProfileID'];
					$asprofiledetails = $freedomas->GetProfileItemDetails($profileid);
					foreach($asitems as $item){
						$additem = $freedomas->AddItem($profileid, $item['sku'], $item['qty']);
					}
				}
				if($noautoship){
					foreach($asitems as $item){
						$additemresult = $freedom->OnlineOrder_Autoship_AddItem($request['OnlineOrderID'], $item['sku'], $item['qty']);
					}
					$autoshipsetdateresult = $freedom->OnlineOrder_Autoship_SetDate($request['OnlineOrderID'], $request['ASDate']);
				}
			}
			if( isset($itemsincart)){
				$freedomdata = $freedom->OnlineOrder_GetShipMethods_v2($request['OnlineOrderID']);
				if( ! empty($freedomdata)){
					$shipmethod = (isset($freedomdata['ShipMethods_v2']['0']['ShipMethodID'])) ? $freedomdata['ShipMethods_v2'][0] : $freedomdata['ShipMethods_v2'];
					$request['Description'] = $shipmethod['Description'];
					$request['ShipMethodID'] = $shipmethod['ShipMethodID'];
					$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($request['OnlineOrderID'], $shipmethod['ShipMethodID']);
				}else{
					$request['Description'] = 'US Mail';
					$request['ShipMethodID'] = 1012;
					$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($request['OnlineOrderID'], 1012);	
				}
				if( isset($updateshipmethodid) && is_numeric($updateshipmethodid) && $updateshipmethodid > 0){
					$totals = $freedom->OnlineOrder_GetTotals($request['OnlineOrderID']);
					if( ! empty($totals)){
						$request['ShippingTotal'] = number_format($totals['ShippingTotal'], 2, '.', ',');
						$request['TaxTotal'] = number_format($totals['TaxTotal'], 2, '.', ',');
						$request['OrderTotal'] = number_format($totals['OrderTotal'], 2, '.', ',');
						$payment = $freedom->Payment_CreditCard($request['OnlineOrderID'],$request['Firstname'],$request['Lastname'],$request['BillStreet1'],$request['BillPostalCode'],$request['ExpDateMonth'], $request['ExpDateYear'],$request['CVV'],'',$request['CreditCardNumber'],$totals['OrderTotal']);
						self::savePaymentInfo($request['DatabaseID'], $payment);
						if($payment['TransactionCompleted'] === 'true'){
							$orderdata = $freedom->CreateOrder($request['OnlineOrderID'], $request['SignUpID']);
							if(is_numeric($orderdata['OrderID']) && $orderdata['OrderID'] > 0){
								$request['OrderID'] = $orderdata['OrderID'];
								self::saveOrderDetails($request['DatabaseID'], $request['OrderID'], 'OrderID');
								$freedom->GenerateOrderAR($request['OrderID']);
								self::checkSaveCompleted($request['DatabaseID']);
								$data = array('success' => true, 'info' => $request);
							}else{
								$data = array('success' => false, 'message' => $freedomdata['ErrorMsg']);
							}
						}else{
							if($payment['Description'] = 'This+transaction+has+been+declined.') $msg = 'Credit Card has been declined.';
							$data = array('success' => false, 'message' => $msg);
						}
					}else{
						$data = array('success' => false, 'message' => 'Could not calculate totals.');
					}
				}else{
					$data = array('success' => false, 'message' => 'Cannot set shipping method.');
				}
			}else{
				$data = array('success' => false, 'message' => 'Cannot add items to order.');
			}
		}else{
			$data = array('success' => false, 'message' => 'Could not create order.');
		}
		if (!$data['success'] && function_exists('error_handler')){
			//send error to raygun
			error_handler('100', $data['message'], __FILE__,  __LINE__);
		}
		self::returnJSON($data);
	}


	static function new_signup_order($request){
		//die("here"); 
		$request['DatabaseID'] = self::saveInitialRequest($request);
		$freedom = new Freedom();

		$request['SignUpID'] = $freedom->CreateOnlineSignUp($request,'distributor');
		self::saveSignupDetails($request['DatabaseID'], $request['SignUpID'], 'SignupID');
		if( ! empty($request['SignUpID']) && is_numeric($request['SignUpID']) && $request['SignUpID'] > 0){
			$request['OnlineOrderID'] = $freedom->CreateOnlineOrderSignup($request);
			if( is_numeric($request['OnlineOrderID']) && $request['OnlineOrderID'] > 0){
				self::saveOrderDetails($request['DatabaseID'], $request['OnlineOrderID'], 'OnlineOrderID');
				$items = json_decode($request['Cart'], TRUE);
				$asitems = json_decode($request['ASItems'], TRUE);
				$itemsincart = false;
				foreach($items as $itemid => $qty){
					$additemresult = $freedom->OnlineOrder_AddItem($request['OnlineOrderID'], $itemid, $qty);
					if( is_numeric($additemresult) && $additemresult > 0 && !$itemsincart) $itemsincart = true;
				}
				if(!empty($asitems)){
					foreach($asitems as $item){
						$additemresult = $freedom->OnlineOrder_Autoship_AddItem($request['OnlineOrderID'], $item['sku'], $item['qty']);
					}
					$autoshipsetdateresult = $freedom->OnlineOrder_Autoship_SetDate($request['OnlineOrderID'], $request['ASDate']);
				}
				if( isset($itemsincart)){
					if($request['EnrollmentType'] == 'customer') {
						//$freedom->OnlineOrder_AddItem($request['OnlineOrderID'], 90102, 1);
					}else {
						if($request['EnrollmentItem'] == false) {
							$freedom->OnlineOrder_AddItem($request['OnlineOrderID'], 90101, 1);
						}
					}
					$freedomdata = $freedom->OnlineOrder_GetShipMethods_v2($request['OnlineOrderID']);

					if( ! empty($freedomdata)){
						$shipmethod = (isset($freedomdata['ShipMethods_v2']['0']['ShipMethodID'])) ? $freedomdata['ShipMethods_v2'][0] : $freedomdata['ShipMethods_v2'];
						$request['Description'] = $shipmethod['Description'];
						$request['ShipMethodID'] = $shipmethod['ShipMethodID'];
						$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($request['OnlineOrderID'], $shipmethod['ShipMethodID']);
					}else{
						$request['Description'] = 'US Mail';
						$request['ShipMethodID'] = 1012;
						$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($request['OnlineOrderID'], 1012);	
					}
					if( isset($updateshipmethodid) && is_numeric($updateshipmethodid) && $updateshipmethodid > 0){
						$totals = $freedom->OnlineOrder_GetTotals($request['OnlineOrderID']);	
						if( ! empty($totals)){
							$fc = FreedomCheckout::instance();
							$request['ShippingTotal'] = number_format($totals['ShippingTotal'], 2, '.', ',');
							$request['TaxTotal'] = number_format($totals['TaxTotal'], 2, '.', ',');
							$request['OrderTotal'] = number_format($totals['OrderTotal'], 2, '.', ',');
							$request['CompanyName'] = isset($request['CompanyName']) ? $request['CompanyName'] : null;
							$request['TaxId'] = isset($request['TaxId']) ? $request['TaxId'] : null;
							$payment = $freedom->Payment_CreditCard_Test($request['OnlineOrderID'],$request['Firstname'],$request['Lastname'],$request['BillStreet1'],$request['BillPostalCode'],$request['ExpDateMonth'], $request['ExpDateYear'],$request['CVV'],'',$request['CreditCardNumber'], $totals['OrderTotal'], $request['TaxId'], $request['CompanyName']);
							self::savePaymentInfo($request['DatabaseID'], $payment);
							//var_dump($request['OnlineOrderID'],$request['Firstname'],$request['Lastname'],$request['BillStreet1'],$request['BillPostalCode'],$request['ExpDateMonth'], $request['ExpDateYear'],$request['CVV'],'',$request['CreditCardNumber'], $totals['OrderTotal']); 
							//var_dump($payment); 
							if($payment['TransactionCompleted'] === 'true'){
								$request['RepNumber'] = $freedom->CreateRep($request['SignUpID']);
								if( $request['RepNumber']){
									self::saveSignupDetails($request['DatabaseID'], $request['RepNumber'], 'YGYID');
									$freedom->GenerateRepAR($request['RepNumber']);
									$orderdata = $freedom->CreateOrder($request['OnlineOrderID'], $request['SignUpID']);
									if(is_numeric($orderdata['OrderID']) && $orderdata['OrderID'] > 0){
										$request['OrderID'] = $orderdata['OrderID'];
										self::saveOrderDetails($request['DatabaseID'], $request['OrderID'], 'OrderID');
										$freedom->GenerateOrderAR($request['OrderID']);
										self::checkSaveCompleted($request['DatabaseID']);
										$fc->sendWelcomeEmail($request);
										$data = array('success' => true, 'info' => $request);
									}else{
										$data = array('success' => false, 'message' => $orderdata['ErrorMsg']);
									}
								}else{
									$data = array('success' => false, 'message' => 'Cannot create account.');
								}

							}else{
								if (FC_DEBUG){
									$foo = array('ShippingTotal' => 123, 'TaxTotal' => 123, 'OrderTotal' => '123', 'Description' => 'test descr', 'OrderID' => 123, 'RepNumber' => 123 );
									$data = array('success' => true, 'info' => $foo);
									$fc->sendWelcomeEmail($request);
								}else{
									$msg = 'Credit Card has been declined.';
									$data = array('success' => false, 'message' => $msg);
								}
							}
						}else{
							$data = array('success' => false, 'message' => 'Could not calculate totals.');
						}
					}else{
						$data = array('success' => false, 'message' => 'Cannot set shipping method.');
					}
				}else{
					$data = array('success' => false, 'message' => 'Cannot add items to order.');
				}
			}else{
				$data = array('success' => false, 'message' => 'Could not create order.');
			}
		}
		else {
			$data = array('success' => false, 'message' => 'Sign up failed. Agent ID you entered is not exists.');
		}
		if (!$data['success'] && function_exists('error_handler')){
			//send error to raygun
			error_handler('100', $data['message'], __FILE__,  __LINE__);
		}
		self::returnJSON($data);
	}

	function reset_order(){
		session_destroy();
	}

	static function saveInitialRequest($data){
		return;
		global $table_prefix;
		$db = self::initDB();
		$info = $data;
		unset($info['action']);
		unset($info['CreditCardNumber']);
		unset($info['ExpDateMonth']);
		unset($info['ExpDateYear']);
		unset($info['CVV']);
		unset($info['Password']);
		unset($info['TaxID']);
		$info = serialize($info);

		try {
			$stmt = $db->prepare("INSERT INTO `Order`(`UserData`) VALUES (:userdata)");
			$stmt->bindValue(':userdata', $info, PDO::PARAM_STR);
			$stmt->execute();
		} catch (PDOException $e) {
			echo $e->getCode().'\n\r DB Error, could not save to the database. Save Initial Request';
		}

		return $db->lastInsertId();
		$db = NULL;
	}

	static function saveSignupDetails($databaseid, $userid, $type){
		return;
		$db = self::initDB();
		try {
			if($type == 'SignupID'){
				$stmt = $db->prepare("UPDATE `Order` SET `SignupID`=:signupid WHERE `ID`=:id");
				$stmt->bindValue(':signupid', $userid, PDO::PARAM_INT);
				$stmt->bindValue(':id', $databaseid, PDO::PARAM_INT);
				$stmt->execute();
			}
			if($type == 'YGYID'){
				$stmt = $db->prepare("UPDATE `Order` SET `YGYID`=:ygyid WHERE `ID`=:id");
				$stmt->bindValue(':ygyid', $userid, PDO::PARAM_INT);
				$stmt->bindValue(':id', $databaseid, PDO::PARAM_INT);
				$stmt->execute();
			}
		} catch (PDOException $e) {
			echo $e->getCode().'\n\r DB Error, could not save to the database. Save Signup Details';
		}

		$db = NULL;		
	}

	static function saveOrderDetails($databaseid, $orderid, $type){
		return;
		$db = self::initDB();
		try {
			if($type == 'OnlineOrderID'){
				$stmt = $db->prepare("UPDATE `Order` SET `OnlineOrderID`=:orderid WHERE `ID`=:id");
				$stmt->bindValue(':orderid', $orderid, PDO::PARAM_INT);
				$stmt->bindValue(':id', $databaseid, PDO::PARAM_INT);
				$stmt->execute();
			}
			if($type == 'OrderID'){
				$stmt = $db->prepare("UPDATE `Order` SET `OrderID`=:orderid WHERE `ID`=:id");
				$stmt->bindValue(':orderid', $orderid, PDO::PARAM_INT);
				$stmt->bindValue(':id', $databaseid, PDO::PARAM_INT);
				$stmt->execute();
			}
		} catch (PDOException $e) {
			echo $e->getCode().'\n\r DB Error, could not save to the database. Save Order Details';
		}

		$db = NULL;		
	}

	static function savePaymentInfo($databaseid, $payment){
		return;
		$transactioncompleted = ($payment['TransactionCompleted']==true) ? 1 : 0;
		$description = str_replace('+',' ',$payment['Description']);

		$db = self::initDB();
		try {
			$stmt = $db->prepare("UPDATE `Order` SET `TransactionCompleted`=:completed,`PaymentDescription`=:description,`AuthCode`=:authcode WHERE `ID`=:id");
			$stmt->bindValue(':completed', $transactioncompleted, PDO::PARAM_STR);
			$stmt->bindValue(':description', $description, PDO::PARAM_STR);
			$stmt->bindValue(':authcode', $payment['AuthCode'], PDO::PARAM_STR);
			$stmt->bindValue(':id', $databaseid, PDO::PARAM_INT);
			$stmt->execute();

		} catch (PDOException $e) {
			echo $e->getCode().'\n\r DB Error, could not save to the database. Save Payment Info';
		}

		$db = NULL;	
	}

	static function checkSaveCompleted($id){
		return;
		$db = self::initDB();
		try {
			$stmt = $db->prepare("SELECT * FROM `Order` WHERE `ID`=:id");
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			if($row_count < 0){
				echo "DB Error, could not save to the database. Check Save";
				exit;
			}else{
				$noerrors = false;
				while ($rows = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
					if($row['UserData'] != NULL && $row['YGYID'] != NULL && $row['OnlineOrderID'] != NULL && $row['OrderID'] != NULL && $row['TransactionCompleted'] != NULL && $row['PaymentDescription'] != NULL && $row['AuthCode'] != NULL){
						$noerrors = true;
					}
				}
				if($noerrors){
					try {
						$stmt = $db->prepare("UPDATE `Order` SET `Completed`=1 WHERE `ID`=:id");
						$stmt->bindValue(':id', $databaseid, PDO::PARAM_INT);
						$stmt->execute();
					} catch (PDOException $e) {
						echo $e->getCode().'\n\r DB Error, could not save to the database. Save';
					}
				}
			}
		} catch (PDOException $e) {
			echo $e->getCode().'\n\r DB Error, could not save to the database.  Check Save';
		}

		$db = NULL;
	}

	static function redirect_test(){
		if( !empty($_SESSION['redirect'])){
			$data = array('success' => true, 'redirect' => $_SESSION['redirect']);
		}else{
			$data = array('success' => false, 'message' => 'No redirect location set.');
		}
		self::returnJSON($data);
	}

	/*  Generic Functions */

	static function get_request_vars(){
		if($_SERVER['REQUEST_METHOD'] === 'POST') $request = $_POST;
		elseif($_SERVER['REQUEST_METHOD'] === 'GET') $request = $_GET;
		foreach ($request as $key => $value) {
			if($value != ''){
				$vars[$key] = trim(urldecode($value));
			} 
		}
		return $vars;
	}

	static function returnJSON($data){
		header('Content-type: application/json');
		echo json_encode($data);
	}

	function initDB(){
		$host = DB_HOST;
		$db = DB_NAME;
		$charset = DB_CHARSET;
		$user = DB_USER;
		$pass = DB_PASSWORD;
		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		$opt = array(
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			);
		$pdo = new PDO($dsn, $user, $pass, $opt);
		return $pdo;
	}
}
?>