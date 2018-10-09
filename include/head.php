<?php
	session_start();
	require_once __DIR__ . "/../config/config.php";

	if($_SERVER["HTTP_HOST"] == "10.45.0.19")
		define("ROOT", "/unifi/");
	else
		define("ROOT", "/");
?>
<!DOCTYPE html>
<html<?= ($conf->isItThisPage("view") ? " class='c-none'" : "");?>>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="/favicon.ico">
		<title><?= $conf->setup->title ?></title>
		<!-- Bootstrap core CSS -->
		<link href="<?= ROOT;?>css/bootstrap.min.css?v=4.1.3" rel="stylesheet">
		<!-- Custom styles for this template -->
		<link href="<?= ROOT;?>css/main.css" rel="stylesheet">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<!-- jQuery -->
		<script type="text/javascript" src="<?= ROOT;?>js/jquery.min.js?v=3.3.1"></script>
		<!-- Bootstrap core JS -->
		<script type="text/javascript" src="<?= ROOT;?>js/bootstrap.min.js?v=4.3.1"></script>
	</head>
	<body<?= ($conf->isItThisPage("view") ? " class='c-none'" : "");?>>
