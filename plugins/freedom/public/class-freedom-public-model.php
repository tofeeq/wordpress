<?php
class Freedom_Public_Model {

	public function getProducts(array $skus, $rep = null) {
		if (!$rep) {
			$rep = "E100129";
		}

		$request = [
			"RepOrCustomerNumber" => $rep,
			"ProductID" => $skus
		];
		
		//print_r($request);
		
		try {
			$res = Freedom_Client::json('POST', 'Products', $request);
			if ($res->Status == 'SUCCESS') {
				return $res->Result->Items;
			}
		} catch (Exception $e) {
			echo $e->getMessage(); 
			die();
		}
	}

	public function getRepInfo( $repNumber ) {
		
		$request = [
			"repNumberOrUrl" => $repNumber,
		];

		try {
			$res = Freedom_Client::request('GET', 'Representative/Details', $request);
			if ($res->Status == 'SUCCESS') {
				return $res->Result;
			}

		} catch (Exception $e) {
			echo $e->getMessage(); die();
		}
	}


	public function getShipping( array $order ) {
		$request = [
		  'CustomerID' => "",
		  'RepresentativeNumber' => $order['RepresentativeNumber'],
		  'EmailAddress' => $order['EmailAddress'],
		  'FirstName' => $order['FirstName'],
		  'LastName' => $order['LastName'],
		  'PhoneNumber' => $order['PhoneNumber'],
		  'OrderItems' => $order['OrderItems'],
		  //'Password' => 'string',
		  'BillingAddress' => $order['BillingAddress'],
		  'ShippingAddress' => $order['ShippingAddress'],
	  	  'ShipMethodID' => 2,
		];

		//echo "------- shipping ---------";
		//print_r($request);

		$res = Freedom_Client::json('POST', 'CalculateOrder', $request);
		return ["response" => $res, "request" => $request];	
	}


	public function getProductSchedule( $product ) {
		//get last three characters of a product 
		//first character is the amount
		//second two characters are the periodType
		//example 1MO means 1 Month, 1YR means 1 Year
		$productSku = $product['ProductID'];
		$type = substr($productSku, -2);
		$amount = substr($productSku, -3, 1);

		$recurring = new Recurring();
		$period = $recurring->getPeriodType($type, $amount);

		$schedule = [
		    'PeriodTypeID' =>  $period['code'],
		    'StartDate' => date("Y-m-d"),
		    'StopDate' =>  date("Y-m-d", strtotime("+50 years")),
		    'NextShipDate' => $period['date'],
		    'PeriodDay' => 1,
		  ];

		return $schedule;

	}
}

class Recurring {
	const MONTHLY 			= 1;
	const WEEKLY 			= 2;
	const FOUR_MONTHS 		= 1000; //EVERY FOUR MONTHS
	const ANNUALLY 			= 4;
	const QUARTERLY			= 5; 
	const SEMI_ANUAL		= 6; //EVERY SIX MONTHS
	const TWO_MONTHS		= 7; //EVERY TWO MONTHS 

	public function getPeriodType($code, $amount = 1) {
		switch ($code) {
			case 'MO':
				switch ($amount) {
					case 1:
						return [
							'code' => self::MONTHLY,
							'date' => date("Y-m-d", strtotime("+1 month"))
							]
						;
					break;
				}
			break;
			
			case 'YR':
				switch ($amount) {
					case 1:
					return [
							'code' => self::ANNUALLY,
							'date' => date("Y-m-d", strtotime("+1 year"))
							];
					break;
				}
			break;
		}
	}
}