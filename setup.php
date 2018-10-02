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
				require_once __DIR__ . "/include/cameras.php";
				$cam = new Cameras();
		?>
			<form method="POST" action="backend">
				<input type="hidden" name="action" value="step-2">
				<?php
					foreach($cam->fetch_all() as $c){
				?>
				<div class="form-group">
					<label for="camshow_<?= $c["id"];?>"><?= $c["name"] . " (" . $c["ip"] . ")";?></label>
					<input type="checkbox" name="camshow[]" id="camshow_<?= $c["id"];?>" class="form-control" value="<?= $c["id"];?>">
				</div>
				<?php
					}
				?>
				<div class="form-control text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 3):
		?>
			<!-- View -->
			<form method="POST" action="backend">
				<input type="hidden" name="action" value="step-3">
				<?php
					foreach($conf->setup->cameras as $c){
				?>
				<div class="form-group">
					<label for="camshow_<?= $c["id"];?>"><?= $c["name"] . " (" . $c["ip"] . ")";?></label>
					<input type="number" name="camshow_<?= $c["id"];?>" id="camshow_<?= $c["id"];?>" class="form-control" value="<?= $c["sort"];?>">
				</div>
				<?php
					}
				?>
				<div class="form-group">
					<label for="compression-level">Compression level</label>
					<input type="text" name="compression-level" id="compression-level" class="form-control" value="<?= $conf->setup->compressionLevel;?>">
					<p class="form-text text-muted">Higher is more. Default is 9</p>
				</div>
				<div class="form-group">
					<label for="refreshtime">Refreshtime</label>
					<input type="text" name="refreshtime" id="refreshtime" class="form-control" value="<?= $conf->setup->refreshtime;?>">
					<p class="form-text text-muted">In ms. Default is 1000</p>
				</div>
				<div class="form-control text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 4):
		?>
			<pre>
				<?= var_export($conf->setup);?>
			</pre>
			<form method="POST" action="backend">
				<input type="hidden" name="action" value="step-4">
				<div class="form-control text-right">
					<button class="btn btn-primary">Gem</button>
				</div>
			</form>
		<?php
			endif;
		endif;
	?>
</div>
<?php
	require_once __DIR__ . "/include/foot.php";
?>