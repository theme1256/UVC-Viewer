<?php
	require_once __DIR__ . "/../config/config.php";

	/**
	 * 
	 */
	class Cameras{
		private $opts = [
			"ssl" => [
				"verify_peer" => false,
				"verify_peer_name" => false,
			],
			"http" => [
				"timeout" => 1
			]
		];
		private $dead = __DIR__ . "/../img/dead.jpg";
		
		function __construct(){
		}

		public function fecth_img(String $cam_id, String $host){
			try{
				$url = "http://" . $host . "/snap.jpeg?cb=" . time();

				$im = @file_get_contents($url, false, stream_context_create($arrContextOptions));

				if(empty($im) || var_export($im, true) == false || json_decode($im)["rc"] == "error"){
					$im = file_get_contents($this->dead);
				} else{
					$cached = __DIR__ . "/../" . $conf->setup->cachepath . $cam_id . ".jpg";
					if(is_file($cached)){
						// Sammenlign det hentede med det gemte
						$prev = file_get_contents($cached);
						if($prev == $im)
							$im = file_get_contents($this->dead);
					} else{
						// Opret filen
						file_put_contents($cached, $im);
					}
				}
			} catch(Exception $ex){
				$im = file_get_contents($this->dead);
			}
			return $im;
		}

		public function fetch_all(){
			global $conf;
			$data = json_decode(
				file_get_contents(
					'https://'.$conf->setup->domain.':'.$conf->setup->port.'/api/2.0/camera?apiKey='.$conf->setup->apiKey, 
					false, 
					stream_context_create($this->opts)
				), 
				true)["data"];
			$cameras = [];
			for($i = 0; $i < count($data); $i++){
				if($data[$i]["managed"])
					$cameras[] = ["id" => $data[$i]["_id"], "name" => $data[$i]["name"], "ip" => $data[$i]["host"]];
			}
			return $cameras;
		}
	}
?>
