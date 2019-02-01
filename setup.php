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
			<div class="form-group text-right">
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
						Default is 7443
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
				<div class="form-group row">
					<div class="col-md-6"><a href="<?= ROOT?>setup/0" class="btn btn-warning">Go back</a></div>
					<div class="col-md-6 text-right"><button class="btn btn-primary">Next</button></div>
				</div>
			</form>
		<?php
			elseif($step == 2):
				require_once __DIR__ . "/include/cameras.php";
				$cam = new Cameras();
		?>
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-2">
				<div class="form-group">
					<legend>Select cameras to display</legend>
				<?php
					foreach($cam->fetch_all() as $c){
				?>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="camshow[]" id="camshow_<?= $c["id"];?>" value="<?= $c["id"];?>" <?= ($cam->selected($c["id"]) ? "checked" : "");?>>
						<label class="form-check-label" for="camshow_<?= $c["id"];?>">
							<?= $c["name"] . " (" . $c["ip"] . ")";?>
						</label>
					</div>
				<?php
					}
				?>
				</div>
				<hr>
				<div class="form-group row">
					<div class="col-md-6"><a href="<?= ROOT?>setup/1" class="btn btn-warning">Go back</a></div>
					<div class="col-md-6 text-right"><button class="btn btn-primary">Next</button></div>
				</div>
			</form>
		<?php
			elseif($step == 3):
		?>
			<!-- View -->
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-3">
				<div class="row">
					<div class="col-md-12" id="#grid">
						<?php
							require_once __DIR__ . "/include/cameras.php";
							$cam = new Cameras();
							echo $cam->view_grid(sizeof($conf->setup->cameras));
						?>
					</div>
					<div class="col-md-12 row">
						<?php
							foreach($conf->setup->cameras as $c){
								if($c->ip == $_SERVER['SERVER_ADDR'])
									continue;
						?>
						<div class="draggable col-md-2">
							<img class="camera" src="http://<?= $c->ip;?>/snap.jpeg?cb=<?= time();?>" data-sort="<?= $c->sort;?>">
							<legend><?= $c->name . " (" . $c->ip . ")";?></legend>
							<input type="hidden" name="camshow_<?= $c->id;?>" value="<?= $c->sort;?>">
						</div>
						<?php
							}
						?>
					</div>
					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
					<script type="text/javascript">
						$(function(){
							rz();
							$(".draggable").draggable({
								snap: ".camera"
							});
							$(".camera").each(function(index, el){
								if($(this).data("sort") >= 0){
									var loc1 = $("div.camera[data-sort='"+$(this).data("sort")+"']").offset();
									var loc2 = $(this).offset();
									var loc = {
										top: loc1.top - loc2.top, 
										left: loc1.left - loc2.left, 
									};
									console.log(loc);
									$(this).parent(".draggable").css(loc);
								}
							});
							$(".camera").droppable({
								drop: function(event, ui){
									console.log($(this)[0].dataset);
									console.log($($(ui.draggable)[0]));
									$(ui.draggable[0]).find("input").val($(this)[0].dataset.sort)
								}
							});
						});

						// Funktion som holder styr på at billederne er i korrekt gitter, når skærmen skifter størrelse
						function rz(){
							$(".camera").each(function(index, el){
								$(el).height($(el).width()*9/16+19);
							});
						}
					</script>
					<style type="text/css">
						#grid{ margin-bottom: 20px; }
						.camera{ border: 1px solid grey; }
						.draggable{  }
						.draggable legend{ font-size: 0.8em; }
					</style>
				</div>
				<hr>
				<div class="form-group">
					<legend for="refreshtime">Refreshtime</legend>
					<input type="text" name="refreshtime" id="refreshtime" class="form-control" value="<?= $conf->setup->refreshtime;?>">
					<p class="form-text text-muted">In ms. Default is 1000</p>
				</div>
				<hr>
				<div class="form-group row">
					<div class="col-md-6"><a href="<?= ROOT?>setup/2" class="btn btn-warning">Go back</a></div>
					<div class="col-md-6 text-right"><button class="btn btn-primary">Next</button></div>
				</div>
			</form>
		<?php
			elseif($step == 4):
		?>
			<pre>
				<?= var_export($conf->setup, true);?>
			</pre>
			<hr>
			<form method="POST" action="<?= ROOT;?>backend">
				<input type="hidden" name="action" value="step-4">
				<div class="form-group row">
					<div class="col-md-6"><a href="<?= ROOT?>setup/3" class="btn btn-warning">Go back</a></div>
					<div class="col-md-6 text-right"><button class="btn btn-primary">Save</button></div>
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