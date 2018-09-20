<?php
class Freedom_Logger {

	public static $filehandle;
	public static $post_content;
	public static $post_id;
	
	public static function pushlog($content) {
		
		self::$post_content .= nl2br($content);

		 $log_post = array(
		      'ID'           => self::$post_id,
		      'post_content' => self::$post_content
		  );

		// Update the post into the database
		  wp_update_post( $log_post );;
	}

	public static function start($request) {
 

		$date = date("Y-m-d H:i:s");

		self::$post_content = nl2br(
			"\n=====================\nnew log : $date \n================\n" 
		);


		$log_post = array(
		  
		  'post_title'    => ($date . " - " . $request['UserInfo']['FirstName'] . " " . $request['UserInfo']['LastName'] . " - " . $request['UserInfo']['Email']),

		  'post_content'  => self::$post_content,
		  
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type'		=> 'freedom_log',
		  'meta_input'	=> [
		  		'_first_name' => $request['UserInfo']['FirstName'],
				'_last_name' => $request['UserInfo']['LastName'],
				'_email' => $request['UserInfo']['Email']
		  	]
		);
			 
			// Insert the post into the database
		self::$post_id = wp_insert_post( $log_post );

		 
		

		/*
		$dir = __DIR__ . "/../";
		if (!is_dir($dir . 'log')) {
			@mkdir($dir . 'log');
			@chmod($dir . 'log', 0775);
		}

		if ($filename) {
			$file = $dir . 'log/' . $filename;
		} else {
			$file = $dir . 'log/' . date("Y-m-d") . '.log.txt';
		}

		self::$filehandle = fopen($file, 'a');
		

		fwrite(self::$filehandle, "\n=====================\nnew log : $date \n================\n");
		*/
	}

	public static function log($log) {
		self::pushlog("\n >> ");
		if (is_array($log) or is_object($log)) {
			self::pushlog( var_export($log, 1) );
		} else {
			self::pushlog( $log );
		}
		
		/*
		fwrite(self::$filehandle, "\n >> ");
		fwrite(self::$filehandle, var_export($log, 1));
		*/
	}

	public static function close() {
		self::pushlog("\n=====================\n Log Closed \n=====================\n");

		/*
		if (self::$filehandle) {
			fwrite(self::$filehandle, 
				"\n=====================\n Log Closed \n=====================\n");

			fclose(self::$filehandle);
			self::$filehandle = false;
		}
		*/
	}
}