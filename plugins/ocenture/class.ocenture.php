<?php

use Ocenture\Logger as Logger;

class Ocenture{

  protected $_initiated = false;

  //live client id
  public $clientId = '1052714';

  //constructor is not called in / after any wp hook so any general hook which is not dependant on any other wp hook can be defined here
  public function __construct() {
    add_action( 'plugins_loaded', [ $this, 'update_db_check' ] );
    
  }

  //this function init is called inside the wp init hook, so all the hooks what needs to be called after init are defined here
  public function init() {
    if ( ! $this->_initiated ) {
      $this->_init_hooks();
    }
  }

  /**
   * Initializes WordPress hooks
   */

  //this is called in init hook
  protected function _init_hooks() {
    $this->_initiated = true;

    //assign the client id test based on freedom settings
    $setting = get_option('freedom-settings');

    //if freedom is in test mode then set ocenture client id as test id
    if ($setting['mode'] == 'sandbox') {
        //set sandbox settings url or id here
    } 

    //on order success from freedom, call ocenture update to insert data into wp and create account on oncenture
    add_filter( 'freedom_order_success', array(
        $this, 'ocenture_update' 
      ));

    //perform testing here
    if (isset($_GET['test_ocenture'])) {
      Logger::start("Manual Insert");
      $this->ocenture_insert();
      Logger::close();
    }
  }

  /*
  This is how this plugin receives data from freedom in ocenture update function 

  array (
    'order' => 
    array (
      'user' => 
      array (
        'RepresentativeNumber' => 'E100129',
        'Url' => 'E100129',
        'UserInfo' => 
        array (
          'FirstName' => 'TEST',
          'LastName' => 'Murray',
          'Email' => 'tofeeq3@gmail.com',
          'DateOfBirth' => '1970-01-01',
          'Gender' => 'Male',
        ),
        'BillingAddress' => 
        array (
          'Name' => 'TEST Murray',
          'Phone' => '1234567890',
          'Street1' => '7584 Big Canyon Drive',
          'Street2' => '',
          'City' => 'Anaheim',
          'State' => 'California',
          'Country' => 'USA',
          'PostalCode' => '92808',
        ),
        'ShippingAddress' => 
        array (
          'Name' => 'TEST Murray',
          'Phone' => '1234567890',
          'Street1' => '7584 Big Canyon Drive',
          'Street2' => '',
          'City' => 'Anaheim',
          'State' => 'California',
          'Country' => 'USA',
          'PostalCode' => '92808',
        ),
        'ContactDetails' => 
        array (
          'Phone1' => '1234567890',
        ),
        'IPAddress' => '182.186.82.142',
      ),
      'product' => 
      array (
        'RetailOrder' => false,
        'CustomerID' => 'R26106503',
        'RepresentativeNumber' => 'E100129',
        'EmailAddress' => 'tofeeq3@gmail.com',
        'FirstName' => 'TEST',
        'LastName' => 'Murray',
        'PhoneNumber' => '1234567890',
        'OrderItems' => 
        array (
          0 => 
          array (
            'ProductID' => 'YSDPROIDM1MO',
            'Quantity' => 1,
          ),
          1 => 
          array (
            'ProductID' => 'YSDPROIDMP1MO',
            'Quantity' => 1,
          ),
        ),
        'BillingAddress' => 
        array (
          'Name' => 'TEST Murray',
          'Phone' => '1234567890',
          'Street1' => '7584 Big Canyon Drive',
          'Street2' => '',
          'City' => 'Anaheim',
          'State' => 'California',
          'Country' => 'USA',
          'PostalCode' => '92808',
        ),
        'ShippingAddress' => 
        array (
          'Name' => 'TEST Murray',
          'Phone' => '1234567890',
          'Street1' => '7584 Big Canyon Drive',
          'Street2' => '',
          'City' => 'Anaheim',
          'State' => 'California',
          'Country' => 'USA',
          'PostalCode' => '92808',
        ),
        'PaymentInfo' => 
        array (
          'FirstNameOnCard' => 'TEST',
          'LastNameOnCard' => 'Murray',
          'CardNumber' => '4242424242424242',
          'ExpiryDateMonth' => '12',
          'ExpiryDateYear' => '2022',
          'CVV' => '123',
          'Address1' => '7584 Big Canyon Drive',
          'Address2' => '',
          'City' => 'Anaheim',
          'StateProvince' => 'California',
          'Country' => 'USA',
          'PostalCode' => '92808',
        ),
        'InvoiceNotes' => 'Sales Order',
        'IPAddress' => '182.186.82.142',
        'PaymentAmount' => '39.98',
        'OverrideShipping' => 1,
        'ShippingTotal' => '0.00',
        'ShipMethodID' => 2,
      ),
    ),
    'result' => 
      array (
        'ShippingTotal' => '0.00',
        'OrderTotal' => '39.98',
        'CustomerID' => 'R26106503',
        'OrderID' => 13247,
      ),
    );
  */
  //----------------- public functions --------------

  public function ocenture_update(array $args ) {

    require_once( OCENTURE_PLUGIN_DIR . 'lib/Ocenture.php' );

    $ocenture = new Ocenture_Client($this->clientId);

    $user = $args['order']['user']['UserInfo'];
    $billing = $args['order']['product']['BillingAddress'];
    $products = $args['order']['product']['OrderItems'];
    $repNo = $args['order']['user']['RepresentativeNumber'];

    $params = [
      'args' => [
        "ProductCode"   => 'IG7985', //default product code, will be changed below
        "ClientMemberID"  => $args['result']['CustomerID'],
        "FirstName"     => $user['FirstName'],
        "LastName"      => $user['LastName'],
        "Address"     => $billing['Street1'],
        "City"        => $billing['City'],
        "State"       => $billing['State'],
        "Zipcode"     => $billing['PostalCode'],
        "Phone"       => $billing['Phone'], 
        "Email"       => $user['Email'],
        //remove dob for now as it is not mandatory
        //"DOB"       => $user['DateOfBirth'],
        "Gender"      => $user['Gender'],
        "RepID"       => $repNo
      ]
    ];

    

    $results = [];
    $log = [] ;

    foreach ($products as $product) {
      //get ocenture product id by mapping the freedom product id
      $ocentureProductCode = $this->getOcentureProductCode(
          $product['ProductID']
        );

      $params['args']['ProductCode'] = $ocentureProductCode;

      Logger::start($params['args']);

      try {
        
        Logger::log("Request Params");
        Logger::log($params);

        //create account on ocenture
        $result = $ocenture->createAccount($params);

        Logger::log("Ocenture Result");
        Logger::log($result);

        //if the account was successfully created
        if ($result->Status == 'Account Created') {
            Logger::log("Ocenture account created");
            $params['args']['ocenture'] = $result;
            $this->addUser($params['args']);  
        }
      } catch (Exception $e) {
        $result = $e->getMessage();
        Logger::log("Exception: $result");
      }

      $log[] = ['params' => $params, 'result' => $result];
      $results[] = $result;
    }

    /* sample result from ocenture:
    stdClass::__set_state(array(
       'Status' => 'Account Created',
       'ClientMemberID' => 'R25961408',
       'MembershipID' => 111836826,
       'ProductCode' => 'YP8381',
    )),
    */


    
    Logger::close();

    return ['ocenture' => $results];
  }

  /*
  Mapping between ocenture and freedom products
  */

  public function getOcentureProductCode($freedomProductCode) {
    $products = [
      'YSDPROTECHM1MO'  => 'YP8383',
      'YSDPROTECHM1YR'  => 'YP83811',
      'YSDPROIDM1MO'    => 'YP8381',
      'YSDPROIDM1YR'    => 'YP8389',
      'YSDPROIDF1MO'    => 'YP8382',
      'YSDPROIDF1YR'    => 'YP83810',
      'YSDPROIDMP1MO'   => 'YP8387',
      'YSDPROIDMP1YR'   => 'YP83815',
      'YSDPROIDFP1MO'   => 'YP8388',
      'YSDPROIDFP1YR'   => 'YP83816',
      'YSDPROROADM1MO'  => 'YP8384',
      'YSDPROROADM1YR'  => 'YP83812',
      'YSDPROBUNDLM1MO' => 'YP8385',
      'YSDPROBUNDLM1YR' => 'YP83813',
      'YSDPROBUNDLF1MO' => 'YP8386',
      'YSDPROBUNDLF1YR' => 'YP83814',
      'testproduct'     => 'IG7985'
    ];

    if (isset($products[$freedomProductCode])) {
      return $products[$freedomProductCode];
    } else {
      die("no product found matching with ocenture");
    }
  }

  
  

  /*
  add user data including ocenture and freedom ids to wordpress db 
  */
  public function addUser( $userdata ) {
      /* $userdata = [
            [ProductCode] => IG7985
            [ClientMemberID] => R27586351
            [FirstName] => TEST
            [LastName] => MURRAY
            [Address] => Abb
            [City] => Abbivlie
            [State] => AL
            [Zipcode] => 36310
            [Phone] => 3017874905
            [Email] => test563@test.com
            [DOB] => 1938-02-19
            [Gender] => M
            [RepID] => E104509
            [ocenture] => stdClass Object
                (
                    [Status] => Account Created
                    [ClientMemberID] => R27586351
                    [MembershipID] => 111849849
                    [ProductCode] => IG7985
                )
          ]
      */
      Logger::log("Adding ocenture user to db");    

      $ocentureData = $userdata['ocenture'];
      //ocenture sends us following in case of error, please note spelling of error, it should be Error but it is Erorr from Ocenture.
      /*
            stdClass::__set_state(array(
         'Status' => 'Erorr',
         'Erorr' => 'DuplicateRecordFault',
      ))
      */
      
      if ($ocentureData->Status != 'Erorr') 
      {
        try {

          //insert new user as post 
          $postId = wp_insert_post([
              'post_title'  =>  "[{$ocentureData->MembershipID} - {$ocentureData->ProductCode}] {$userdata['FirstName']} {$userdata['LastName']}", 
              'post_type'=>'ocenture',
              'post_status' => 'publish',
          ]);

          //adding user meta, to avoid it showing as custom fields 
          add_post_meta($postId, '_membership_id', $ocentureData->MembershipID);
          add_post_meta($postId, '_product_code', $ocentureData->ProductCode);
          add_post_meta($postId, '_status', $ocentureData->Status == 'Account Created' ? 1 : 0);
          add_post_meta($postId, '_client_member_id', $ocentureData->ClientMemberID); //youngevity id

          add_post_meta($postId, '_rep_id', $userdata['RepID']); 
          add_post_meta($postId, '_fname', $userdata['FirstName']);
          add_post_meta($postId, '_lname', $userdata['LastName']);
          add_post_meta($postId, '_email', $userdata['Email']);
          add_post_meta($postId, '_phone', $userdata['Phone']);
          add_post_meta($postId, '_address', $userdata['Address']);
          add_post_meta($postId, '_city', $userdata['City']);
          add_post_meta($postId, '_state', $userdata['State']);
          add_post_meta($postId, '_zip', $userdata['Zipcode']);
          add_post_meta($postId, '_date', time());

          Logger::log("Ocenture user added to db, post_id : $postId");
          
        } catch (Exception $e) {

        }
      }
  }

  /* manually insert a user into ocenture, used for testing only */
  public function ocenture_insert() {

    Logger::log("inserting user manually into ocenture");
    require_once( OCENTURE_PLUGIN_DIR . 'lib/Ocenture.php' );

    $ocenture = new Ocenture_Client($this->clientId);

    $params = [
      'args' => [
        'ProductCode' => 'YP83815',
        'ClientMemberID' => 'R26107633',
        'FirstName' => 'Francoise ',
        'LastName' => 'Rannis Arjaans',
        'Address' => '801 W Whittier Blvd',
        'City' => 'La Habra',
        'State' => 'CA',
        'Zipcode' => '90631-3742',
        'Phone' => '(562) 883-3000',
        'Email' => 'francoisearjaans@gmail.com',
        'Gender' => 'Female',
        'RepID' => '101269477',
      ]
    ];
    
    Logger::log($params);
    $result = $ocenture->createAccount($params);
    //if ($result->Status == 'Account Created') 
    {
      $params['args']['ocenture'] = $result;
      $this->addUser($params['args']);  
    }
    Logger::log($result);
  
  }
  /////////////// wordpress plugin installation related functions ////////////
  public function install() {
     
  }

  public function uninstall() {

  }

  function update_db_check() {
     
  }
}