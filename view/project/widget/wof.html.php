<?php

use Goteo\Core\Error,
    Goteo\Library\WallFriends,
    Goteo\Library\Text,
    Goteo\Model;

$project = $this['project'];

$width = 580;
$all_avatars = 1;

$url = SITE_URL.'/project/'.$project->id;


$wof = new WallFriends($project->id, $all_avatars);

if (!$wof instanceof \Goteo\Library\WallFriends) return;

//cal que siguin multiples del tamany
$wsize = $wof->w_size + $wof->w_padding * 2;
$hsize = $wof->h_size + $wof->h_padding * 2;
//num icones per fila
$num_icons = floor($width / $wsize);
//tamany minim
if($num_icons < 15) $num_icons = 14;
//amplada efectiva
$width = $wsize * $num_icons;

// estilos dinamicos
$style = "<style type=\"text/css\">";
$style .= "div.wof>div.ct>a>img {border:0;width:{$wof->w_size}px;height:{$wof->h_size}px;display:inline-block;padding:{$wof->h_padding}px {$wof->w_padding}px {$wof->h_padding}px {$wof->w_padding}px}";
$style .= "</style>";
echo $style;

?>
<div class="wof" style="width:<?php echo $width ?>px;height:<?php echo ($hsize * 3) ?>px;">
<?php if ($wof->show_title) echo '<h2><span></span><a href="'.$url.'" style="width:'.($width - 50).'px;">Goteo.org  Crowdfunding the commons</a></h2>' //'.GOTEO_META_TITLE.' ?>
    <div class="ct">
<!-- //num finançadors -->
        <div class="a i" style="left:<?php echo ($num_icons < 15 ? "0" : $wsize) ?>px;top:<?php echo $hsize ?>px;width:<?php echo ($wsize * 5) ?>px;height:<?php echo ($hsize * 3) ?>px;">
            <h3><a href="<?php echo $url ?>"><?php echo count($project->investors) ?></a></h3>
            <p><a href="<?php echo $url ?>"><?php echo Text::get('project-view-metter-investors') ?></a></p>
        </div>

<!-- //financiacio, data -->
        <div class="b i" style="left:<?php echo ($wsize * ($num_icons <15 ? 6 : 7)) ?>px;top:<?php echo $hsize ?>px;width:<?php echo ($wsize * 8) ?>px;height:<?php echo ($hsize * 3) ?>px;">
            <h3><a href="<?php echo $url ?>"><?php echo amount_format($project->invested) ?> <img src="<?php echo SRC_URL ?>/view/css/euro/violet/xxl.png" alt="&euro;"></a></h3>
            <p><a href="<?php echo $url ?>"><?php echo Text::get('project-view-metter-days'). " {$project->days} " . Text::get('regular-days') ?></a></p>
        </div>

<!-- //impulsores, nom, desc -->
        <div class="c i" style="left:<?php echo ($num_icons < 18 ? "0" : $wsize) ?>px;top:<?php echo ( $hsize * 5) ?>px;width:<?php echo ($wsize * ($num_icons < 18 ? $num_icons : 17)) ?>px;height:<?php echo ($hsize *3) ?>px;">
            <div class="c1" style="height:<?php echo ($wsize * 3) ?>px;width:<?php echo ($wsize * 3) ?>px;">
                <p><a href="<?php echo SITE_URL.'/user/'.$project->owner ?>"><img src="<?php echo $project->user->avatar->getLink(56,56,true) ?>" alt="<?php echo $project->user->name ?>" title="<?php echo $project->user->name ?>"><br /><?php echo Text::get('regular-by') . ' '  . $project->user->name ?></a></p>
            </div>
            <div class="c2" style="height:<?php echo ($wsize * 3) ?>px;width:<?php echo ($wsize * ($num_icons < 18 ? $num_icons - 3 : 14)) ?>px;">
                <h3><a href="<?php echo $url ?>"><?php echo $project->name ?></a></h3><p><a href="<?php echo $url ?>"><?php echo $project->subtitle ?></a></p>
            </div>
        </div>

        <?php echo implode("",$wof->html_content($num_icons)) ?>
    </div>

</div>


