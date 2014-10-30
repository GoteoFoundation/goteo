/*
Slider vertical
autor: coyr / marcgd
Sitio: www.xoyaz.com / marcgd.com
*/

var spd = 1500;
var wait = 3000;
var stop = 1;
var dif = 0;
var timer = 0;

function carouselTw() {
	clearInterval(start);
	sliderH = $(".tweets").height();
	modH = $(".tweet").outerHeight();
	sliderTop = parseFloat($(".tweets").css("top"));
	dif = sliderH + sliderTop;
	if (stop === 1) {
		if( dif>modH ) {
			$(".tweets").animate({top: "-="+modH } , spd);
			timer = setTimeout(carouselTw,wait);
		} else {
			clearTimeout(timer);
			$(".tweets").css({ top: 0});
			timer = setTimeout(carouselTw,wait);
		}
	} else {
		timer = setTimeout(carouselTw,600);
	}
}

$(document).ready(function() {
	start = setInterval(function(){carouselTw();},3000);
	$(".tweets").mouseover(function(){stop = 0;});
	$(".tweets").mouseout(function(){stop = 1;});
});
