<?php
namespace Ocenture;

class Logger {

	public static $filehandle;
	public static $post_title = "";
	public static $post_content = "";
	public static $post_id;
	
	public static function pushlog($content) {
		
		self::$post_content .= nl2br($content);

		$log_post = array(
		  'ID'           => self::$post_id,
		  'post_content' => self::$post_content
		);

		// Update the post into the database
		wp_update_post( $log_post );
	}

	public static function start( $data = null ) {
		
		$title_separtor = ') - ';

		//Scenario 1: 
		//if post id already exists that means log was already started, so just update its title and return

		if (self::$post_id) {
			//update title of the post and update the content, add product code in the title

			self::$post_title = str_replace($title_separtor, ", {$data['ProductCode']}" . $title_separtor , self::$post_title);


			$log_post = array(
			  'ID'           => self::$post_id,
			  'post_title' => self::$post_title
			);

			// Update the post into the database
			wp_update_post( $log_post );

			return ;
		}

		//Scenario 2:
		// log is being created first time
		$date = date("Y-m-d H:i:s");

		if ( is_array($data) or is_object($data) ) {
			//data is the array or object having customer info
			$log = "New Log";
		} else {
			//data is string, simple log
			$log = $data;
		}

		self::$post_content = nl2br(
			"\n=====================\n$log : $date \n================\n" 
		);


		if ( is_array($data) or is_object($data) ) {
			//log was object or array

			self::$post_title = ($date . " - (" . $data['ProductCode'] . $title_separtor .
			  		 $data['FirstName'] . " " . $data['LastName'] . " - " . $data['Email']);

			$log_post = [
			  'post_title'    => self::$post_title,
			  'post_content'  => self::$post_content,
			  'post_status'   => 'publish',
			  'post_author'   => 1,
			  'post_type'		=> 'ocenture_log',
			  'meta_input'	=> [
			  		'_first_name' => $data['UserInfo']['FirstName'],
					'_last_name' => $data['UserInfo']['LastName'],
					'_email' => $data['UserInfo']['Email']
			  	]
			];
		} else {
			
			self::$post_title = ($date . " - " . $log);

			$log_post = [
			  'post_title'    => self::$post_title,
			  'post_content'  => self::$post_content,
			  'post_status'   => 'publish',
			  'post_author'   => 1,
			  'post_type'		=> 'ocenture_log',
			];
		}
			 
		// Insert the post into the database
		self::$post_id = wp_insert_post( $log_post );
		 
	}

	public static function log($log) {
		self::pushlog("\n >> ");
		if (is_array($log) or is_object($log)) {
			self::pushlog( var_export($log, 1) );
		} else {
			self::pushlog( $log );
		}
		
	}

	public static function close() {
		self::pushlog("\n=====================\n Log Closed \n=====================\n");
	}
}