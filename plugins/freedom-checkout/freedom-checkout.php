<?php
/*
 * Plugin Name:       Freedom Checkout
 * Description:       Freedom Checkout
 * Version:     			1.0.0
 */


! defined( 'ABSPATH' ) and exit;


if ( ! class_exists( 'FreedomCheckout' ) ) {

	class FreedomCheckout{
		private static $instance;
		public static function instance() {
			if ( ! isset ( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
		public function __construct(){
			global $pagenow, $typenow;
				//checking for max execution time
			$this->include_libs();
			$this->setup_globals();
			$this->setup_hooks();
			if (!session_id()){
				session_start();
			}
		}
		private function include_libs(){

		}
		private function setup_globals() {
			$this->file         = __FILE__;
			$this->basename     = plugin_basename( $this->file );
			$this->plugin_dir   = plugin_dir_path( $this->file );
			$this->plugin_url   = plugin_dir_url ( $this->file );
			set_time_limit(0);
			//define('DEBUG', true);
		}
		private function setup_hooks(){
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' )  , 255);
			add_shortcode( 'freedom_checkout_iframe', array( $this, 'generate_checkout_iframe' ) );
			add_shortcode( 'display_get_params', array( $this, 'display_get_params' ) );
		}
		public function display_get_params( $atts ) {
			return $_GET[$atts['name']];
		}
		public function shortcode_get_referrer_name(){
			return 123;
		}
		public function enqueue_script_admin(){

		}
		public function enqueue_script(){

			//wp_enqueue_script( 'fc-bootstrap', $this->plugin_url . 'source/js/bootstrap.min.js'  );
			wp_enqueue_script( 'fc-storage', $this->plugin_url . 'js/jstorage.js'  );
			wp_register_script( 'fc-core', $this->plugin_url . 'js/core.js', array(), '0.0.14' );
			/*$sponsor_name = '';
			$sponsor_id = '';
			if (isset($_COOKIE['_rep'])){
				$sponsor = get_sponsor_info($_COOKIE['_rep']);
				$sponsor_id =  isset($sponsor['RepDID'])?$sponsor['RepDID']:null;
				$sponsor_name = $sponsor['FirstName']. ' ' . $sponsor['LastName'];
				$sponsor_email = $sponsor['Email'];fre
			}
			wp_localize_script('fc-core', 'fc_referrer_name', $sponsor_name);
			wp_localize_script('fc-core', 'fc_referrer_id', $sponsor_id);
			wp_localize_script('fc-core', 'fc_referrer_email', $sponsor_email);*/

			wp_localize_script('fc-core', 'fc_data_check_url', $this->plugin_url.'source/data-check.php');
			wp_enqueue_script('fc-core');

			wp_enqueue_style( 'fc-style', $this->plugin_url . 'source/css/core.css'  );
			wp_enqueue_style( 'fc-font-roboto',  '//fonts.googleapis.com/css?family=Roboto:400,300,700'  );
			wp_enqueue_style( 'fc-font-awesome',  $this->plugin_url .'source/css/font-awesome.css' );
			//wp_enqueue_style( 'fc-font-bootstrap',  $this->plugin_url .'source/css/bootstrap.min.css' );
			wp_enqueue_style( 'fc-font-bootstrap-responsive',  $this->plugin_url .'source/css/bootstrap-responsive.min.css' );

		}
		public function generate_checkout_iframe( $atts ){
			//$a = shortcode_atts( array('product_id' => 10263 ), $atts );
			//$itemurl = "item-1={$a['product_id']}|1";
			//$url = $this->plugin_url  . 'source/index.php?destroy=1&sponsorid=101651253&'.$itemurl;
			//return "<iframe src=\"{$url}\" frameborder=\"0\" id=\"ygy-iframe\" style=\" height: 400px; width: 100%;\"></iframe>";
			ob_start();
			require_once($this->plugin_dir.'source/index.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}

		public function activate(){


		}
		public function deactivate(){
			
		}
			/*
		Returns products to be in cart
		 */
		public static function get_cart_items(){
			return array('item-1' => 'YSDSERVREP|1' ); // '10263|1' ); 90101DAC
			//return array('item-1' => '90101DAC|1' ); // '10263|1' ); 90101DAC
		}
		/*Redirect page*/
		public function get_redirect_page(){
			return get_permalink(get_page_by_path('sign-up-success'));
		}
		public function sendWelcomeEmail($request) {
			//return true;
			$email = $request['Email'];
	 


			add_filter( 'wp_mail_content_type', array('FreedomCheckout', 'set_html_content_type') );
			$email_template = $this->plugin_dir.'/emails/dac_welcome2.html';
			$message = file_get_contents( $email_template );
			//$message = str_replace('{fc_plugin_url}', $this->plugin_url, $message);


			$message = str_replace(
				array(
					'{REPNAME}', 
					'{REPNUM}', 
					'{REPURL}', 
					'{PASSWORD}'
				),
				array(
					($request['Firstname'] . ' ' . $request['Lastname']), 
					$request['RepNumber'], 
					$request['ReplicatedURL'], 
					$request['Password']),
			 	$message
			);


			$headers = array("From: Youngevity Services <support@youngevityservices.com>");
			wp_mail( $email, 'Welcome To Youngevity Services', $message, $headers);
			remove_filter( 'wp_mail_content_type', array('FreedomCheckout', 'set_html_content_type') );
		}
		public static function set_html_content_type(){
			return 'text/html';
		}

	}

	new FreedomCheckout();
}





