<!DOCTYPE html>
<html lang="en">
	<head>
		<style type="text/css">
			html{ cursor: none; }
			body{ margin: 0; background: black; cursor: none; }
			img{ max-width: 100%; height: 100%; }
			.l > img{ float: left; }
			.r > img{ float: right; }

			.grid_1{ width: 100%; height: 100%; float: left; }

			.grid_2{ width: 50%; height: 50%; float: left; }

			.grid_3{ width: 33%; height: 33%; float: left; }
			.grid_3.layer_1{ width: 66%; height: 66%; float: left; }

			.grid_4{ width: 25%; height: 25%; float: left; }

			span.cam-name{ position: relative; top: 80%; width: inherit; color: white; text-align: center; }
			.grid_2.layer_2 .cam-name{ font-size: 50%; top: 77%; }
			.grid_3.layer_2 .cam-name{ font-size: 75%; top: 79%; }
			.r .cam-name{ left: 50%; }
			.l .cam-name{ right: 50%; }
		</style>
	</head>
	<body>
		<?php
			require("include/cameras2.php");
		?>
		<script src="https://code.jquery.com/jquery-latest.min.js"></script>
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
					url: 'image.php?cameraId=dead',
					type: 'get',
					cache: false,
					success: function(output){
						dead_img = output;
					}
				});
			});
			$('.camera').each(function(){
				// Løber alle kameraerne igennem, så de bliver opdateret
				var $this = $(this);
				if($this.data("poll") === true){
					intervals[$this.data("cameraid")] = setInterval(function(){
						fetch($this);
					}, <?= $refreshtime;?>);
				}
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
						url: 'image.php?cameraId=' + $this.data("cameraid") + '&width=' + $this.data("width") + '&host=' + $this.data("ip"),
						type: 'get',
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
	</body>
</html>
