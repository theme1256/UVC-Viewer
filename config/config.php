<?php
	// $setUsername = "admin";
	// $setPassword = "5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8";
	// $domain = "host";
	// $port = "7443";
	// $apiKey = "key";
	// $auth = "false";
	// $defaultWidth = "480";
	// $title = "Unifi HTML5 Video";
	// $compressionLevel = "9";
	// $refreshtime = 1000;

	/**
	 * 
	 */
	class Config{
		public $setup = {};
		private $conf_file = "";
		
		function __construct(){
			$this->conf_file = __DIR__ . "/conf.json";

			$json = json_decode(file_get_contents($this->conf_file));
			if($json->setup == true){
				$this->setup = $json;
			} else{
				if(!$this->isItThisPage("setup"))
					header("Location: setup/1");
			}
		}

		public function set($key, $value){
			$this->setup->{$key} = $value;
		}

		/**
		 * Gemmer opsætningen
		 */
		public function save(){
			file_put_contents($this->conf_file, json_encode($this->setup));
		}

		/**
		 * Tjekker om det givne er den side man er på lige nu
		 */
		public function isItThisPage($check){
			if(is_array($check)){
				foreach($check as $site){
					if(strpos($site, "!")){
						$site = str_replace("!", "", $site);
						if($site == $this->pageName() || strpos($this->pageName(), $site))
							return false;
					} else{
						if($site == $this->pageName() || strpos($this->pageName(), $site))
							return true;
					}
				}
				return false;
			} else{
				if($check == $this->pageName() || strpos($this->pageName(), $check))
					return true;
				else
					return false;
			}
		}

		/*
		 * Returnerer navnet på den nuværende fil og hvilken mappe den er i
		 */
		public function pageName(){
			// Finder URL og erstatter / med /dashboard og fjerner .php
			$u = str_replace(".php", "", $_SERVER['REQUEST_URI']);
			// Fjerner det der står efter ?
			if(strpos($u, "?")){
				$U = explode("?", $u);
				$u = $U[0];
			}
			// Fjerner det afsluttende /
			if($u[strlen($u)-1] == "/")
				$u = substr($u, 0, strlen($u)-1);
			return $u;
		}
	}

	$conf = new Config();
?>
