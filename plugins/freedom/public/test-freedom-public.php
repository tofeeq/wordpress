<?php
class Test_Freedom extends Freedom_Public {
	
	public function init() {
		add_action( 'wp_footer', [$this, 'start'] );
	}

	public function start() {
		if (isset($_GET['testcase'])) {
			switch ($_GET['testcase']) {
				case 'order':
					$this->_test_order();
					break;
			}
		}
	}

	private function _test_order() {
		Freedom_Logger::start();
		Freedom_Logger::log("Testing order manually");

		$request = array (
		  'RetailOrder' => false,
		  'CustomerID' => 'R26107673',
		  'RepresentativeNumber' => '101233985',
		  'EmailAddress' => 'cynthia3sunwall@gmail.com',
		  'FirstName' => 'Cynthia',
		  'LastName' => 'Sunwall',
		  'PhoneNumber' => '(914) 357-1338',
		  'OrderItems' => 
		  array (
		    0 => 
		    array (
		      'ProductID' => 'YSDPROIDFP1MO',
		      'Quantity' => 1,
		    ),
		  ),
		  'BillingAddress' => 
		  array (
		    'Name' => 'Cynthia Sunwall',
		    'Phone' => '(914) 357-1338',
		    'Street1' => '22 Cornelius Ln',
		    'Street2' => '',
		    'City' => 'Baldwin Place',
		    'State' => 'NY',
		    'Country' => 'USA',
		    'PostalCode' => '82070',
		  ),
		  'ShippingAddress' => 
		  array (
		    'Name' => 'Cynthia Sunwall',
		    'Phone' => '(914) 357-1338',
		    'Street1' => '22 Cornelius Ln',
		    'Street2' => '',
		    'City' => 'Baldwin Place',
		    'State' => 'NY',
		    'Country' => 'USA',
		    'PostalCode' => '82070',
		  ),
		  'PaymentInfo' => 
		  array (
		    'FirstNameOnCard' => 'Cynthia',
		    'LastNameOnCard' => 'Sunwall',
		    'CardNumber' => '',
		    'ExpiryDateMonth' => '04',
		    'ExpiryDateYear' => '22',
		    'CVV' => '057',
		    'Address1' => '22 Cornelius Ln',
		    'Address2' => '',
		    'City' => 'Baldwin Place',
		    'StateProvince' => 'NY',
		    'Country' => 'USA',
		    'PostalCode' => '82070',
		  ),
		  'InvoiceNotes' => 'Sales Order',
		  'IPAddress' => '74.89.147.35',
		  'PaymentAmount' => '69.99',
		  'OverrideShipping' => 1,
		  'ShippingTotal' => '0.00',
		  'ShipMethodID' => 2,
		);
		
		Freedom_Logger::log("order request");
		$logreq = $request;
		$logreq['PaymentInfo']['CardNumber'] = 'xxxxxxxxxxx';
		$logreq['PaymentInfo']['CVV'] = 'xxx';
		Freedom_Logger::log($logreq);
		
		try {
			$res = Freedom_Client::json('POST', 'CreateOrder/v2', $request);
			Freedom_Logger::log("order response");
			Freedom_Logger::log($res);

		} catch (Exception $e) {
			Freedom_Logger::log("Exception");
			Freedom_Logger::log($e->getMessage());	
		}		 

		Freedom_Logger::close();
	}
}