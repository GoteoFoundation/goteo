<?php

$project=$this->project;

if ($project->media->url):

	if(!empty($project->secGallery['play-video'][0])):
		$img_url=$project->secGallery['play-video'][0]->imageData->getLink(780, 478);
?>
        <script type="text/javascript">
            function loadVideo() {
                var vid = document.getElementById('video_holder');
                vid.innerHTML = '<?= $project->media->getEmbedCode(false, null,1); ?>';
            }
        </script>
		<div class="project-media embed-responsive embed-responsive-16by9" style="position:relative;" id="video_holder">
			<img src="<?php echo $img_url; ?>" class="img-responsive">
			<div onclick="loadVideo()" class="video_button"><img src="<?php echo SRC_URL; ?>/assets/img/project/play.png" class="img-responsive"></div>
		</div>
<?php
	else:
?>
		<div class="project-media embed-responsive embed-responsive-16by9" <?php if ($project->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
	    <?php echo $project->media->getEmbedCode($project->media_usubs, \LANG); ?>
		</div>
<?php
	endif;
elseif($project->image && $project->image->id):
?>
        <div class="project-media" style="position:relative;height:auto;" id="video_holder">
            <img class="img-responsive" src="<?php echo $project->image->getLink(620, 380); ?>" />
        </div>
<?php
    // Eliminar de la galeria si ya se ha mostrado
    // TODO: quiza esto no se deberia hacer aqui
    //       o no se deberia hacer y punto (repetir imagenes)
    if($project->gallery) {
        foreach($project->gallery as $i => $img) {
            if($img->imageData->id === $project->image->id) {
                unset($project->gallery[$i]);
            }
        }
    }
endif;
