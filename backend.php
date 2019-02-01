<?php
	session_start();
	require_once __DIR__ . "/config/config.php";

	$action = @$_POST["action"];

	if($action == "login"){
		if(empty($_POST["username"])){
			$_SESSION["error"] = "Missing username";
		} elseif(empty($_POST["password"])){
			$_SESSION["error"] = "Missing password";
		} else{
			$step = (!isset($_POST["step"]) ? $conf->first_step : str_replace(".php", "", $_POST["step"]));
			if(hash('sha256', $_POST["password"]) == $conf->setup->auth->password && $_POST["username"] == $conf->setup->auth->username)
				$_SESSION["login"] = true;
			else
				$_SESSION["error"] = "Username or password not matching";
		}
		header("Location: setup/" . $step);
	} elseif($action == "step-0"){
		if(empty($_POST["nvr-ip"])){
			$_SESSION["error"] = "Missing NVR IP";
		} elseif(empty($_POST["nvr-port"])){
			$_SESSION["error"] = "Missing NVR port";
		} elseif(empty($_POST["site-title"])){
			$_SESSION["error"] = "Missing site title";
		} elseif(empty($_POST["version"])){
			$_SESSION["error"] = "Missing NVR version";
		} else{
			$conf->set("domain", $_POST["nvr-ip"]);
			$conf->set("port", $_POST["nvr-port"]);
			$conf->set("title", $_POST["site-title"]);
			$conf->set("unifi", (object)["version" => $_POST["version"], "username" => "", "password" => "", "apiKey" => ""]);
			$conf->save();
		}
		header("Location: setup/1");
	} elseif($action == "step-1"){
		if(empty($_POST["nvr-username"]) && $conf->setup->unifi->version == "unifi-protect"){
			$_SESSION["error"] = "Missing NVR username";
		} elseif(empty($_POST["nvr-password"]) && $conf->setup->unifi->version == "unifi-protect"){
			$_SESSION["error"] = "Missing NVR password";
		} elseif(empty($_POST["nvr-api-key"]) && $conf->setup->unifi->version == "unifi-video"){
			$_SESSION["error"] = "Missing API-key";
		} elseif(empty($_POST["username"])){
			$_SESSION["error"] = "Missing username";
		} elseif(empty($_POST["password"])){
			$_SESSION["error"] = "Missing password";
		} else{
			if($conf->setup->unifi->version == "unifi-protect")
				$conf->set("unifi", (object)["version" => $conf->setup->unifi->version, "username" => $_POST["nvr-username"], "password" => $_POST["nvr-password"], "apiKey" => ""]);
			elseif($conf->setup->unifi->version == "unifi-video")
				$conf->set("unifi", (object)["version" => $conf->setup->unifi->version, "username" => "", "password" => "", "apiKey" => $_POST["nvr-api-key"]]);
			$conf->set("auth", (object)["username" => $_POST["username"], "password" => hash('sha256', $_POST["password"])]);
			$conf->save();
		}
		header("Location: setup/2");
	} elseif($action == "step-2"){
		// Save the list of cameras to display
		$cams = [];
		require_once __DIR__ . "/include/cameras.php";
		$cam = new Cameras();
		foreach($cam->fetch_all() as $c){
			if(in_array($c["id"], $_POST['camshow']))
				$cams[] = (object)["id" => $c["id"],"name" => $c["name"],"ip" => $c["ip"],"sort" => count($cams)];
		}
		$conf->set("cameras", $cams);
		$conf->save();
		header("Location: setup/3");
	} elseif($action == "step-3"){
		// Save the view (sort), compressionrate and refresh time
		$cams_tmp = [];
		$cams = [];
		require_once __DIR__ . "/include/cameras.php";
		$cam = new Cameras();
		foreach($cam->fetch_all() as $c){
			if(isset($_POST["camshow_" . $c["id"]]))
				$cams_tmp[] = (object)["id" => $c["id"], "name" => $c["name"], "ip" => $c["ip"], "sort" => $_POST["camshow_" . $c["id"]]];
		}
		for($i = 0; $i < $_POST['grid_size']; $i++){ 
			$c = 0;
			foreach($cams_tmp as $cam){
				if($cam->sort == $i)
					$c++;
			}
			if($c == 0){
				$cams[] = (object)["id" => "blank_".$i, "name" => "blank_".$i, "ip" => $_SERVER['SERVER_ADDR'], "sort" => $i];
			} elseif($c == 1){
				foreach($cams_tmp as $cam){
					if($cam->sort == $i)
						$cams[] = $cam;
				}
			} else{
				header("Location: setup/3");
				exit;
			}
		}
		$conf->set("cameras", $cams);
		$conf->set("refreshtime", (empty($_POST["refreshtime"]) ? 1000 : $_POST["refreshtime"]));
		$conf->save();
		header("Location: setup/4");
	} elseif($action == "step-4"){
		// Save the setup
		$conf->set("setup", true);
		$conf->save();
		header("Location: view");
	}
?>