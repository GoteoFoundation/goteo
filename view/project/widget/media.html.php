<script>

function loadVideo()

{

    var vid = document.getElementById('video_holder');

    vid.innerHTML = '<?php if(!empty($this['project']->media))  echo $this['project']->media->getEmbedCode($this['project']->media_usubs); ?>';
   
}
</script>

<?php 

if (!empty($this['project']->media)) {

	if(!empty($this['project']->secGallery['play-video'][0])) 
	{

		$img_url=$this['project']->secGallery['play-video'][0]->imageData->getLink(620, 380);

		?>
		<div class="widget project-media" <?php if ($this['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?> style="position:relative;" id="video_holder">	
			<img src="<?php echo $img_url; ?>" width="620" height="380"/>  
			<div onclick="loadVideo()" class="video_button"><img src="/view/css/project/widget/play.png" width="6"style="margin-right:12px;"/>Ver video</div>
		</div>
<?php 
	}

	else 
	{ ?>
		<div class="widget project-media" <?php if ($this['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
	    <?php echo $this['project']->media->getEmbedCode($this['project']->media_usubs); ?>
		</div>
	<?php 
	}
}
?>