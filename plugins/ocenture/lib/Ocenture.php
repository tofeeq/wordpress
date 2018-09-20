<?php
class Ocenture_Client {

	protected $_clientId;
	//protected $_endpoint = 
	//	'http://ocenture.net.st1.ocenture.com/webservice/soap/AM2/?wsdl';
	
	protected $_endpoint = 
		'https://ocenture.net/webservice/soap/AM2/?wsdl';
	
	protected $_client;

	protected function _buildQuery(&$params) {
		$params['args']["ClientID"] = $this->getClientId();
	}

	public function getClientId() {
		return $this->_clientId;
	}

	public function getClient() {
		if (!$this->_client) {
			$this->_client = new SoapClient($this->_endpoint, [
				'trace' => true, 
				'keep_alive' => false,
				'cache_wsdl'	=> WSDL_CACHE_NONE,
				'connection_timeout'	=> 90, //in seconds
				]);
		}
		return $this->_client;
	}

	public function __construct($clientId) {
		$this->_clientId = $clientId;
	}

	public function createAccount(array $params) {
		// Call wsdl function 
		$this->_buildQuery($params);
		try {
			$result = $this->getClient()->__soapCall("CreateAccount", $params);
			if ($result instanceof stdClass  && ($result->Status == 'Account Created')) {
				return $result;
			} else {
				$ob = new stdClass();
				$ob->Status = 'Erorr';
				$ob->Erorr =  $result;
				return $ob;
			}

		} catch (SoapFault $e) {
			$ob = new stdClass();
			$ob->Status = 'Erorr';
			$ob->Erorr =  $e->getMessage();
			return $ob;
		}

		
	}

	public function cancelAccount(array $params) {
		// Call wsdl function 
		$this->_buildQuery($params);
		try {
			$result = $this->getClient()->__soapCall("CancelAccount", $params);

			if ($result instanceof stdClass  && ($result->Status != 'Error')) {
				return $result;
			} else {
				$ob = new stdClass();
				$ob->Status = 'Erorr';
				$ob->Erorr =  $result;
				return $ob;
			}

		} catch (SoapFault $e) {
			$ob = new stdClass();
			$ob->Status = 'Erorr';
			$ob->Erorr =  $e->getMessage();
			return $ob;
		}

		
	}

	public function reactivateAccount(array $params) {
		// Call wsdl function 
		$this->_buildQuery($params);
		try {
			$result = $this->getClient()->__soapCall("ReactivateAccount", $params);
			if ($result instanceof stdClass  && ($result->Status != 'Error')) {
				return $result;
			} else {
				$ob = new stdClass();
				$ob->Status = 'Erorr';
				$ob->Erorr =  $result;
				return $ob;
			}

		} catch (SoapFault $e) {
			$ob = new stdClass();
			$ob->Status = 'Erorr';
			$ob->Erorr =  $e->getMessage();
			return $ob;
		}

		
	}
}
