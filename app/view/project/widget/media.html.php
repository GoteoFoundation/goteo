<?php
use Goteo\Library\Text;

if ($this['project']->media->url):

	if(!empty($this['project']->secGallery['play-video'][0])):
		$img_url=$this['project']->secGallery['play-video'][0]->imageData->getLink(620, 380);
?>
        <script type="text/javascript">
            function loadVideo() {
                var vid = document.getElementById('video_holder');
                vid.innerHTML = '<?php echo $this['project']->media->getEmbedCode(false, null,1); ?>';
            }
        </script>
		<div class="widget project-media" style="position:relative;" id="video_holder">
			<img src="<?php echo $img_url; ?>" width="620" height="380"/>
			<div onclick="loadVideo()" class="video_button"><img src="<?php echo SRC_URL; ?>/view/css/project/widget/play.png" width="6"style="margin-right:12px;"/><?php echo Text::get('project-media-play_video'); ?></div>
		</div>
<?php
	else:
?>
		<div class="widget project-media" <?php if ($this['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
	    <?php echo $this['project']->media->getEmbedCode($this['project']->media_usubs, \LANG); ?>
		</div>
<?php
	endif;
elseif($this['project']->image):
?>
        <div class="widget project-media" style="position:relative;" id="video_holder">
        <img src="<?php echo $this['project']->image->getLink(620, 380); ?>" width="620" height="380"/>
        </div>
<?php
endif;
