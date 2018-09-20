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
			if(hash('sha256', $_POST["password"]) == $conf->setup->auth->password && $_POST["username"] == $conf->setup->auth->username)
				$_SESSION["login"] = true;
			else
				$_SESSION["error"] = "Username or password not matching";
		}
		header("Location: setup/" . $step);
	} elseif($action == "step-1"){
		if(empty($_POST["nvr-ip"])){
			$_SESSION["error"] = "Missing NVR IP";
		} elseif(empty($_POST["nvr-port"])){
			$_SESSION["error"] = "Missing NVR port";
		} elseif(empty($_POST["site-title"])){
			$_SESSION["error"] = "Missing site title";
		} elseif(empty($_POST["nvr-api-key"])){
			$_SESSION["error"] = "Missing API-key";
		} elseif(empty($_POST["username"])){
			$_SESSION["error"] = "Missing username";
		} elseif(empty($_POST["password"])){
			$_SESSION["error"] = "Missing password";
		} else{
			$conf->set("domain", $_POST["nvr-ip"]);
			$conf->set("port", $_POST["nvr-port"]);
			$conf->set("title", $_POST["site-title"]);
			$conf->set("apiKey", $_POST["nvr-api-key"]);
			$conf->set("auth", (object)["username" => $_POST[""], "password" => hash('sha256', $_POST["password"])]);
			$conf->save();
		}
		header("Location: setup/2");
	} elseif($action == "step-2"){
		// Save the list of cameras to display

		$conf->save();
		header("Location: setup/3");
	} elseif($action == "step-3"){
		// Save the view, compressionrate and refresh time

		$conf->save();
		header("Location: setup/4");
	} elseif($action == "step-4"){
		// Save the setup
		$conf->set("setup", true);
		$conf->save();
		header("Location: view");
	} elseif($action == "get-image"){
		// Fetch an image from a camera, compress it and base64 encode it
		if(isset($_GET["cameraId"]))
			$cameraId = $_GET["cameraId"];
		else
			die("{\"error\": true, \"reason\": \"missing cameraId\"}");

		header('Content-Encoding: gzip');

		require_once __DIR__ . "/include/cameras.php";
		$cam = new Cameras();
		echo gzencode(base64_encode($com->fetch_img($_GET["cameraId"], $_GET['host'])), $conf->setup->compressionLevel);
	}
?>