<?php
/**
 * @package Ocenture
 */
/*
Plugin Name: Ocenture 
Plugin URI: Direct Sales Forge
Description: Users and Order management at Ocenture.
Version: 1.0.0
Author: Direct Sales Forge
Author URI: Direct Sales Forge
License: GPLv2 or later
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

//autoload to include lib files specific to ocenture
spl_autoload_register(function($class) {
	if (strpos($class, 'Ocenture') !== false) {
	    $file = str_replace(['Ocenture\\', '\\'], [ __DIR__ . '/lib/', '/'], $class) . ".php";

	    if (file_exists($file) ) {
	    	require_once($file);
	    } else {
	    	echo "file $file not found"; 
	    	die;
	    }

	}
});



define( 'OCENTURE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OCENTURE_VIEWS_DIR', plugin_dir_path( __FILE__ ) . 'views/' );

register_activation_hook( __FILE__, array( 'Ocenture', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Ocenture', 'plugin_deactivation' ) );

require_once( OCENTURE_PLUGIN_DIR . 'class.ocenture.php' );

//incase widgets are needed
//require_once( OCENTURE_PLUGIN_DIR . 'class.ocenture-widget.php' );




$ocenture = new Ocenture();

add_action( 'init', array( $ocenture, 'init' ) );

if ( is_admin() ) {
	require_once( OCENTURE_PLUGIN_DIR . 'class.ocenture-admin.php' );
	$ocentureAdmin = new OcentureAdmin();
	add_action( 'init', array($ocentureAdmin, 'init' ) );
	add_action( 'admin_init', array($ocentureAdmin, 'admin_init' ) );
}