<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $this['call'];
$social = $this['social'];

$URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL;
$share_url = $URL . '/call/' . $call->id;
if (LANG != 'es')
    $share_url .= '?lang=' . LANG;

$shate_title = (!empty($social->tweet)) ? $social->tweet : $call->name;
$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
?>
<div id="social">
	<div id="social-logo">
	    <ul>
	    <?php if (!empty($call->user->facebook)): ?>
	    <li class="facebook"><a href="<?php echo htmlspecialchars($call->user->facebook) ?>"><?php echo Text::get('regular-facebook'); ?></a></li>
	    <?php endif ?>
	    <?php if (!empty($call->user->google)): ?>
	    <li class="google"><a href="<?php echo htmlspecialchars($call->user->google) ?>"><?php echo Text::get('regular-google'); ?></a></li>
	    <?php endif ?>
	    <?php if (!empty($call->user->twitter)): ?>
	    <li class="twitter"><a href="<?php echo htmlspecialchars($call->user->twitter) ?>"><?php echo Text::get('regular-twitter'); ?></a></li>
	    <?php endif ?>
	    <?php if (!empty($call->user->identica)): ?>
	    <li class="identica"><a href="<?php echo htmlspecialchars($call->user->identica) ?>"><?php echo Text::get('regular-identica'); ?></a></li>
	    <?php endif ?>
	    <?php if (!empty($call->user->linkedin)): ?>
	    <li class="linkedin"><a href="<?php echo htmlspecialchars($call->user->linkedin) ?>"><?php echo Text::get('regular-linkedin'); ?></a></li>
	    <?php endif ?>
	</ul>
	<a href="<?php echo $URL ?>/service/resources" id="capital" target="_blank"><?php echo Text::get('footer-service-resources') ?></a>
	</div>

	<div id="social-actions">
	<h2>Difunde esta iniciativa</h2>

		<a href="https://twitter.com/share" class="twitter-share-button"
		   data-url="<?php echo $share_url; ?>"
		   data-via="<?php echo $social->author ?>"
		   data-text="<?php echo $share_title; ?>"
		   data-lang="<?php echo \LANG; ?>"
		   data-counturl="<?php echo SITE_URL . '/call/' . $call->id; ?>"
		   target="_blank"><?php echo Text::get('regular-twitter'); ?></a>
		<script>!function(d,s,id){
		    var js,fjs=d.getElementsByTagName(s)[0];
		    if(!d.getElementById(id)){
		        js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);
		    }
		}(document,"script","twitter-wjs");</script>

	<div data-show-faces="false" data-width="450" data-layout="button_count" data-send="false" data-href="http://www.facebook.com/pages/Goteo/268491113192109" class="fb-like fb_edge_widget_with_comment fb_iframe_widget" fb-xfbml-state="rendered"><span style="height: 20px; width: 118px;"><iframe scrolling="no" id="f3d78948875001e" name="f357c6a66af3e0c" style="border: medium none; overflow: hidden; height: 20px; width: 118px;" title="Like this content on Facebook." class="fb_ltr" src="http://www.facebook.com/plugins/like.php?api_key=189133314484241&amp;locale=es_ES&amp;sdk=joey&amp;channel_url=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter.php%3Fversion%3D18%23cb%3Df3953b18d391ebc%26origin%3Dhttp%253A%252F%252Fgoteo.org%252Ff29a354eb36053%26domain%3Dgoteo.org%26relation%3Dparent.parent&amp;href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FGoteo%2F268491113192109&amp;node_type=link&amp;width=450&amp;layout=button_count&amp;colorscheme=light&amp;show_faces=false&amp;send=false&amp;extended_social_context=false"></iframe></span></div>

		<?php if (!empty($social->fbappid)) : ?>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) {return;}
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/<?php echo \Goteo\Library\Lang::locale(); ?>/all.js#xfbml=1&appId=<?php echo $social->fbappid; ?>";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<?php else: // si no tiene app de facebook ponemos un compartir ?>
		<!--a target="_blank" href="<?php echo htmlentities($facebook_url) ?>"><?php echo Text::get('regular-facebook'); ?></a-->
		<?php endif; ?>
			
	</div>
</div>
