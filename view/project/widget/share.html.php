<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

$share_title = $project->name;

$share_url = SITE_URL . '/project/' . $project->id;
$facebook_url = 'http://facebook.com/sharer.php?u=' . rawurlencode($share_url) . '&t=' . rawurlencode($share_title . ' | Goteo.org');
$twitter_url = 'http://twitter.com/home?status=' . rawurlencode($share_title . ': ' . $share_url . ' #Goteo');

?>
 <script type="text/javascript">
            jQuery(document).ready(function ($) { 
				$("#a-proyecto").fancybox({
					'titlePosition'		: 'inside',
					'transitionIn'		: 'none',
					'transitionOut'		: 'none'
				});
			});
</script>			
<div class="widget project-share">    
	<div class="left">
        <h<?php echo $level+1 ?>><?php echo Text::get('project-share-header'); ?></h<?php echo $level+1 ?>>
        <ul>
            <li class="twitter"><a target="_blank" href="<?php echo htmlspecialchars($twitter_url) ?>" onclick="alert('desactivado hasta puesta en marcha'); return false;"><?php echo Text::get('regular-twitter'); ?></a></li>
            <li class="facebook"><a target="_blank" href="<?php echo htmlspecialchars($facebook_url) ?>" onclick="alert('desactivado hasta puesta en marcha'); return false;"><?php echo Text::get('regular-facebook'); ?></a></li>
            <li onclick="$(this).children('input').focus(); return false;" class="url"><span>URL: </span> <input type="text" onfocus="this.select();" readonly="readonly" size="35" value="<?php echo htmlspecialchars($share_url) ?>" /></li>
        </ul>
    </div>
	
    <div class="right">
        <h<?php echo $level+1 ?>><?php echo Text::get('project-spread-header'); ?></h<?php echo $level+1 ?>>
        <ul>
        	<li class="proyecto">
            <a target="_blank" id="a-proyecto" href="#proyecto" title="">Widget del proyecto</a>
            <div style="display: none;">               
            <div id="proyecto" class="widget projects" style="width:600px;height:560px;overflow:auto;">
                    <h2 class="widget-title">PUBLICA EL WIDGET DEL PROYECTO</h2>
                    <div class="widget-porject-legend">Copia y pega el código en tu web o blog y ayuda a difundir este proyecto</div>
                    <?php echo new View('view/project/widget/embed.html.php', array('project'=>$project)) ?>
                </div>
            </div>
            </li>
         
        </ul>
    </div>
</div>