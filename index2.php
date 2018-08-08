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
		</style>
	</head>
	<body>
		<?php
			require("include/cameras2.php");
		?>
		<script src="https://code.jquery.com/jquery-latest.min.js"></script>
		<script type="text/javascript">
			$(function(){
				rz();
			});
			$('.camera').each(function(){
				var $this = $(this);
				(function poll(){
					if($this.data("poll") === true){
						setTimeout(function(){
							$.ajax({
								url: 'image.php?cameraId=' + $this.data("cameraid") + '&width=' + $this.data("width"),
								type: 'get',
								cache: false,
								context: this,
								success: function(output){
									$this.html('<img src="data:image/jpeg;base64,' + output + '" />');
								},
								complete: poll
							});
						}, <?= $refreshtime;?>);
					} else{
						setTimeout(poll, 10); 
					}
				})();
			});
			function rz(){
				var w_h = $(window).height();
				if(cam_count == 1){
					$(".camera").height(w_h);
				} else if(cam_count == 5 || cam_count == 6 || cam_count == 8 || cam_count == 9){
					$(".camera.layer_1").height(w_h/3*2);
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
			$(window).resize(function(event){
				/* Act on the event */
				rz();
			});
		</script>
	</body>
</html>
