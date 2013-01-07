<?php

use Goteo\Library\Text,
    Goteo\Library\Buzz,
    Goteo\Core\View;

$call = $this['call'];
$social = $this['social'];
?>
<h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>

<?php if ($call->status == 3) : //inscripcion ?>
<p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
<?php elseif (!empty($call->amount)) : //en campaña con dinero ?>
<p class="subtitle"><?php echo Text::html('call-splash-invest_explain', $call->user->name) ?></p>
<?php else : //en campaña sin dinero, con recursos ?>
<p class="subtitle"><?php echo Text::recorta($call->resources, 200) ?></p>
<?php endif; ?>

<hr />
<?php if (!empty($social->tweet)) : ?>
<a href="https://twitter.com/share" class="twitter-share-button"
   data-url="<?php echo SITE_URL . '/call/' . $call->id; ?>"
   data-via="<?php echo $social->author ?>"
   data-text="<?php echo $social->tweet; ?>"
   data-lang="<?php echo \LANG; ?>"
   data-counturl="<?php echo SITE_URL . '/call/' . $call->id; ?>"
   target="_blank">Tweet</a>
<script>!function(d,s,id){
    var js,fjs=d.getElementsByTagName(s)[0];
    if(!d.getElementById(id)){
        js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);
    }
}(document,"script","twitter-wjs");</script>
<?php endif; ?>
<?php if (!empty($social->fbappid)) : ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo \Goteo\Library\Lang::locale(); ?>/all.js#xfbml=1&appId=<?php echo $social->fbappid; ?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php endif; ?>
<hr />
<?php echo 'DEBUG:: '. $social->buzz_debug; ?>
<hr />
<?php echo \trace($social->buzz); ?>
<hr />