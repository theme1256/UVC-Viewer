<?php $basename = substr(strtolower(basename($_SERVER['PHP_SELF'])),0,strlen(basename($_SERVER['PHP_SELF']))-4); ?>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
	<div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="#">UniFi Video</a>
	</div>
	<div id="navbar" class="collapse navbar-collapse">
	  <ul class="nav navbar-nav">
		<li <?php if ($basename == 'index') echo ' class="active"'; ?>><a href="index.php">Home</a></li>
		<li <?php if ($basename == 'info') echo ' class="active"'; ?>><a href="info.php">Info</a></li>
		<?php if ($auth == 'true') echo '<li><a href="logout.php">Logout</a></li>' ?>
	</ul>
	</div><!--/.nav-collapse -->
  </div>
</nav>
