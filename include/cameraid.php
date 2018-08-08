<?php
include('config/config.php');
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
$url = 'https://'.$domain.':'.$port.'/api/2.0/camera?apiKey='.$apiKey;
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
$data = json_decode($json, true)["data"];
$max = count($data) - 1;
for ($i = 0; $i <= $max; $i++) {
echo "\t<b>Name:</b> ".json_decode($json, true)["data"][$i]["name"]."<br /><b>ID:</b> ".json_decode($json, true)["data"][$i]["_id"]."<br /><br />\n\r";
}
?>
