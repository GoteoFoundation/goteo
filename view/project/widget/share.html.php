<?php
use Goteo\Library\Text;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

$share_title = $project->name;

$share_url = SITE_URL . '/project/' . $project->id;
$facebook_url = 'http://facebook.com/sharer.php?u=' . rawurlencode($share_url) . '&t=' . rawurlencode($share_title . ' | Goteo.org');
$twitter_url = 'http://twitter.com/home?status=' . rawurlencode($share_title . ': ' . $share_url . ' #Goteo');

$url = SITE_URL . '/widget/project/' . $project->id;


$widget_code = '<iframe frameborder="0" height="380px" src="'.$url.'" width="200px"></iframe>';


?>
    <script type="text/javascript">

    jQuery(document).ready(function ($) {

        $("#project-spread-widget").click(function (event) {
            event.preventDefault();

            /* Mostrar el codigo widget en una ventana*/
            alert('Utiliza este código: \n\r<?php echo $widget_code; ?>');

        });

    });
    </script>
<div class="widget project-share">    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-share-header'); ?></h<?php echo $level ?>>
    <ul>
        <li class="twitter"><a target="_blank" href="<?php echo htmlspecialchars($twitter_url) ?>" onclick="alert('desactivado hasta puesta en marcha'); return false;">Twitter</a></li>
        <li class="facebook"><a target="_blank" href="<?php echo htmlspecialchars($facebook_url) ?>" onclick="alert('desactivado hasta puesta en marcha'); return false;">Facebook</a></li>
        <li onclick="$(this).children('input').focus(); return false;" class="url"><span>URL: </span> <input type="text" onfocus="this.select();" readonly="readonly" size="35" value="<?php echo htmlspecialchars($share_url) ?>" /></li>
    </ul>
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-spread-header'); ?></h<?php echo $level ?>>
    <ul>
        <li class="widget"><a href="#" id="project-spread-widget"><?php echo Text::get('project-spread-widget'); ?></a></li>
    </ul>
</div>