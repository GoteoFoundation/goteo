<script>

function loadVideo()

{

    var vid = document.getElementById('video_holder');

    vid.innerHTML = '<iframe width="640" height="360" src="//www.youtube.com/embed/F-N7vv0IdbQ" frameborder="0" allowfullscreen></iframe>';
   
}
</script>

<?php 

$img_url=$this['project']->secGallery['play-video'][0]->imageData->getLink(620, 380);

if (!empty($this['project']->media)): ?>
<div class="widget project-media" <?php if ($this['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
	<div style="position:relative;" id="video_holder">
		<img src="<?php echo $img_url; ?>" width="620" height="380"/>  
		<div onclick="loadVideo()" class="video_button" style="position:absolute;left:236.5px;top:169px; font-size:18px;"><img src="/view/css/project/widget/play.png" width="6"style="margin-right:12px;"/><strong>Ver video</strong></div>
	</div>

</div>
<?php endif ?>