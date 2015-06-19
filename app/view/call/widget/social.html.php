<?php

use Goteo\Library\Text,
    Goteo\Application\Lang,
    Goteo\Core\View;
$call = $vars['call'];
$social = $vars['social'];

$URL = \SITE_URL;
$share_url = $URL . '/call/' . $call->id.'/project';
if (LANG != 'es')
    $share_url .= '?lang=' . LANG;

$share_title = (!empty($social->tweet)) ? $social->tweet : $call->name;
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
            <li class="email"><a href="mailto:<?php echo $call->user->email ?>" target="_blank"><?php echo Text::get('regular-email'); ?></a></li>
        </ul>
        <a href="<?php echo $URL ?>/service/resources" id="capital" target="_blank"><?php echo Text::get('footer-service-resources') ?></a>
    </div>

    <div id="social-actions">
        <h2><?php echo Text::get('call-header-social_spread'); ?></h2>

        <?php

         if (!empty($social->fbappid)) :
             echo Text::widget($social->fbappid, 'fb-nocount', 'height:20px;'); ?>
            <div id="fb-root"></div>
            <script type="text/javascript">(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) {return;}
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/<?php echo Lang::getLocale(); ?>/all.js#xfbml=1&appId=189133314484241";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
        <?php /* else: // si no tiene app de facebook ponemos un compartir (por ahora no) ?>
                <!-- a target="_blank" href="<?php echo htmlentities($facebook_url) ?>"><?php echo Text::get('regular-facebook'); ?></a -->
        <?php */ endif; ?>

        <a href="https://twitter.com/share" class="twitter-share-button"
           data-url="<?php echo $share_url; ?>"
           data-text="<?php echo $share_title; ?>"
           data-lang="<?php echo Lang::current(); ?>"
           data-count="none"
           data-counturl="<?php echo SITE_URL . '/call/' . $call->id; ?>"
           target="_blank"><?php echo Text::get('regular-twitter'); ?></a>
        <script type="text/javascript">!function(d,s,id){
            var js,fjs=d.getElementsByTagName(s)[0];
            if(!d.getElementById(id)){
                js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);
            }
        }(document,"script","twitter-wjs");</script>

    </div>
</div>
