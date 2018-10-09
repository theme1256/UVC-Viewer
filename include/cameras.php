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

		public function fetch_img(String $cam_id, $host){
			global $conf;
			if(is_null($host))
				return "";
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
			usort($cameras, function($a, $b){
				return strcmp($a["name"], $b["name"]);
			});
			return $cameras;
		}

		public function view(Array $camera){
			usort($camera, function($a, $b){
				return strcmp($a->sort, $b->sort);
			});
			$cam_count = count($camera);
			$output = "";

			if($cam_count == 1){
				/*
					x
				*/
				$output .= "<div class=\"camera grid_1\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"1080\" data-poll=\"true\"></div>".PHP_EOL;
			} elseif(2 <= $cam_count && $cam_count <= 4){
				/*
					 x x
					 x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					$output .= "<div class=\"camera grid_2 layer_1 ".($x%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
				}
			} elseif(5 <= $cam_count && $cam_count <= 6){
				/*
					 x   x
					     x
					 x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					if($x == 0)
						$output .= "<div class=\"camera grid_3 layer_1 r\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
					else
						$output .= "<div class=\"camera grid_3 layer_2 ".(($x <= 2 || $x == 5) ? "l" : "r")."\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
			} elseif($cam_count == 7){
				/*
					|x |x|x|
					|__|x|x|
					|x | x |
					|  |   |
				*/
				for($x = 0; $x < $cam_count; $x++){
					if($x == 1){
						$output .= "<div class=\"grid_2 layer_1 l\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							$output .= "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x+$i]->id."\" data-ip=\"".$camera[$x+$i]->ip."\" data-name=\"".$camera[$x+$i]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					} else{
						$output .= "<div class=\"camera grid_2 layer_1 ".($x == 6 ? "l" : "r")."\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
					}
				}
			} elseif(8 <= $cam_count && $cam_count <= 9){
				/*
					 x x x
					 x x x
					 x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					$output .= "<div class=\"camera grid_3 layer_2 l\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
			} elseif($cam_count == 10){
				/*
					 x  x x
					    x x
					 x  x x
					    x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					if($x == 1 || $x >= 6){
						$output .= "<div class=\"grid_2 layer_1 l\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							if(!empty($camera[$x]->id))
								$output .= "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x+$i]->id."\" data-ip=\"".$camera[$x+$i]->ip."\" data-name=\"".$camera[$x+$i]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					} else{
						$output .= "<div class=\"camera grid_2 layer_1 r\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
					}
				}
			} elseif(11 <= $cam_count && $cam_count <= 13){
				/*
					 x  x x
					    x x
					x x x x
					x x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					if($x == 0){
						$output .= "<div class=\"camera grid_2 layer_1 r\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
					} else{
						$output .= "<div class=\"grid_2 layer_1 ".($x == 5 ? "r" : "l")."\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							if(!empty($camera[$x]->id))
								$output .= "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x+$i]->id."\" data-ip=\"".$camera[$x+$i]->ip."\" data-name=\"".$camera[$x+$i]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					}
				}
			} elseif(14 <= $cam_count && $cam_count <= 16){
				/*
					x x x x
					x x x x
					x x x x
					x x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					$output .= "<div class=\"camera grid_4 layer_2\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
			} elseif(17 <= $cam_count && $cam_count <= 20){
				/*
					 x    x x x
					      x x x
					      x x x
					 x    x x x
					      x x x
					      x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					if($x == 0){
						$output .= "<div class=\"camera grid_2 layer_1 r\" data-cameraid=\"".$camera[$x]->id."\" data-ip=\"".$camera[$x]->ip."\" data-name=\"".$camera[$x]->name."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
					} else{
						$output .= "<div class=\"grid_2 layer_1\">".PHP_EOL;
						for($i = 0; $i < 9; $i++){
							if(!empty($camera[$x]->id))
								$output .= "<div class=\"camera grid_3 layer_2 l\" data-cameraid=\"".$camera[$x+$i]->id."\" data-ip=\"".$camera[$x+$i]->ip."\" data-name=\"".$camera[$x+$i]->name."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 8;
					}
				}
			} else{
				$output .= "Only 1 through 20 cameras supported";
			}

			$output .= "<script type='text/javascript'>var cam_count = ".$cam_count.";</script>";

			return $output;
		}
	}
?>
