<?php
	require_once __DIR__ . "/include/head.php";

	require_once __DIR__ . "/include/cameras.php";
	$cam = new Cameras();

	echo $cam->view($conf->setup->cameras);
?>
<script type="text/javascript">
	var start_time = Date.now(),
		intervals = [];

	$(window).resize(function(event){
		rz();
	});
	$(function(){
		rz();
		$('.camera').each(function(){
			// Løber alle kameraerne igennem, så de bliver opdateret
			var $this = $(this);
			if($this.data("poll") === true){
				intervals[$this.data("cameraid")] = setInterval(function(){
					fetch($this);
				}, <?= $conf->setup->refreshtime;?>);
			}
		});
	});

	// Funktion som holder styr på at billederne er i korrekt gitter, når skærmen skifter størrelse
	function rz(){
		$(".camera").each(function(index, el){
			$(el).height($(el).width()*9/16);
		});
	}

	// Henter et nyt billede, tjekker om der er gået mere end 15 minutter, siden siden blev loadet og reload i det tilfælde, for at spare RAM
	function fetch($this){
		if($this.data("poll") === true){
			// Hvis den har kørt i mere end 15 minutter, reload for at tømme source (RAM)
			if(Date.now() - start_time >= 900000)
				location.reload();

			// Lav et billede, som objekt
			var img = new Image(),
				ts = Date.now();

			// Når billedet er loadet, sæt billedet ind det rigtige sted, så det bliver vist
			img.onload = function() {
				// $this.html('<img src="' + img.src + '" /><span class="cam-name">' + $this.data("name") + '</span>');
				$this.html('<img src="' + img.src + '" />');
				// Slet objektet, for at spare på RAM
				img = undefined;
				delete(img);
			};

			// Hent billedet direkte fra kameraet
			img.src = 'http://' + $this.data("ip") + '/snap.jpeg?cb=' + ts;
		}
	}
</script>
