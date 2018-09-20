<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-freedom-public-model.php';
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Freedom
 * @subpackage Freedom/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Freedom
 * @subpackage Freedom/public
 * @author     Your Name <email@example.com>
 */
class Freedom_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Freedom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Freedom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/freedom-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Freedom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Freedom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Register the script
		wp_register_script( $this->plugin_name . '_validation', plugin_dir_url( __FILE__ ) . 'js/freedom-public-validation.js', array( 'jquery' ), $this->version, false );

		// Localize the script with new data
		$translation_array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( $this->plugin_name . '_validation', 'frontend_ajax_object', $translation_array );

		// Enqueued script with localized data.
		wp_enqueue_script($this->plugin_name . '_validation' );


		/*wp_enqueue_script( $this->plugin_name . '_validation', plugin_dir_url( __FILE__ ) . 'js/freedom-public-validation.js', array( 'jquery' ), $this->version, false );*/
		wp_enqueue_script( $this->plugin_name . '_freedom', plugin_dir_url( __FILE__ ) . 'js/freedom.js', array($this->plugin_name . '_validation' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '_freedompub', plugin_dir_url( __FILE__ ) . 'js/freedom-public.js', array( $this->plugin_name . '_freedom' ), $this->version, false );

	}

	function prefix_enqueue() 
	{       
	    // JS
	    /*wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
	    wp_enqueue_script('prefix_bootstrap');*/

	    // CSS
	    /*wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
	    wp_enqueue_style('prefix_bootstrap');*/

	    wp_register_style($this->plugin_name . '_integrity-light',  plugins_url(  $this->plugin_name 
	    	. '/public/css/stacks/integrity-light.css' ) );

	    wp_enqueue_style($this->plugin_name . '_integrity-light');

	}

	

	public function init() {
		$this->add_rewrite_tags();
	}


	function add_query_vars($vars) {
		array_push($vars, 'freedom_rep');
		array_push($vars, 'sku');
    	return $vars;
	}

	function add_rewrite_tags() {
		add_rewrite_tag('%sku%', '([^&]+)');
	}

	function add_rewrite_rules(array $rules) {
		$newrules = array();

		
		// /order-now/E100129/sku/USSNAP1021,USYC1201/
		$newrules['^([^/]*)/([^/]*)/sku/([^/]*)'] = 'index.php?pagename=$matches[1]&freedom_rep=$matches[2]&sku=$matches[3]';

		// /order-now/sku/USSNAP1021,USYC1201/
		$newrules['^([^/]*)/sku/([^/]*)'] = 'index.php?pagename=$matches[1]&sku=$matches[2]';
		 

		//for query string rules, skus are handled by tags rewrite above

		// /order-now/E100129?sku=USSNAP1021,USYC1201
		$newrules['^([^/]*)/([^/]*)'] = 'index.php?pagename=$matches[1]&freedom_rep=$matches[2]';

		return $newrules + $rules;

	}

	function flush_rules() {
		$rules = get_option( 'rewrite_rules' );
		if (! isset( $rules['^([^/]*)/sku/([^/]*)'] ) ) {
			global $wp_rewrite;
		   	$wp_rewrite->flush_rules();
		}
	}

	public function shortcode_freedom_products($atts = [], $content = null, $tag = '') {

		

		/*$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
	    // override default attributes with user attributes
	    $wporg_atts = shortcode_atts([
                                     'title' => 'WordPress.org',
                                 ], $atts, $tag);*/

        $skus = get_query_var('sku');

        if ($skus) {
        	$cart = explode(",", $skus);
        }


		if ( isset($_POST['action']) && ($_POST['action'] == 'freedom_checkout') ) {

		} else {
			//get products from freedom
			$model = new Freedom_Public_Model();
			$repNumber = get_query_var('freedom_rep');
			
			$repName = "";

			if ( $repNumber ) {
				$repInfo = $model->getRepInfo($repNumber);
				if ( $repInfo ) {
					$repName = $repInfo->UserInfo->FirstName . 
						' ' . $repInfo->UserInfo->LastName;
				}
			}
			$products = $model->getProducts($cart, get_query_var('freedom_rep'));
			
			if ($products) {
				$this->prefix_enqueue();
				ob_start();
			    /*include( plugin_dir_path( __FILE__ ) . 'partials/freedom-products.php' );*/
			    include( plugin_dir_path( __FILE__ ) . 'partials/freedom-checkout.php' );
			    return ob_get_clean();	
			}
			
		}                              
	    
	}

	//event, its not being used in new flow, form will be submitted using ajax
	public function post_freedom_checkout( $content ) {
		
		if (isset($_POST['freedom_products'])) {

			//posted as array of checkboxes
			$cart = $_POST['freedom_products']; 
			
			$model = new Freedom_Public_Model();

			$products = $model->getProducts($cart, get_query_var('freedom_rep'));

			$this->prefix_enqueue();
			ob_start();
			include( plugin_dir_path( __FILE__ ) . 'partials/freedom-checkout.php' ); 
			$form = ob_get_clean();
			return $content . $form;

		} else {
			unset($_POST['action']);
			return $this->shortcode_freedom_products();
		}
	}

	public function newsignuporder() {
		$post = $_POST;

		/*
		Array(
		    [action] => newsignuporder
		    [SponsorRepNumber] => 123
		    [ReplicatedURL] => 
		    [Email] => testing.tecnotch@gmail.com
		    [Password] => 36310
		    [Gender] => M
		    [Birthday] => 2000-02-19
		    [Firstname] => TESTER
		    [Lastname] => TOFEEQ
		    [Phone1] => 3017874905
		    [BillCountry] => USA
		    [BillPostalCode] => 36310
		    [BillStreet1] => Abb
		    [BillStreet2] => 
		    [BillCity] => Abbivlie
		    [BillState] => AL
		    [ShipStreet1] => Abb
		    [ShipStreet2] => 
		    [ShipCity] => Abbivlie
		    [ShipState] => AL
		    [ShipPostalCode] => 36310
		    [ShipCountry] => USA
		    [CreditCardNumber] => 4222222222222
		    [ExpDateMonth] => 03
		    [ExpDateYear] => 20
		    [CVV] => 123
		    [Cart] => %5B%22USYG100086%22%5D
		)
		*/
		$phone1 = str_replace(['(', ')', ' ', '-'], ['', '', '', ''], $post['Phone1']);
		$request = [
			  'RepresentativeNumber' => $post['SponsorRepNumber'],
			  'Url' => $post['SponsorRepNumber'],
			  'UserInfo' => [
			    'FirstName' =>  $post['Firstname'],
			    'LastName' => $post['Lastname'],
			    // 'Company' => 'string',
			    'Email' => $post['Email'],
			    'DateOfBirth' => date("Y-m-d", strtotime($post['Birthday'])),
			    'Gender' => $post['Gender']
			  ],
			  //'Password' => 'string',
			  'BillingAddress' => [
			    'Name' => $post['Firstname'] . ' ' . $post['Lastname'],
			    'Phone' => $phone1,
			    'Street1' => $post['BillStreet1'],
			    'Street2' => $post['BillStreet2'],
			    'City' => $post['BillCity'],
			    'State' => $post['BillState'],
			    'Country' => $post['BillCountry'],
			    'PostalCode' => $post['BillPostalCode'],
			  ],
			  'ShippingAddress' =>  [
			    'Name' => $post['Firstname'] . ' ' . $post['Lastname'],
			    'Phone' => $phone1,
			    'Street1' => $post['ShipStreet1'],
			    'Street2' => $post['ShipStreet2'],
			    'City' => $post['ShipCity'],
			    'State' => $post['ShipState'],
			    'Country' => $post['ShipCountry'],
			    'PostalCode' => $post['ShipPostalCode'],
			  ],
			  'ContactDetails' => [
			    'Phone1' => $phone1,
			   	// 'Phone2' => $post['Phone1'],
			  ],
			  //'TaxID' => 'string',
			  'IPAddress' => Freedom_Client::ip()
		];

		

		
		try {
			if (1) {
				$customerResponse = Freedom_Client::json('POST', 'Customer/Create', $request);
			} else {
				$customerResponse = new stdClass;
			    $customerResponse->OnlineCustomerId = "117510";
			    $customerResponse->CustomerId = "R26106297";
			    $customerResponse->TransactionId = "764092";
			    $customerResponse->Status = "SUCCESS";
			}			
			
			if ($customerResponse->Status == 'SUCCESS') {
				
				try {

					$orderResponseData = $this->_createOrder(
						$customerResponse, $post);
					//print_r($orderResponseData);
					$orderResponse = $orderResponseData['response'];
					$orderRequest = $orderResponseData['request'];

					if ($customerResponse->Status == 'SUCCESS') {
						$info = [
								"ShippingTotal" => $orderRequest['ShippingTotal'],
								"OrderTotal" => $orderRequest['PaymentAmount'],
								"CustomerID" => $customerResponse->CustomerId,
								"OrderID" => $orderResponse->OrderID,
							];

						try {

							$arg = [
								'order' => [
									"user" => $request,
									"product"	=> $orderRequest
								], 
								'result' => $info
							];

							$arr =  apply_filters('freedom_order_success', $arg);
							if ($arr) {
								$info['plugin'] = $arr;
							}
						} catch (Exception $e) {

						}	


						echo json_encode([
							"success" => true,
							"info" => $info
						]);

					} else {
						$this->error($orderResponse);
					}
				} catch (Exception $e) {
					$res = json_decode($e->getMessage());
					$this->error($res);
				}
			} else {
				//print_r($request);
				$this->error($customerResponse);
			}	
		} catch (Exception $e) {
			$res = json_decode($e->getMessage());
			$this->error($res);
		}

 		/*
 		stdClass Object
		(
		    [OnlineCustomerId] => 117510
		    [CustomerId] => R26106297
		    [TransactionId] => 764092
		    [Status] => SUCCESS
		    [ErrorMessage] => 
		    [StatusCode] => OK
		    [Message] => 
		)
		*/
		wp_die(); 
	}

	/*
		@NOTES: set "FirstNameOnCard" as "TEST" to skip the payment processing
	*/
	private function _createOrder($customer, $post) {

		$cart = explode(",", $post['Cart']);
		 

		$model = new Freedom_Public_Model();
		$items = $model->getProducts($cart, get_query_var('freedom_rep'));

		$products = [];
		$payment = 0;

		foreach ($items as $product) {
			$products[] = [
		        "ProductID" => $product->ProductID,
		        "Quantity" => 1
	        ];

	        $payment += $product->Price;
		}

		$shippingResponseData = $this->_getShipping($customer, $post, $products);

		$shippingResponse = $shippingResponseData['response'];

		$shipping = 0;
		$tax = 0;
		$handling = 0;
		$amount = 0;

		if ($shippingResponse->Status == 'SUCCESS') {
			$shipping = $shippingResponse->Result->ShippingTotal;
			$tax = $shippingResponse->Result->TaxTotal;
			$handling = $shippingResponse->Result->HandlingFee;
			$amount = $shippingResponse->Result->BalanceDue;
		}

		 

		/*
 		stdClass Object
		(
		    [OnlineCustomerId] => 117510
		    [CustomerId] => R26106297
		    [TransactionId] => 764092
		    [Status] => SUCCESS
		    [ErrorMessage] => 
		    [StatusCode] => OK
		    [Message] => 
		)
		*/

		$request = [
			  'RetailOrder' => false,
			  'CustomerID' => $customer->CustomerId,
			  'RepresentativeNumber' => $post['SponsorRepNumber'],
			  'EmailAddress' => $post['Email'],
			  'FirstName' => $post['Firstname'],
			  'LastName' => $post['Lastname'],
			  'PhoneNumber' => $post['Phone1'],
			  //'ReferenceNumber' => 1343,
			  'OrderItems' =>  $products,
			  'BillingAddress' => [
			    'Name' => $post['Firstname'] . ' ' . $post['Lastname'],
			    'Phone' => $post['Phone1'],
			    'Street1' => $post['BillStreet1'],
			    'Street2' => $post['BillStreet2'],
			    'City' => $post['BillCity'],
			    'State' => $post['BillState'],
			    'Country' => $post['BillCountry'],
			    'PostalCode' => $post['BillPostalCode'],
			  ],
			  'ShippingAddress' =>  [
			    'Name' => $post['Firstname'] . ' ' . $post['Lastname'],
			    'Phone' => $post['Phone1'],
			    'Street1' => $post['ShipStreet1'],
			    'Street2' => $post['ShipStreet2'],
			    'City' => $post['ShipCity'],
			    'State' => $post['ShipState'],
			    'Country' => $post['ShipCountry'],
			    'PostalCode' => $post['ShipPostalCode'],
			  ],

			  'PaymentInfo' => [ 
			    'FirstNameOnCard' => $post['Firstname'],
			    'LastNameOnCard' => $post['Lastname'],
			    'CardNumber' => $post['CreditCardNumber'],
			    'ExpiryDateMonth' => $post['ExpDateMonth'],
			    'ExpiryDateYear' => $post['ExpDateYear'],
			    'CVV' => $post['CVV'],
			    'Address1' => $post['BillStreet1'],
			    'Address2' => $post['BillStreet2'],
			    'City' => $post['BillCity'],
			    'StateProvince' => $post['BillState'],
			    'Country' => $post['BillCountry'],
			    'PostalCode' => $post['BillPostalCode']
			    ],

			  'InvoiceNotes' => "Sales Order",
			  'IPAddress' => Freedom_Client::ip(),
			  "PaymentAmount" => sprintf("%.2f", $amount),
			  "OverrideShipping" => 1,
		      "ShippingTotal" => sprintf("%.2f", ($shipping + $tax + $handling)),
		      "ShipMethodID" => 2
		];

		//Freedom_Client::$debug = true;
		$res = Freedom_Client::json('POST', 'CreateOrder/v2', $request);
		return ["response" => $res, "request" => $request];
	}

	public function _getShipping($customer, $post, $products) {
		$request = array (
		  'CustomerID' => "",
		  'RepresentativeNumber' => $post['SponsorRepNumber'],
		  'EmailAddress' => $post['Email'],
		  'FirstName' => $post['Firstname'],
		  'LastName' => $post['Lastname'],
		  'PhoneNumber' => $post['Phone1'],


		  'OrderItems' => $products,

		  //'Password' => 'string',
		  'BillingAddress' => [
		    'Name' => $post['Firstname'] . ' ' . $post['Lastname'],
		    'Phone' => $post['Phone1'],
		    'Street1' => $post['BillStreet1'],
		    'Street2' => $post['BillStreet2'],
		    'City' => $post['BillCity'],
		    'State' => $post['BillState'],
		    'Country' => $post['BillCountry'],
		    'PostalCode' => $post['BillPostalCode'],
		  ],
		  'ShippingAddress' =>  [
		    'Name' => $post['Firstname'] . ' ' . $post['Lastname'],
		    'Phone' => $post['Phone1'],
		    'Street1' => $post['ShipStreet1'],
		    'Street2' => $post['ShipStreet2'],
		    'City' => $post['ShipCity'],
		    'State' => $post['ShipState'],
		    'Country' => $post['ShipCountry'],
		    'PostalCode' => $post['ShipPostalCode'],
		  ],
		  
	  	  'ShipMethodID' => 2,
		);
		//print_r( json_encode($request) ); die;


		$res = Freedom_Client::json('POST', 'CalculateOrder', $request);
		return ["response" => $res, "request" => $request];	
	}

	public function error($obj) {
		if (preg_match("#payment#i", $obj->ErrorMessage)) {
			$error = "Payment could not be processed";
		} else if (preg_match("#REPRESENTATIVE#i", $obj->ErrorMessage)) {
			$error = "Please provide valid reference ID";
		} else {
			$error = $obj->ErrorMessage;
		}

		if ($obj->statusCode == 401) {
			//$error = $obj->message;
			$error = "Freedom not configured correctly";
		}

		if (!$error) {
			$error = "Please check your network connection";
		}

		echo json_encode([
				"error" => $error,
				"response" => $obj
			]);
	}
	//ajax func
	public function repinfo() {
		$repNumber = $_GET['repnumber'];
		$model = new Freedom_Public_Model();
			
		$repName = "";

		if ( $repNumber ) {
			$repInfo = $model->getRepInfo($repNumber);

			if ( $repInfo ) {
				$repName = $repInfo->UserInfo->FirstName . 
					' ' . $repInfo->UserInfo->LastName;
				echo json_encode(['success' => true,
						'repName'=> $repName
					]);
				die();
			}
		}
	}
}
