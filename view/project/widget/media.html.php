<?php 

$img_url=$this['project']->secGallery['play-video'][0]->imageData->getLink(620, 380);

if (!empty($this['project']->media)): ?>
<div class="widget project-media" <?php if ($this['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
	<div style="position:relative;">
		<img src="<?php echo $img_url; ?>" width="620" height="380"/>  
		<div style="position:absolute;top:150px;left:272px;">
		<a href="#" class="button" target="_blank">Ver video</a>
		</div>
	</div>

</div>
<?php endif ?>