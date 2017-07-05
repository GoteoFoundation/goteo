<?php

$project=$this->project;

if ($project->media->url):

    if(!empty($project->secGallery['play-video'][0])) {

        $img_url=$project->secGallery['play-video'][0]->imageData->getLink(780, 478);
        echo $this->insert('project/partials/video', ['embed' => $project->media->getEmbedCode(false, null, true), 'cover' => $img_url]);
    }
    else {
        echo $this->insert('project/partials/video', ['embed' => $project->media->getEmbedCode()]);
    }

elseif($project->image && $project->image->id):
?>
        <div class="project-media video-holder" style="position:relative;height:auto;" id="video_holder">
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
