<?php
include('config/config.php');
class Camera
{
public $id;
public $width;
}
$i = 0;
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
$url = 'https://'.$domain.':'.$port.'/api/2.0/camera?apiKey='.$apiKey;
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
$cameraCount = count(json_decode($json, true)["data"]) - 1;
for ($i = 0; $i <= $cameraCount; $i++) {
	$camera[$i] = new Camera;
	$camera[$i]->id = json_decode($json, true)["data"][$i]["_id"];
	$camera[$i]->width = $defaultWidth;
	}
$max = count($camera) - 1;
for ($x = 0; $x <= $max; $x++) {
	echo "\t<div class=\"camera grid\" data-cameraid=\"".$camera[$x]->id."\" data-width=\"".$camera[$x]->width."\" data-poll=\"true\"></div>\r\n";
} 
?>
