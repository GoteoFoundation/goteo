<?php
use Goteo\Library\Text;
?>
<div id="side" class="twitter">
    <h2><?php echo Text::get('call-header-buzz'); ?></h2>
	<!-- aqui el foreach -->
	<div class="tweet">
	    <div class="avatar">
	        <a href="https://twitter.com/ACS_Arquisocial" target="_blank">
	            <img src="http://a0.twimg.com/profile_images/2602026186/r2vhs8x0azj4zrve1tv2_normal.jpeg" alt="ACS_Arquisocial" title="Arq_CompromisoSocial">
	        </a>
	    </div>
	    <div class="text">
	        <strong><a href="https://twitter.com/ACS_Arquisocial" target="_blank">Arq_CompromisoSocial</a></strong>
	        <br>
	        <a href="https://twitter.com/ACS_Arquisocial" target="_blank">@ACS_Arquisocial</a>
		    <blockquote>RT @platoniq: Una Plaza procomún: cúpula en el Campo de Cebada @campodecebada http://t.co/XtBfhSP1 Riégalo por #goteo http://t.co/u5npJEek</blockquote>
	    </div>
	</div>
	
	<div class="tweet">
	    <div class="avatar">
	        <a href="https://twitter.com/ACS_Arquisocial" target="_blank">
	            <img src="http://a0.twimg.com/profile_images/2309950436/ACS_logo__normal.jpg" alt="ACS_Arquisocial" title="Arq_CompromisoSocial">
	        </a>
	    </div>
	    <div class="text">
	        <strong><a href="https://twitter.com/ACS_Arquisocial" target="_blank">Arq_CompromisoSocial</a></strong>
	        <br>
	        <a href="https://twitter.com/ACS_Arquisocial" target="_blank">@ACS_Arquisocial</a>
		    <blockquote>MagmaCultura News is out! http://t.co/4l5Irl7r ▸ Top stories today via @hoyesarte_com @platoniq</blockquote>
	    </div>
	</div>
<?php
/*
// pintado estatico para maquetar
//
// Petición a twitter desconectada en Linea 448 en controller/call.php
# if ($_SESSION['user']->id == 'root' && !empty($social->buzz_debug)) echo '<p>DEBUG:: '. $social->buzz_debug . '</p>';
# if ($_SESSION['user']->id == 'root' && !empty($social->buzz)) echo \trace($social->buzz);

$social = $this['social'];

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
*/ ?>
</div>

