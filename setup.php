<?php
	require_once __DIR__ . "/include/head.php";
?>
<div class="container">
	<?php
		$step = (!isset($_GET["step"]) ? $conf->first_step : str_replace(".php", "", $_GET["step"]));

		if($conf->setup->setup == true && $_SESSION["login"] == false):
	?>
		<form method="POST" action="<?= ROOT;?>backend">
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

			if($step == 0):
		?>
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-0">
				<div class="form-group">
					<legend for="nvr-ip">NVR IP or domain</legend>
					<input type="text" name="nvr-ip" id="nvr-ip" class="form-control" value="<?= $conf->setup->domain;?>" aria-describedby="nvr-ip-HelpBlock">
					<small id="nvr-ip-HelpBlock" class="form-text text-muted">
						Only the IP or FQDN
					</small>
				</div>
				<div class="form-group">
					<legend for="nvr-port">NVR Port</legend>
					<input type="text" name="nvr-port" id="nvr-port" class="form-control" value="<?= $conf->setup->port;?>" aria-describedby="nvr-port-HelpBlock">
					<small id="nvr-port-HelpBlock" class="form-text text-muted">
						Default is <?= $conf->setup->port;?>
					</small>
				</div>
				<hr>
				<div class="form-group">
					<legend for="site-title">Site title</legend>
					<input type="text" name="site-title" id="site-title" class="form-control" value="<?= $conf->setup->title;?>">
				</div>
				<hr>
				<div class="form-group">
					<legend>Unifi software</legend>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="version" id="version-video" value="unifi-video" <?= ($conf->setup->unifi->version == "unifi-video" ? "checked" : "");?> aria-describedby="nvr-software-HelpBlock">
						<label class="form-check-label" for="version-video">
							Unifi Video
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="version" id="version-protect" value="unifi-protect" <?= ($conf->setup->unifi->version == "unifi-protect" ? "checked" : "");?> aria-describedby="nvr-software-HelpBlock">
						<label class="form-check-label" for="version-protect">
							Unifi Protect
						</label>
					</div>
					<small id="nvr-software-HelpBlock" class="form-text text-muted">
						Is your NVR running Unifi Protect (it's a Unifi CloudKey Gen2+) or Unifi Video (It's a Unifi NVR)
					</small>
				</div>
				<hr>
				<div class="form-group text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 1):
		?>
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-1">
				<?php if($conf->setup->unifi->version == "unifi-video"):?>
				<div class="form-group">
					<legend for="nvr-api-key">NVR API-key</legend>
					<input type="text" name="nvr-api-key" id="nvr-api-key" class="form-control" value="<?= $conf->setup->unifi->apiKey;?>" aria-describedby="nvr-api-key-HelpBlock">
					<small id="nvr-api-key-HelpBlock" class="form-text text-muted">
						Get the API-ey by signing in to <a href="https://<?= $conf->setup->domain;?>:<?= $conf->setup->port;?>/login" target="_BLANK">Unifi Video</a> and access your user information
					</small>
				</div>
				<?php elseif($conf->setup->unifi->version == "unifi-protect"):?>
				<div class="form-group">
					<legend for="nvr-username">NVR username</legend>
					<input type="text" name="nvr-username" id="nvr-username" class="form-control" value="<?= $conf->setup->unifi->username;?>">
				</div>
				<div class="form-group">
					<legend for="nvr-password">NVR password</legend>
					<input type="text" name="nvr-password" id="nvr-password" class="form-control" value="<?= $conf->setup->unifi->password;?>" aria-describedby="nvr-password-HelpBlock">
					<small id="nvr-password-HelpBlock" class="form-text text-muted">
						Will be stored in plain-text on this system
					</small>
				</div>
				<?php endif;?>
				<hr>
				<div class="form-group">
					<legend for="username">Username</legend>
					<input type="text" name="username" id="username" class="form-control" value="<?= $conf->setup->auth->username;?>">
				</div>
				<div class="form-group">
					<legend for="password">Password</legend>
					<input type="password" name="password" id="password" class="form-control" aria-describedby="password-HelpBlock">
					<small id="password-HelpBlock" class="form-text text-muted">
						Will be used to access and change these settings and will be stored in hashed format
					</small>
				</div>
				<hr>
				<div class="form-group text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 2):
				require_once __DIR__ . "/include/cameras.php";
				$cam = new Cameras();
		?>
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-2">
				<?php
					foreach($cam->fetch_all() as $c){
				?>
				<div class="form-group">
					<legend for="camshow_<?= $c["id"];?>"><?= $c["name"] . " (" . $c["ip"] . ")";?></legend>
					<input type="checkbox" name="camshow[]" id="camshow_<?= $c["id"];?>" class="form-control" value="<?= $c["id"];?>">
				</div>
				<?php
					}
				?>
				<div class="form-group text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 3):
		?>
			<!-- View -->
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-3">
				<?php
					foreach($conf->setup->cameras as $c){
				?>
				<div class="form-group">
					<legend for="camshow_<?= $c->id;?>"><?= $c->name . " (" . $c->ip . ")";?></legend>
					<input type="number" name="camshow_<?= $c->id;?>" id="camshow_<?= $c->id;?>" class="form-control" value="<?= $c->sort;?>">
				</div>
				<?php
					}
				?>
				<div class="form-group">
					<legend for="refreshtime">Refreshtime</legend>
					<input type="text" name="refreshtime" id="refreshtime" class="form-control" value="<?= $conf->setup->refreshtime;?>">
					<p class="form-text text-muted">In ms. Default is 1000</p>
				</div>
				<div class="form-group text-right">
					<button class="btn btn-primary">Next</button>
				</div>
			</form>
		<?php
			elseif($step == 4):
		?>
			<pre>
				<?= var_export($conf->setup, true);?>
			</pre>
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-4">
				<div class="form-group text-right">
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