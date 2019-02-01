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

		public function selected($id){
			global $conf;
			foreach($conf->setup->cameras as $cam){
				if($cam->id == $id)
					return true;
			}
			return false;
		}

		public function fetch_all(){
			global $conf;
			$cameras = [];
			if($conf->setup->unifi->version == "unifi-video"):
				$data = json_decode(
					file_get_contents(
						'https://'.$conf->setup->domain.':'.$conf->setup->port.'/api/2.0/camera?apiKey='.$conf->setup->apiKey, 
						false, 
						stream_context_create($this->opts)
					), 
					true)["data"];
				for($i = 0; $i < count($data); $i++){
					if($data[$i]["managed"])
						$cameras[] = ["id" => $data[$i]["_id"], "name" => $data[$i]["name"], "ip" => $data[$i]["host"]];
				}
			elseif($conf->setup->unifi->version == "unifi-protect"):
				$auth = $this->get_auth_string();
				if(empty($auth))
					die("Couldn't sign in to CloudKey, no auth-key returned");

				$url = "https://".$conf->setup->domain.":".$conf->setup->port."/api/bootstrap";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer ".$auth]);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				$rsp = curl_exec($ch);
				// $info = curl_getinfo($ch);
				$er = curl_error($ch);
				if(!empty($er))
					die("Connection to CloudKey could not be established, error: " . $er);
				curl_close($ch);
				$data = json_decode($rsp)->cameras;
				for($i = 0; $i < count($data); $i++){
					$cameras[] = ["id" => $data[$i]->id, "name" => $data[$i]->name, "ip" => $data[$i]->host];
				}
			endif;
			usort($cameras, function($a, $b){
				return strcmp($a["name"], $b["name"]);
			});
			return $cameras;
		}

		private function get_auth_string(){
			global $conf;
			$url = "https://".$conf->setup->domain.":".$conf->setup->port."/api/auth";
			$data = ["username" => $conf->setup->unifi->username, "password" => $conf->setup->unifi->password];
			$data_string = json_encode($data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			$response = curl_exec($ch);
			// $info = curl_getinfo($ch);
			$er = curl_error($ch);
			if(!empty($er))
				die("Connection to CloudKey could not be established, error: " . $er);
			$headers = array_filter(explode("\r\n", substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE))));
			$body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
			curl_close($ch);
			foreach($headers as $header){
				if(preg_match("/^Authorization:/", $header))
					return trim(str_replace("Authorization:", "", $header));
			}
			return "";
		}

		public function view_grid(int $count){
			$output = "";

			if($count == 1){
				/*
					x
				*/
				$output .= "<div class=\"camera grid_1\" data-sort=\"1\" data-width=\"1080\"></div>".PHP_EOL;
			} elseif(2 <= $count && $count <= 4){
				/*
					 x x
					 x x
				*/
				$count = 4;
				for($x = 0; $x < $count; $x++){
					$output .= "<div class=\"camera grid_2 layer_1 ".($x%2 == 0 ? "r" : "l")."\" data-sort=\"{$x}\" data-width=\"720\"></div>".PHP_EOL;
				}
			} elseif(5 <= $count && $count <= 6){
				/*
					 x   x
					     x
					 x x x
				*/
				$count = 6;
				for($x = 0; $x < $count; $x++){
					if($x == 0)
						$output .= "<div class=\"camera grid_3 layer_1 r\" data-sort=\"{$x}\" data-width=\"720\"></div>".PHP_EOL;
					else
						$output .= "<div class=\"camera grid_3 layer_2 ".(($x <= 2 || $x == 5) ? "l" : "r")." data-sort=\"{$x}\" data-width=\"480\"></div>".PHP_EOL;
				}
			} elseif($count == 7){
				/*
					|x |x|x|
					|__|x|x|
					|x | x |
					|  |   |
				*/
				for($x = 0; $x < $count; $x++){
					if($x == 1){
						$output .= "<div class=\"grid_2 layer_1 l\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							$output .= "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-sort=\"".($x+$i)."\" data-width=\"480\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					} else{
						$output .= "<div class=\"camera grid_2 layer_1 ".($x == 6 ? "l" : "r")."\" data-sort=\"{$x}\" data-width=\"720\"></div>".PHP_EOL;
					}
				}
			} elseif(8 <= $count && $count <= 9){
				/*
					 x x x
					 x x x
					 x x x
				*/
				$count = 9;
				for($x = 0; $x < $count; $x++){
					$output .= "<div class=\"camera grid_3 layer_2 l\" data-sort=\"{$x}\" data-width=\"480\"></div>".PHP_EOL;
				}
			} elseif($count == 10){
				/*
					 x  x x
					    x x
					 x  x x
					    x x
				*/
				for($x = 0; $x < $count; $x++){
					if($x == 1 || $x >= 6){
						$output .= "<div class=\"grid_2 layer_1 l\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							if(!empty($camera[$x]->id))
								$output .= "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-sort=\"".($x+$i)."\" data-width=\"480\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					} else{
						$output .= "<div class=\"camera grid_2 layer_1 r\" data-sort=\"{$x}\" data-width=\"720\"></div>".PHP_EOL;
					}
				}
			} elseif(11 <= $count && $count <= 13){
				/*
					 x  x x
					    x x
					x x x x
					x x x x
				*/
				$count = 13;
				for($x = 0; $x < $count; $x++){
					if($x == 0){
						$output .= "<div class=\"camera grid_2 layer_1 r\" data-sort=\"{$x}\" data-width=\"720\"></div>".PHP_EOL;
					} else{
						$output .= "<div class=\"grid_2 layer_1 ".($x == 5 ? "r" : "l")."\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							if(!empty($camera[$x]->id))
								$output .= "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-sort=\"".($x+$i)."\" data-width=\"480\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					}
				}
			} elseif(14 <= $count && $count <= 16){
				/*
					x x x x
					x x x x
					x x x x
					x x x x
				*/
				$count = 16;
				for($x = 0; $x < $count; $x++){
					$output .= "<div class=\"camera grid_4 layer_2\" data-sort=\"{$x}\" data-width=\"480\"></div>".PHP_EOL;
				}
			} elseif(17 <= $count && $count <= 20){
				/*
					 x    x x x
					      x x x
					      x x x
					 x    x x x
					      x x x
					      x x x
				*/
				$count = 20;
				for($x = 0; $x < $count; $x++){
					if($x == 0 || $x == 10){
						$output .= "<div class=\"camera grid_2 layer_1 r\" data-sort=\"{$x}\" data-width=\"720\"></div>".PHP_EOL;
					} else{
						$output .= "<div class=\"grid_2 layer_1\">".PHP_EOL;
						for($i = 0; $i < 9; $i++){
							if(!empty($camera[$x]->id))
								$output .= "<div class=\"camera grid_3 layer_2 l\" data-sort=\"".($x+$i)."\" data-width=\"480\"></div>".PHP_EOL;
						}
						$output .= "</div>".PHP_EOL;
						$x += 8;
					}
				}
			} else{
				$output .= "Only 1 through 20 cameras supported";
			}

			$output .= "<input type=\"hidden\" name=\"grid_size\" value=\"{$count}\"/>";

			return $output;
		}

		private function format_out(String $class, $cam_id, $cam_ip, $cam_name, int $width, String $poll = "true"){
			return "<div class=\"camera {$class}\" data-cameraid=\"{$cam_id}\" data-ip=\"$cam_ip\" data-name=\"$cam_name\" data-width=\"{$width}\" data-poll=\"{$poll}\"></div>".PHP_EOL;
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
				$output .= $this->format_out("grid_1", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 1080);
			} elseif(2 <= $cam_count && $cam_count <= 4){
				/*
					 x x
					 x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					$output .= $this->format_out("grid_2 layer_1 ".($x%2 == 0 ? "r" : "l"), $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 720);
				}
			} elseif(5 <= $cam_count && $cam_count <= 6){
				/*
					 x   x
					     x
					 x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					if($x == 0)
						$output .= $this->format_out("grid_3 layer_1 r", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 720);
					else
						$output .= $this->format_out("grid_3 layer_2 ".(($x <= 2 || $x == 5) ? "l" : "r"), $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 480);
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
							$output .= $this->format_out("grid_2 layer_2 ".($i%2 == 0 ? "r" : "l"), $camera[$x+$i]->id, $camera[$x+$i]->ip, $camera[$x+$i]->name, 480);
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					} else{
						$output .= $this->format_out("grid_2 layer_1 ".($x == 6 ? "l" : "r"), $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 720);
					}
				}
			} elseif(8 <= $cam_count && $cam_count <= 9){
				/*
					 x x x
					 x x x
					 x x x
				*/
				for($x = 0; $x < $cam_count; $x++){
					$output .= $this->format_out("grid_3 layer_2 l", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 480);
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
								$output .= $this->format_out("grid_2 layer_2 ".($i%2 == 0 ? "r" : "l"), $camera[$x+$i]->id, $camera[$x+$i]->ip, $camera[$x+$i]->name, 480);
						}
						$output .= "</div>".PHP_EOL;
						$x += 3;
					} else{
						$output .= $this->format_out("grid_2 layer_1 r", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 720);
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
						$output .= $this->format_out("grid_2 layer_1 r", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 720);
					} else{
						$output .= "<div class=\"grid_2 layer_1 ".($x == 5 ? "r" : "l")."\">".PHP_EOL;
						for($i = 0; $i < 4; $i++){
							if(!empty($camera[$x]->id))
								$output .= $this->format_out("grid_2 layer_2 ".($i%2 == 0 ? "r" : "l"), $camera[$x+$i]->id, $camera[$x+$i]->ip, $camera[$x+$i]->name, 480);
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
					$output .= $this->format_out("grid_4 layer_2", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 480);
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
					if($x == 0 || $x == 10){
						$output .= $this->format_out("grid_2 layer_1 r", $camera[$x]->id, $camera[$x]->ip, $camera[$x]->name, 720);
					} else{
						$output .= "<div class=\"grid_2 layer_1\">".PHP_EOL;
						for($i = 0; $i < 9; $i++){
							if(!empty($camera[$x]->id))
								$output .= $this->format_out("grid_3 layer_2 l", $camera[$x+$i]->id, $camera[$x+$i]->ip, $camera[$x+$i]->name, 480);
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
