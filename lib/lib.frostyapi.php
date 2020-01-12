<?php

	// This is a new feature I'm working on to centralise some functions to https://api.frostytrading.com
	// This central API can be used by all the Frostybots for various future enhancements, such as 
	// OHLCV data, versioning, usage statistics, backtesting, notifications, trade analytics etc, but for
	// now it's only providing OHLCV data for Deribit and Bitmex, since the Deribit API does not provide 
	// OHLCV data and the Bitmex API is stupid...

	const frostyapiurl = 'https://api.frostytrading.com';    

	// Frosty API Plugin Wrapper

	class apiPlugin extends stdclass {
		
		private $server;
		private $plugin;
		private $method;
		private $cmd;
		
		public function __construct($server, $plugin) {
			$this->server = $server;
			$this->plugin = $plugin;
		}
		
		public function __call($method, $args) {
			$this->method = $method;
			$params = [];
			$params['cmd'] = $this->plugin.':'.$this->method;
			if (isset($args[0])) {
				foreach ($args[0] as $argName => $argVal) {
					$params[$argName] = $argVal;
				}
			}
			$result = $this->_curl_get($params);

			$decoded = @json_decode($result);
			if (!is_null($decoded)) {
				return $decoded;
			}
			return false;		
		}
		
		
		private function _curl_get($get = NULL, $options = []) {    
			$defaults = array( 
				CURLOPT_URL => $this->server. (strpos($this->server, '?') === FALSE ? '?' : ''). http_build_query($get), 
				CURLOPT_HEADER => 0, 
				CURLOPT_TIMEOUT => 60, 
				CURLOPT_RETURNTRANSFER => TRUE
			); 
    
			$ch = curl_init(); 
			curl_setopt_array($ch, ($options + $defaults)); 
			if( ! $result = curl_exec($ch)) { 
				trigger_error(curl_error($ch), E_USER_ERROR); 
			} 
			curl_close($ch); 
			return $result; 
		} 
		
	}	

	// FrostyAPI Class

	class FrostyAPI {

		private $server;

		public function __construct($server = null) {
			$this->server = (is_null($server) ? frostyapiurl : $server);
			return false;
		}

		public function __get($plugin) {
			return new apiPlugin($this->server, $plugin);
		}
		

	}



?>