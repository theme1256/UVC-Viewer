<?php
	require_once('config/config.php');

	$arrContextOptions = [
		"ssl" => [
			"verify_peer" => false,
			"verify_peer_name" => false,
		],
		"http" => [
			"timeout" => 1
		]
	];

	$json = file_get_contents('https://'.$domain.':'.$port.'/api/2.0/camera?apiKey='.$apiKey, false, stream_context_create($arrContextOptions));
	$data = json_decode($json, true)["data"];
	$cam_count = count($data);
	$camera = [];
	for($i = 0; $i < $cam_count; $i++){
		if($data[$i]["managed"])
			$camera[] = ["id" => $data[$i]["_id"], "name" => $data[$i]["name"], "ip" => $data[$i]["host"]];
	}
	$cam_count = count($camera);

	usort($camera, function($a, $b){
		return strcmp($a["name"], $b["name"]);
	});

	if($cam_count == 1){
		/*
			x
		*/
		echo "<div class=\"camera grid_1\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"1080\" data-poll=\"true\"></div>".PHP_EOL;
	} elseif(2 <= $cam_count && $cam_count <= 4){
		/*
			 x x
			 x x
		*/
		for($x = 0; $x < count($camera); $x++){
			echo "<div class=\"camera grid_2 layer_1 ".($x%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
		}
	} elseif(5 <= $cam_count && $cam_count <= 6){
		/*
			 x   x
			     x
			 x x x
		*/
		for($x = 0; $x < count($camera); $x++){
			if($x == 0)
				echo "<div class=\"camera grid_3 layer_1 r\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
			else
				echo "<div class=\"camera grid_3 layer_2 ".(($x <= 2 || $x == 5) ? "l" : "r")."\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
		}
	} elseif($cam_count == 7){
		/*
			|x |x|x|
			|__|x|x|
			|x | x |
			|  |   |
		*/
		for($x = 0; $x < count($camera); $x++){
			if($x == 1){
				echo "<div class=\"grid_2 layer_1 l\">".PHP_EOL;
				for($i = 0; $i < 4; $i++){
					echo "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x+$i]['id']."\" data-ip=\"".$camera[$x+$i]['ip']."\" data-name=\"".$camera[$x+$i]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
				echo "</div>".PHP_EOL;
				$x += 3;
			} else{
				echo "<div class=\"camera grid_2 layer_1 ".($x == 6 ? "l" : "r")."\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
			}
		}
	} elseif(8 <= $cam_count && $cam_count <= 9){
		/*
			 x x x
			 x x x
			 x x x
		*/
		for($x = 0; $x < count($camera); $x++){
			echo "<div class=\"camera grid_3 layer_2 l\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
		}
	} elseif($cam_count == 10){
		/*
			 x  x x
			    x x
			 x  x x
			    x x
		*/
		for($x = 0; $x < count($camera); $x++){
			if($x == 1 || $x >= 6){
				echo "<div class=\"grid_2 layer_1 l\">".PHP_EOL;
				for($i = 0; $i < 4; $i++){
					if(!empty($camera[$x]['id']))
						echo "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x+$i]['id']."\" data-ip=\"".$camera[$x+$i]['ip']."\" data-name=\"".$camera[$x+$i]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
				echo "</div>".PHP_EOL;
				$x += 3;
			} else{
				echo "<div class=\"camera grid_2 layer_1 r\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
			}
		}
	} elseif(11 <= $cam_count && $cam_count <= 13){
		/*
			 x  x x
			    x x
			x x x x
			x x x x
		*/
		for($x = 0; $x < count($camera); $x++){
			if($x == 0){
				echo "<div class=\"camera grid_2 layer_1 r\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
			} else{
				echo "<div class=\"grid_2 layer_1 ".($x == 5 ? "r" : "l")."\">".PHP_EOL;
				for($i = 0; $i < 4; $i++){
					if(!empty($camera[$x]['id']))
						echo "<div class=\"camera grid_2 layer_2 ".($i%2 == 0 ? "r" : "l")."\" data-cameraid=\"".$camera[$x+$i]['id']."\" data-ip=\"".$camera[$x+$i]['ip']."\" data-name=\"".$camera[$x+$i]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
				echo "</div>".PHP_EOL;
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
		for($x = 0; $x < count($camera); $x++){
			echo "<div class=\"camera grid_4 layer_2\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
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
		for($x = 0; $x < count($camera); $x++){
			if($x == 0){
				echo "<div class=\"camera grid_2 layer_1 r\" data-cameraid=\"".$camera[$x]['id']."\" data-ip=\"".$camera[$x]['ip']."\" data-name=\"".$camera[$x]['name']."\" data-width=\"720\" data-poll=\"true\"></div>".PHP_EOL;
			} else{
				echo "<div class=\"grid_2 layer_1\">".PHP_EOL;
				for($i = 0; $i < 9; $i++){
					if(!empty($camera[$x]['id']))
						echo "<div class=\"camera grid_3 layer_2 l\" data-cameraid=\"".$camera[$x+$i]['id']."\" data-ip=\"".$camera[$x+$i]['ip']."\" data-name=\"".$camera[$x+$i]['name']."\" data-width=\"480\" data-poll=\"true\"></div>".PHP_EOL;
				}
				echo "</div>".PHP_EOL;
				$x += 8;
			}
		}
	} else{
		echo "Only 1 through 20 cameras supported";
	}

	echo "<script type='text/javascript'>var cam_count = ".$cam_count.";</script>";

?>
