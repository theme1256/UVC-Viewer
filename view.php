<?php
	require_once __DIR__ . "/include/head.php";

	require_once __DIR__ . "/include/cameras.php";
	$cam = new Cameras();

	echo $cam->view($conf->setup->cameras);
?>
<script type="text/javascript">
	var dead_img = "";
	var intervals = [];

	$(window).resize(function(event){
		rz();
	});
	$(function(){
		rz();
		// Henter prøvebilledet, så systemet kan se om det er det der vises
		$.ajax({
			url: 'backend',
			data: {
				action: "get-image",
				cameraId: "dead"
			},
			type: 'post',
			cache: false,
			success: function(output){
				dead_img = output;
			}
		});
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
	function rz(){
		var w_h = $(window).height();
		if(cam_count == 1){
			$(".camera").height(w_h);
		} else if(cam_count == 5 || cam_count == 6 || cam_count == 8 || cam_count == 9){
			$(".camera.layer_1").height(w_h/3*2-1);
			$(".camera.layer_2").height(w_h/3);
		} else if(cam_count > 16){
			$(".camera.layer_1").height(w_h/2)
			$(".camera.layer_2").height(w_h/6)
		} else if(14 <= cam_count && cam_count <= 16){
			$(".camera").height(w_h/4);
		} else{
			$(".camera.layer_1").height(w_h/2);
			$(".camera.layer_2").height(w_h/4);
		}
	}
	function fetch($this){
		if($this.data("poll") === true){
			$.ajax({
				url: 'backend',
				data: {
					action: "get-image",
					cameraId: $this.data("cameraid"),
					host: $this.data("ip")
				},
				type: 'post',
				cache: false,
				async: true,
				success: function(output){
					if(output == dead_img){
						$this.html('<img src="data:image/jpeg;base64,' + output + '" /><span class="cam-name">' + $this.data("name") + '</span>');
						$("span.cam-name").each(function(i, e){var t = $(e);t.css("margin-left", "-"+t.width()/2+"px")});
					} else{
						$this.html('<img src="data:image/jpeg;base64,' + output + '" />');
					}
				}
			});
		}
	}
</script>
