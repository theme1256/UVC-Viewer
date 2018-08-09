<?php
	include("include/authcheck.php");
	include("config/config.php");
	header('Content-Encoding: gzip');
	if (isset($_GET["cameraId"])) $cameraId = $_GET["cameraId"];
	else $cameraId = "";
	if (isset($_GET["width"]))$width = $_GET["width"];
	else $width = "720";
	$arrContextOptions = array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);
	if(isset($_GET["cameraId"])){
		try{
			$url = 'https://'.$domain.':'.$port.'/api/2.0/snapshot/camera/'.$cameraId.'?force=true&width='.$width.'&apiKey='.$apiKey;
			$im = @file_get_contents($url, false, stream_context_create($arrContextOptions));
			if(empty($im) || var_export($im, true) == false || json_decode($im)["rc"] == "error"){
				$im = file_get_contents("img/dead.jpg");
			} else{
				$cached = "cached/".$cameraId.".jpg";
				if(is_file($cached)){
					// Sammenlign det hentede med det gemte
					$prev = file_get_contents($cached);
					if($prev == $im)
						$im = file_get_contents("img/dead.jpg");
				} else{
					// Opret filen
					file_put_contents($cached, $im);
				}
			}
		} catch(Exception $ex){
			$im = file_get_contents("img/dead.jpg");
		}
		echo gzencode(base64_encode($im), $compressionLevel);
		// curl_close($ch);
	} else{
		echo "";
	}
?>
