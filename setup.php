<?php
	require_once __DIR__ . "/include/head.php";
?>
<div class="container">
	<?php
		$step = (empty($_GET["step"]) ? 1 : str_replace(".php", "", $_GET["step"]));

		if($conf->setup->setup == true && $_SESSION["login"] == false):
	?>
		<form method="POST" action="backend">
			<input type="hidden" name="action" value="login">
			<input type="hidden" name="step" value="<?= $step;?>">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" class="form-control">
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" class="form-control">
			</div>
			<div class="form-control text-right">
				<button class="btn btn-success">Login</button>
			</div>
		</form>
	<?php
		else:

			if($step == 1):
		?>
			<form method="POST" action="backend">
				<input type="hidden" name="action" value="step-1">
				<div class="form-group">
					<label for="nvr-ip">NVR IP or domain</label>
					<input type="text" name="nvr-ip" id="nvr-ip" class="form-control" value="<?= $conf->setup->domain;?>">
				</div>
				<div class="form-group">
					<label for="nvr-port">NVR Port</label>
					<input type="text" name="nvr-port" id="nvr-port" class="form-control" value="<?= $conf->setup->port;?>">
					<p class="form-text text-muted">Default is 7443</p>
				</div>
				<div class="form-group">
					<label for="site-title">Site title</label>
					<input type="text" name="site-title" id="site-title" class="form-control" value="<?= $conf->setup->title;?>">
				</div>
				<div class="form-group">
					<label for="nvr-api-key">NVR API-key</label>
					<input type="text" name="nvr-api-key" id="nvr-api-key" class="form-control" value="<?= $conf->setup->apiKey;?>">
				</div>
				<div class="form-group">
					<label for="username">Username</label>
					<input type="text" name="username" id="username" class="form-control" value="<?= $conf->setup->auth->username;?>">
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="password" id="password" class="form-control">
				</div>
				<div class="form-control text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 2):
		?>
			Cams
		<?php
			elseif($step == 3):
		?>
			View
			compression
			refreshtime
		<?php
			elseif($step == 4):
		?>
			Overview
			Gem
		<?php
			endif;
		endif;
	?>
</div>
<?php
	require_once __DIR__ . "/include/foot.php";
?>