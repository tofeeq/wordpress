<?php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\BadResponseException;

class Freedom_Client {
	
	protected static $_client;
	protected static $_config;
	protected static $_authConfig;
	protected static $pluginOptions;

	protected static $endpoint;
	protected static $key;

	public static $debug = false;

	public static function config() {
		if (!self::$_config) {
			//get options from wordpress
			self::$pluginOptions = get_option('freedom-settings');


			self::$_config = array(

				"api_end_point" => self::getEndpoint(),

				"headers" => [
					"content-type" => 'application/json',
					"Accept" => 'application/json',
					"User-Agent"	=> "Testing",
					"Ocp-Apim-Subscription-Key" => self::getKey()
				]
			);
		}

		return self::$_config;
	}

	public static function getEndpoint() {

		if (!self::$endpoint) {
			if (self::$pluginOptions['mode'] == 'sandbox') {
				self::$endpoint = "https://ygyapi.azure-api.net/sandbox/ERP/api/";
			} else {
				self::$endpoint = "https://ygyapi.azure-api.net/ERP/api/";
			}
		}
		return self::$endpoint;
	}
	
	public static function getKey() {

		if (!self::$key) {
			self::$key = self::$pluginOptions['key'];
		}
		return self::$key;
	}

	public static function setHeaders(&$params) {
		if (!$params) {
			$params = [];
		}

		$config = self::config();
		

		if (isset($config['headers']) && $config['headers']) {
			foreach ($config['headers'] as $key => $val ) {
				$params['headers'][$key] = $val;
			}
		}
 
		if (isset($config['authorization']) && $config['authorization']) {

			switch ($config['authorization']) {

				case 'basic':
						$credentials = base64_encode(
							$config['api_key'] 
							. ':' 
							. $config['api_secret']
						);



						$params['headers']["Authorization"] = "Basic " . $credentials;
					break;

				case 'digest':
						
					break;

			 
				case 'defualt':
						$params['auth'] = [
							$config['api_key'], 
							$config['api_secret']
						];
					break;
				

			}
		}
	}

	public static function client() {
		$config = self::config();
		
		if (!self::$_client) {
			self::$_client = new Client([// Base URI is used with relative requests
			    'base_uri' => $config['api_end_point'],
			    // You can set any number of default request options.
			    'timeout'  => 20.0
			   ]);	
		}

		return self::$_client;
	}
	

	public static function ip() {
		$ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
	    {
	        if (array_key_exists($key, $_SERVER) === true)
	        {
	            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
	            {
	                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
	                {
	                    return $ip;
	                }
	            }
	        }
	    }
	}

	public static function setDebug(&$query) {
		if (self::$debug) {
			//var_export($request);
			$handler = new CurlHandler();
			$stack = HandlerStack::create($handler); // Wrap w/ middleware

			$tapMiddleware = Middleware::tap(function ($request) {
			    echo $request->getHeaderLine('Content-Type');
			    echo "\n";
			    // application/json
			    echo $request->getBody();
			    echo "\n";
			    // {"foo":"bar"}
			});

			$query['handler'] = $tapMiddleware($stack);
		}
	}

	private static function _request($method, $endpoint, &$query) {

		self::setHeaders($query);

		self::setDebug($query);

		try {
			if (class_exists('Freedom_Logger')) {
				//Freedom_Logger::log("api request start");
				//Freedom_Logger::log($endpoint);
				//Freedom_Logger::log($query);
			}
			$response = self::client()->request($method, $endpoint, $query);

			return self::parseResponse($response);

		} catch (RequestException $e) {

		    echo Psr7\str($e->getRequest());
		    
		    if ($e->hasResponse()) {
		        echo Psr7\str($e->getResponse());
		    }
		} catch (BadResponseException $e) {
			throw new Exception($e->getResponse()->getBody()->getContents());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public static function request($method = 'GET', $endpoint, $params = null) {

		$query = [
				'query'	=> $params
			];

		return self::_request($method, $endpoint, $query);
	}


	public static function json($method = 'GET', $endpoint, $params = null) {
		$query = [
				'json'	=> $params
			];
		return self::_request($method, $endpoint, $query);
	}


	public static function parseResponse($response) {

		$code = $response->getStatusCode(); // 200
		$reason = $response->getReasonPhrase(); // OK
		
		if ($code == 200) {
			$body = $response->getBody();
			$json = $body->getContents();
		
			return json_decode($json);
		} else {
			throw new Exception($reason);				
		}

	}
}
