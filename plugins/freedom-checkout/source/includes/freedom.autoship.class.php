<?php
if(!defined('DEBUG')) define('DEBUG',false);
if(!defined('STAGING')) define('STAGING',false);
if(!defined('TESTMODE')) define('TESTMODE',0);
if(!defined('TESTACCOUNT')) define('TESTACCOUNT',false);
require_once('lib/nusoap.php');

class FreedomAS{
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
		$this->serverpath = 'https://admin.securefreedom.com/youngevity/webservice/AutoshipAPI.asmx';
		
		if(STAGING){
			$this->username = 'website';
			$this->password = 'ryanb1';
			$this->serverpath = 'https://staging.securefreedom.com/youngevity/Webservice/AutoshipAPI.asmx';
		}
		
		if(TESTACCOUNT){
			$this->signupID = '104225';
			$this->repID = '100756866';
			$this->customerID = '5267';
			$this->customerNumber = 'R25873879';
			$this->onlineOrderID = '191877';
		}
		$proxyhost;$proxyport;$proxyusername;$proxypassword;
		
		//$this->onlineCustomerID = rand(1000,50000);

		// create client object
		$this->client = new nusoap_client($this->serverpath, false,
							$proxyhost, $proxyport, $proxyusername, $proxypassword);
	}

	function GetAutoshipProfiles($repnumber){
		$function = "GetAutoshipProfiles";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <RepNumber>".$repnumber."</RepNumber>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function GetProfileItemDetails($profileid){
		$function = "GetProfileItemDetails";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <ProfileID>".$profileid."</ProfileID>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	
	function AddItem($profileid, $itemid, $qty){
		$function = "AddItem";
		$params = "<Credentials>
					<Username>".$this->username."</Username>
					<Password>".$this->password."</Password>
				  </Credentials>
				  <ProfileID>".$profileid."</ProfileID>
				  <ItemNumber>".$itemid."</ItemNumber>
      			  <Quantity>".$qty."</Quantity>";
		
		$result = $this->client->call($function,$params,$this->secureFreedom,$this->secureFreedom.$function);
		
		if(DEBUG){echo '<pre>'; echo print_r($result); echo '</pre>'; }
		
		$retval = $result;
		
		return $retval;
	}
	

}

?>