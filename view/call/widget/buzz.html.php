<?php
use Goteo\Library\Text;

$social = $this['social'];
?>

<script>

/*
Slider vertical
autor: coyr
Sitio: www.xoyaz.com
*/ 
	velocidad = 1500;
	tiempoEspera = 3000;
	verificar = 1;
	dif=0;
	timer=0
	
	function moverSlider() {
		console.log('vamos');
		sliderAltura = $(".tweets").height();
		moduloAltura = $(".tweet").height() + parseFloat($(".tweet").css("padding-top")) + parseFloat($(".tweet").css("padding-bottom"));
		sliderTop = parseFloat($(".tweets").css("top"));
		dif = sliderAltura + sliderTop;
		
		if (verificar==1) {
				if( dif>moduloAltura ) {			
					$(".tweets").animate({top: "-="+moduloAltura } , velocidad);
					timer = setTimeout('moverSlider()',tiempoEspera);
				}	
				else {
					clearTimeout(timer);
					$(".tweets").css({ top: 0});			
					timer = setTimeout('moverSlider()',0);				
				}
		}		
		else {	
		timer = setTimeout('moverSlider()',1000);
		}
	}	
	function bajarSlider() {
		if(dif>=moduloAltura*2) {
					$(".tweets").animate({top: "-="+moduloAltura }, velocidad);
				}
				else {
					$(".tweets").css({ top: 0});
					$(".tweets").animate({top: "-="+moduloAltura }, velocidad);		
				} 
	}
	function subirSlider() {
		if(sliderTop<=-moduloAltura) {
					$(".tweets").animate({top: "+="+moduloAltura }, velocidad);	
				}
				else {
					$(".tweets").css({ top: -sliderAltura+moduloAltura});
					$(".tweets").animate({top: "+="+moduloAltura }, velocidad);						
				} 
	}
	
	
	
	
	$(document).ready(function() {
		moverSlider();
		$(".bajar-slider").click(function(){
			bajarSlider();
		});

		$(".subir-slider").click(function(){
			subirSlider();
		});

		$(".slider-vertical").mouseover(function(){
			verificar = 0;
		});

		$(".slider-vertical").mouseout(function(){
			verificar = 1;
		});
	});
	
	
</script>
<div id="side" class="twitter">
    <h2><?php echo Text::get('call-header-buzz'); ?></h2>
	<div class="tweets-container">
	<div class="tweets">
<?php
// Petición a twitter se desconecta en la Linea 448 en controller/call.php
if ($_SESSION['user']->id == 'root') echo '<!-- BUZZ_DEBUG:: '. $social->buzz_debug . ' -->';

foreach ($social->buzz as $item) : ?>
    <div class="tweet">
        <div class="avatar">
            <a href="<?php echo $item->profile ?>" target="_blank">
                <img src="<?php echo $item->avatar ?>" alt="<?php echo $item->author ?>" title="<?php echo $item->user ?>"/>
            </a>
        </div>
        <div class="text">
            <strong><a href="<?php echo $item->profile ?>" target="_blank"><?php echo $item->user ?></a></strong>
            <br />
            <a href="<?php echo 'https://twitter.com/'.$item->twitter_user ?>" target="_blank"><?php echo '@'.$item->twitter_user ?></a>
                <blockquote><?php echo $item->text ?></blockquote>
        </div>
    </div>
<?php endforeach;
?>
	</div>
	</div>
</div>

