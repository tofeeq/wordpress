<?php
if(!defined('DEBUG')) define('DEBUG',false);
if(!defined('STAGING')) define('STAGING',false);
if(!defined('TESTMODE')) define('TESTMODE',0);
if(!defined('TESTACCOUNT')) define('TESTACCOUNT',false);
require_once('lib/nusoap.php');

class Freedom{
	private $username;
	private $password;
	private $serverpath;
	private $stagingpath;
	private $client;
	private $clientInfo = array();
	private $customerID;
	private $signupID;
	private $repID;
	private $onlineOrderID;
	private $onlineCustomerID;
	private $customerNumber;
	private $secureFreedom = "http://www.securefreedom.com/";
	private $orderID;
	
	

	function __construct(){
		$this->username = 'yngwebconnect';
		$this->password = 'yNgw3b42cnct';
		//$this->serverpath = 'https://secure.youngevity.com/backoffice/webservice/OnlineAPI.asmx';
		$this->serverpath = 'https://admin.securefreedom.com/youngevity/webservice/onlineapi.asmx';
		
		if(STAGING){
			$this->username = 'website';
			$this->password = 'ryanb1';
			$this->serverpath = 'https://staging.securefreedom.com/youngevity/Webservice/OnlineAPI.asmx';
		}
		
		if(TESTACCOUNT){
			$this->signupID = '104225';
			$this->repID = '100756866';
			$this->customerID = '5267';
			$this->customerNumber = 'R25873879';
			$this->onlineOrderID = '191877';
		}
		
		//$this->onlineCustomerID = rand(1000,50000);

		// create client object
		$this->client = new nusoap_client($this->serverpath, false);
	}

	function LoginCheck_Rep($username,$password){
		$function = "LoginCheck_Rep";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <Username>".$username."</Username>
      			  <Password>".$password."</Password>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function GetRepInfo($repurl){
		$function = "GetRepInfo";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <RepNumberOrURL>".$repurl."</RepNumberOrURL>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}

	function GetRepInfo_V3($repurl){
		$function = "GetRepInfo_V3";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <RepNumberOrURL>".$repurl."</RepNumberOrURL>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function GetInventory_SingleItem($itemid, $pricetype = 'wholesale'){
		$function = 'GetInventory_SingleItem_v3';
		$params ="<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				<RepNumber>312501</RepNumber>
				<ProductID>".$itemid."</ProductID>
				<PriceType>".$pricetype."</PriceType>
				<Country>USA</Country>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }

		$retval = $result; 
		
		return $retval;
	}
	
	function CheckRepURL($url){
		$function = "CheckRepURL";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				</Credentials>
				<URL>".$url."</URL>";
			
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		return $retval;
	}
	
	function CheckEmail($email){
		$function = "CheckEmail";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <Email>".$email."</Email>
				  <Type>2</Type>";
				
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		return $retval;
	}
	
	function ZipLookup($zip){
		$function = "ZipLookup_v2";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				</Credentials>
				<ZipCode>".$zip."</ZipCode>";
				
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		return $retval;
	}
	function logRequest($req, $resp){
		$log = fopen('../logs/freedom.txt', 'a');
		fwrite($log, "---------------- START NEW LOG PART ---------------\n");
		fwrite($log, "--------------".date('Y-m-d H:i:s')."--------------\n");
		fwrite($log, "---------------- REQ START ---------------\n");
		fwrite($log, var_export($req, true));
		fwrite($log, "\n---------------- REQ END ---------------\n");
		fwrite($log, "---------------- RESP START ---------------\n");
		fwrite($log, var_export($resp, true));
		fwrite($log, "\n---------------- RESP END ---------------\n");
		fwrite($log, "---------------- END LOG PART ------------------\n\n\n\n\n\n");
		fclose($log);
	}
	function CreateOnlineSignUp($data, $type='customer'){
		$data['BillStreet2'] = (!isset($data['BillStreet2'])) ? ' ' : $data['BillStreet2'];
		$data['ShipStreet2'] = (!isset($data['ShipStreet2'])) ? ' ' : $data['ShipStreet2'];
		$data['TaxID'] = (!isset($data['TaxID'])) ? ' ' : $data['TaxID'];
		$data['ShipCity'] = (!isset($data['ShipCity'])) ? '' : $data['ShipCity'];
		$data['ShipStreet1'] = (!isset($data['ShipStreet1'])) ? '' : $data['ShipStreet1'];
		$data['ShipPostalCode'] = (!isset($data['ShipPostalCode'])) ? '' : $data['ShipPostalCode'];
		$data['ShipCountry'] = (!isset($data['ShipCountry'])) ? '' : $data['ShipCountry'];
		$data['ShipState'] = (!isset($data['ShipState'])) ? '' : $data['ShipState'];
		$data['ShipStreet1'] = (!isset($data['ShipStreet1'])) ? '' : $data['ShipStreet1'];
		$data['ShipStreet2'] = (!isset($data['ShipStreet2'])) ? '' : $data['ShipStreet2'];
		$data['ShipCity'] = (!isset($data['ShipCity'])) ? '' : $data['ShipCity'];
		$data['ShipPostalCode'] = (!isset($data['ShipPostalCode'])) ? '' : $data['ShipPostalCode'];
		$data['ShipCountry'] = (!isset($data['ShipCountry'])) ? '' : $data['ShipCountry'];

		$function = "CreateOnlineSignup";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				</Credentials>
				<OnlineSignupRecord>
					<SponsorRepNumber>".$data['SponsorRepNumber']."</SponsorRepNumber>
					<Firstname>".$data['Firstname']."</Firstname>
					<Lastname>".$data['Lastname']."</Lastname>
					<Company> </Company>
					<BillStreet1>".$data['BillStreet1']."</BillStreet1>
					<BillStreet2>".$data['BillStreet2']."</BillStreet2>
					<BillCity>".$data['BillCity']."</BillCity>
					<BillState>".$data['BillState']."</BillState>
					<BillPostalCode>".$data['BillPostalCode']."</BillPostalCode>
					<BillCountry>".$data['BillCountry']."</BillCountry>
					<ShipStreet1>".$data['ShipStreet1']."</ShipStreet1>
					<ShipStreet2>".$data['ShipStreet2']."</ShipStreet2>
					<ShipCity>".$data['ShipCity']."</ShipCity>
					<ShipState>".$data['ShipState']."</ShipState>
					<ShipPostalCode>".$data['ShipPostalCode']."</ShipPostalCode>
					<ShipCountry>".$data['ShipCountry']."</ShipCountry>";
					if($type=='customer'){
					//	$params .= "<TaxID>0</TaxID>";
					}else {
					}
					$params .= "<TaxID>".$data['TaxID']."</TaxID>";
					$params .= "<Phone1>".$data['Phone1']."</Phone1>
					<Phone2> </Phone2>
        			<Phone3> </Phone3>
        			<Phone4> </Phone4>
					<ReplicatedURL>".$data['ReplicatedURL']."</ReplicatedURL>
					<ReplicatedText> </ReplicatedText>
					<Password>".$data['Password']."</Password>
					<Email>".$data['Email']."</Email>
					<RepTypeID>1</RepTypeID>
					<DateOfBirth>".date("Y-m-d", strtotime($data['DateOfBirth']))."</DateOfBirth>
					<IPAddress>".$this->GetIPAddress()."</IPAddress>
					<PreferredCulture>1</PreferredCulture>
					<PayoutMethodID>0</PayoutMethodID>
				</OnlineSignupRecord>";
		//print_r($params); die;		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		//echo "Result"; print_r($result); die;

		if(DEBUG){ echo $params . '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		if(!TESTACCOUNT){
			$this->signupID = $retval;
		}
		$this->logRequest($params, $retval);
		return $retval;	
	}
	
	function CreateRep($signupid){
		$function = "CreateRep";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineSignupID>".$signupid."</OnlineSignupID>";
				  
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;

		if(!TESTACCOUNT){
			$this->repID = $retval;
		}
		
		return $retval;
	}
	
	function CreateOnlineOrder($data){
		$function = "CreateOnlineOrder";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderRecord>
					<RepNumber>".$data['RepNumber']."</RepNumber>
					<BillFirstname>".$data['Firstname']."</BillFirstname>
					<BillLastname>".$data['Lastname']."</BillLastname>
					<BillCompany></BillCompany>
					<BillStreet1>".$data['BillStreet1']."</BillStreet1>
					<BillStreet2>".$data['BillStreet2']."</BillStreet2>
					<BillCity>".$data['BillCity']."</BillCity>
					<BillState>".$data['BillState']."</BillState>
					<BillPostalCode>".$data['BillPostalCode']."</BillPostalCode>
					<BillCountry>".$data['BillCountry']."</BillCountry>
					<BillPhone>".$data['Phone1']."</BillPhone>
					<ShipFirstname>".$data['Firstname']."</ShipFirstname>
					<ShipLastname>".$data['Lastname']."</ShipLastname>
					<ShipCompany></ShipCompany>
					<ShipStreet1>".$data['ShipStreet1']."</ShipStreet1>
					<ShipStreet2>".$data['ShipStreet2']."</ShipStreet2>
					<ShipCity>".$data['ShipCity']."</ShipCity>
					<ShipState>".$data['ShipState']."</ShipState>
					<ShipPostalCode>".$data['ShipPostalCode']."</ShipPostalCode>
					<ShipCountry>".$data['ShipCountry']."</ShipCountry>
					<ShipPhone>".$data['Phone1']."</ShipPhone>
					<ContactEmail>".$data['Email']."</ContactEmail>
					<InvoiceNotes></InvoiceNotes>
					<IPAddress></IPAddress>
					<MarketShowID>5</MarketShowID>
				  </OnlineOrderRecord>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		if(!TESTACCOUNT){
			$this->onlineOrderID = $retval;
		}
		$this->logRequest($params, $retval);
		return $retval;

	}
	
	function CreateOnlineOrderSignup($data){
		$function = "CreateOnlineOrder";
		$data['ShipState'] = (!isset($data['ShipState'])) ? '' : $data['ShipState'];

		$data['ShipStreet1'] = (!isset($data['ShipStreet1'])) ? '' : $data['ShipStreet1'];
		$data['ShipStreet2'] = (!isset($data['ShipStreet2'])) ? '' : $data['ShipStreet2'];
		$data['ShipCity'] = (!isset($data['ShipCity'])) ? '' : $data['ShipCity'];
		$data['ShipPostalCode'] = (!isset($data['ShipPostalCode'])) ? '' : $data['ShipPostalCode'];
		$data['ShipCountry'] = (!isset($data['ShipCountry'])) ? '' : $data['ShipCountry'];

		

		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderRecord>
					<OnlineSignupID>".$data['SignUpID']."</OnlineSignupID>
					<BillFirstname>".$data['Firstname']."</BillFirstname>
					<BillLastname>".$data['Lastname']."</BillLastname>
					<BillCompany></BillCompany>
					<BillStreet1>".$data['BillStreet1']."</BillStreet1>
					<BillStreet2>".$data['BillStreet2']."</BillStreet2>
					<BillCity>".$data['BillCity']."</BillCity>
					<BillState>".$data['BillState']."</BillState>
					<BillPostalCode>".$data['BillPostalCode']."</BillPostalCode>
					<BillCountry>".$data['BillCountry']."</BillCountry>
					<BillPhone>".$data['Phone1']."</BillPhone>
					<ShipFirstname>".$data['Firstname']."</ShipFirstname>
					<ShipLastname>".$data['Lastname']."</ShipLastname>
					<ShipCompany></ShipCompany>
					<ShipStreet1>".$data['ShipStreet1']."</ShipStreet1>
					<ShipStreet2>".$data['ShipStreet2']."</ShipStreet2>
					<ShipCity>".$data['ShipCity']."</ShipCity>
					<ShipState>".$data['ShipState']."</ShipState>
					<ShipPostalCode>".$data['ShipPostalCode']."</ShipPostalCode>
					<ShipCountry>".$data['ShipCountry']."</ShipCountry>
					<ShipPhone>".$data['Phone1']."</ShipPhone>
					<ContactEmail>".$data['Email']."</ContactEmail>
					<InvoiceNotes></InvoiceNotes>
					<IPAddress></IPAddress>
					<MarketShowID>5</MarketShowID>
				  </OnlineOrderRecord>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		if(!TESTACCOUNT){
			$this->onlineOrderID = $retval;
		}
		$this->logRequest($params, $retval);
		return $retval;

	}
	
	function OnlineOrder_AddItem($orderid, $productid, $quantity){
		$function = "OnlineOrder_AddItem";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>
				  <ProductID>".$productid."</ProductID>
				  <Quantity>".$quantity."</Quantity>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_Autoship_AddItem($orderid, $productid, $quantity){
		$function = "OnlineOrder_Autoship_AddItem";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>
				  <ProductID>".$productid."</ProductID>
				  <Quantity>".$quantity."</Quantity>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_Autoship_SetDate($orderid, $periodday){
		$date = strtotime("+1 month");
		$DateNextRun = date("Y-m-", $date).str_pad($periodday,2,"0", STR_PAD_LEFT)."T00:00:00+00:00";
		$function = "OnlineOrder_Autoship_SetDate";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <setDate>1</setDate>
				  <PeriodDay>".$periodday."</PeriodDay>
				  <DateNextRun>".$DateNextRun."</DateNextRun>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_ClearItems($orderid){
		$function = "OnlineOrder_ClearItems";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_GetItems($orderid){
		$function = "OnlineOrder_GetItems";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_GetShipMethods_v2($orderid){
		$function = "OnlineOrder_GetShipMethods_v2";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				</Credentials>
				<OnlineOrderID>".$orderid."</OnlineOrderID>
				<LocaleID>1</LocaleID>";
				
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_UpdateShipMethod($orderid, $shipmethod){
		$function = "OnlineOrder_UpdateShipMethod";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				</Credentials>
				<OnlineOrderID>".$orderid."</OnlineOrderID>
				<ShipMethodID>".$shipmethod."</ShipMethodID>";
				
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		return $retval;
	}
	
	function OnlineOrder_GetTotals($orderid){
		$function = "OnlineOrder_GetTotals";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>";
				  
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function ValidateCCNumber($ccnumber){
		$function = 'ValidateCCNumber';
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <CreditCardNumber>".$ccnumber."</CreditCardNumber>";
				  
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		$retval = $result;
		
		return $retval;
	}
	
	function Payment_CreditCard($orderid,$firstname,$lastname,$address,$zip,$expmonth,$expyear,$CVV,$CVV2,$cc,$amount, $tax_id, $company_name){
		$function = "Payment_CreditCard";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <TestMode>0</TestMode>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>
				  <PaymentInfo>
					<FirstNameOnCard>".$firstname."</FirstNameOnCard>
					<LastNameOnCard>".$lastname."</LastNameOnCard>
					<Address>".$address."</Address>
					<ZipCode>".$address."</ZipCode>
					<ExpDateMonth>".$expmonth."</ExpDateMonth>
					<ExpDateYear>".$expyear."</ExpDateYear>
					<CVV>".$CVV."</CVV>
					<CVV2>".$CVV2."</CVV2>
					<CreditCardNumber>".$cc."</CreditCardNumber>
					<Amount>".$amount."</Amount>
					<TaxId>".$tax_id."</TaxId>
					<CompanyName>".$company_name."</CompanyName>
				  </PaymentInfo>";
				  
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ 
			echo $params.'<pre>'; echo print_r($result); echo '</pre>'; 
		}
		
		$retval = $result;

		return $retval;	
	}
	
	function Payment_CreditCard_Test($orderid,$firstname,$lastname,$address,$zip,$expmonth,$expyear,$CVV,$CVV2,$cc,$amount){
		$function = "Payment_CreditCard";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <TestMode>-1</TestMode>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>
				  <PaymentInfo>
					<FirstNameOnCard>".$firstname."</FirstNameOnCard>
					<LastNameOnCard>".$lastname."</LastNameOnCard>
					<Address>".$address."</Address>
					<ZipCode>".$address."</ZipCode>
					<ExpDateMonth>".$expmonth."</ExpDateMonth>
					<ExpDateYear>".$expyear."</ExpDateYear>
					<CVV>".$CVV."</CVV>
					<CVV2>".$CVV2."</CVV2>
					<CreditCardNumber>".$cc."</CreditCardNumber>
					<Amount>".$amount."</Amount>
				  </PaymentInfo>";
				  
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ 
			echo $params.'<pre>'; echo print_r($result); echo '</pre>'; 
		}
		
		$retval = $result;

		return $retval;	
	}
	
	function CreateOrder($orderid, $signupid = ''){
		$function = 'CreateOrder';
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <OnlineOrderID>".$orderid."</OnlineOrderID>";
				  
		if($signupid != '') $params .= "<OnlineSignupID>".$signupid."</OnlineSignupID>";

		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
				
		return $retval;
	}
	
	function GenerateOrderAR($orderid){
		$function = 'GenerateAR';
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <ARType>NewOrder</ARType>
				  <LocaleID>1</LocaleID>
				  <Subject>".$orderid."</Subject>";

		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
				
		return $retval;
	}
	
	function GenerateCustomerAR($customerid){
		$function = 'GenerateAR';
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <ARType>NewCustomer</ARType>
				  <LocaleID>int</LocaleID>
				  <Subject>".$customerid."</Subject>";

		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
				
		return $retval;
	}
	
	function GenerateRepAR($repid){
		$function = 'GenerateAR';
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <ARType>NewRep</ARType>
				  <LocaleID>int</LocaleID>
				  <Subject>".$repid."</Subject>";

		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){ echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
				
		return $retval;
	}
	
	function GetIPAddress(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

?>