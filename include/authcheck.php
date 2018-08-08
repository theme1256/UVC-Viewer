<?php
include("config/config.php");
if ($auth == "true"){
session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')){
header ("Location: login.php");
exit();
}
}
?>
