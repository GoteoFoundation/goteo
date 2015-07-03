<?php
use Goteo\Library\Text;

if ($vars['project']->media->url):

	if(!empty($vars['project']->secGallery['play-video'][0])):
		$img_url=$vars['project']->secGallery['play-video'][0]->imageData->getLink(620, 380);
?>
        <script type="text/javascript">
            function loadVideo() {
                var vid = document.getElementById('video_holder');
                vid.innerHTML = '<?php echo $vars['project']->media->getEmbedCode(false, null,1); ?>';
            }
        </script>
		<div class="widget project-media" style="position:relative;" id="video_holder">
			<img src="<?php echo $img_url; ?>" width="620" height="380"/>
			<div onclick="loadVideo()" class="video_button"><img src="<?php echo SRC_URL; ?>/view/css/project/widget/play.png" width="6"style="margin-right:12px;"/><?php echo Text::get('project-media-play_video'); ?></div>
		</div>
<?php
	else:
?>
		<div class="widget project-media" <?php if ($vars['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
	    <?php echo $vars['project']->media->getEmbedCode($vars['project']->media_usubs, \LANG); ?>
		</div>
<?php
	endif;
elseif($vars['project']->image && $vars['project']->image->id):
?>
        <div class="widget project-media" style="position:relative;height:auto;" id="video_holder">
        <img src="<?php echo $vars['project']->image->getLink(620, 380); ?>" alt="" />
        </div>
<?php
    // Eliminar de la galeria si ya se ha mostrado
    // TODO: quiza esto no se deberia hacer aqui
    //       o no se deberia hacer y punto (repetir imagenes)
    if($vars['project']->gallery) {
        foreach($vars['project']->gallery as $i => $img) {
            if($img->imageData->id === $vars['project']->image->id) {
                unset($vars['project']->gallery[$i]);
            }
        }
    }
endif;
