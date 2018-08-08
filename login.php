<?php
if (isset($_POST["username"])) $username = $_POST['username'];
else $username="";
if (isset($_POST["password"])) $password = hash('sha256', $_POST['password']);
else $password="";
include("config/config.php");
if (($username != $setUsername || $password != $setPassword) && $auth == "true") {
?>
<!DOCTYPE html>
<html lang="en">
<?php include("include/head.php"); ?>
  <body>
    <?php include("include/navbar.php"); ?>
    <div class="container">
      <div class="starter-template">
	<h1>Login</h1>
	<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<label for="username">Username:</label>
		<input type="text" title="Enter your Username" name="username" />
		<br />
		<label for="password">Password:</label>
		<input type="password" title="Enter your password" name="password" />
		<br />
		<input type="submit" name="Submit" value="Login" />
	</form>      
	</div>
    </div><!-- /.container -->
    <?php include("include/scripts.php"); ?>
  </body>
</html>
<?php
}
else {
session_start();
$_SESSION['login']= $username;
header ("Location: index.php");
}
?>
