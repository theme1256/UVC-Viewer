$('.camera').each(function() {
   var $this = $(this);
      (function poll() {
	if ($this.data("poll") === true){
 	setTimeout(function () {
	$.ajax({ url: 'image.php?cameraId=' + $this.data("cameraid") + '&width=' + $this.data("width"),
		type: 'get',
                cache: false,
		context: this,
                success: function(output) {
			$this.html('<img src="data:image/jpeg;base64,' + output + '" width="' + $this.data("width") + '" />');
			},
                  complete: poll
                });
         },1000);
	}
	else{
	setTimeout(poll, 10); 
	}
    })();
});

$( ".camera" ).dblclick(function() {
$(this).data("poll", false);
toggleZoom($(this));
//toggleFullScreen($(this));
$(this).data("poll", true);
});

function toggleZoom(x) {
        if (x.data("oldWidth")){
                x.children('img').width(x.data("oldWidth"));
                x.data("width", x.data("oldWidth"));
                x.data("oldWidth", "");
                x.removeClass("overlay");
                $("#underlay").removeClass("underlay");
                x.css("left","")
                x.css("top","")
                $(window).scrollTop( x.data("scroll"));
                x.data("scroll", "");
                x.siblings().data("poll", true);
        }
        else{
                x.data("oldWidth", x.data("width"));
                $("#underlay").addClass("underlay");
                x.addClass("overlay");
                x.data("width", x.width());
                x.children('img').width(x.width());
                x.css("left",($(window).width() / 2) - (x.width() / 2))
                x.css("top",($(window).height() / 2) - (x.height() / 2) - 25)
                x.data("scroll", $(window).scrollTop());
                $(window).scrollTop(0);
                x.siblings().data("poll", false);
        }
}

function toggleFullScreen(x) {
  if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (x.requestFullscreen) {
      x.requestFullscreen();
    } else if (x.msRequestFullscreen) {
      x.msRequestFullscreen();
    } else if (x.mozRequestFullScreen) {
      x.mozRequestFullScreen();
    } else if (x.webkitRequestFullscreen) {
      x.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}

