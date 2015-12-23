<?php
use Goteo\Library\Text;

$project = $vars['project'];

//tratamos los saltos de linea y los links en las descripciones del proyecto
$project->description = nl2br(Text::urlink($project->description));
$project->about       = nl2br(Text::urlink($project->about));
$project->motivation  = nl2br(Text::urlink($project->motivation));
$project->goal        = nl2br(Text::urlink($project->goal));
$project->related     = nl2br(Text::urlink($project->related));
$project->reward     = nl2br(Text::urlink($project->reward));

$level = (int) $vars['level'] ?: 3;

/* funcion codigo para imágenes */
$putImages = function ($images) {
    $code = '';

    if (!empty($images)) {
        foreach ($images as $image) {
            if (!empty($image->link)) {
                $code .= '<div class="free-image">
                    <a href="'.$image->link.'" target="_blank"><img src="'.$image->imageData->getLink(580, 580).'" style="width:580px" alt="image" /></a>
                </div>';
            } else {
                $code .= '<div class="free-image">
                    <img src="'.$image->imageData->getLink(580, 580).'" style="width:580px" alt="image" />
                </div>';
            }

        }
    }

    return $code;
}

?>
    <?php  if (count($project->gallery) > 1) : ?>
		<script type="text/javascript" >
			$(function(){
				$('#prjct-gallery').slides({
					container: 'prjct-gallery-container',
					paginationClass: 'slderpag',
					generatePagination: false,
					play: 0
				});
			});
		</script>
    <?php endif; ?>
<div class="widget project-summary">

    <h<?php echo $level ?>><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>

    <?php if (!empty($project->description)): ?>
    <div class="description">
<!--        <h<?php echo $level + 1?>><?php # echo Text::get('overview-field-description'); ?></h<?php echo $level + 1?>>         -->
        <?php echo $project->description; ?>
    </div>
    <?php endif ?>

    <?php if (count($project->gallery) > 1): ?>
	<div id="prjct-gallery">
		<div class="prjct-gallery-container">
			<?php $i = 1; foreach ($project->gallery as $image) : ?>
			<div class="gallery-image" id="gallery-image-<?php echo $i ?>">
				<img src="<?php echo $image->imageData->getLink(580, 580); ?>" alt="<?php echo $project->name; ?>" />
			</div>
			<?php $i++; endforeach; ?>
		</div>
		<!-- carrusel de imagenes si hay mas de una -->
        <a class="prev">prev</a>
            <ul class="slderpag">
                <?php $i = 1; foreach ($project->gallery as $image) : ?>
                <li><a href="#" id="navi-gallery-image-<?php echo $i ?>" rel="gallery-image-<?php echo $i ?>" class="navi-gallery-image">
                <?php echo htmlspecialchars($image->imageData->name) ?></a>
                </li>
                <?php $i++; endforeach ?>
            </ul>
        <a class="next">next</a>
		<!-- carrusel de imagenes -->
	</div>
    <?php elseif ($project->gallery && $project->gallery[0]->imageData) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo $i ?>"style="display:block;">
            <img src="<?php echo $project->gallery[0]->imageData->getLink(580, 580); ?>" alt="<?php echo $project->name; ?>" />
        </div>
    <?php endif; ?>



    <?php if (!empty($project->about)): ?>
    <a name="about"></a>
    <div class="about">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-about'); ?></h<?php echo $level + 1?>>
        <?php echo $project->about; ?>
    </div>
    <?php echo $putImages($project->secGallery['about']); ?>
    <?php endif ?>

    <?php if (!empty($project->motivation)): ?>
    <a name="motivation"></a>
    <div class="motivation">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-motivation'); ?></h<?php echo $level + 1?>>
        <?php echo $project->motivation; ?>
    </div>
    <?php echo $putImages($project->secGallery['motivation']); ?>
    <?php endif ?>
    <?php if (!empty($project->video->url)):  // video bajo motivación ?>
    <div class="project-motivation-video">
        <a name="motivideo"></a>
        <?php echo $project->video->getEmbedCode($project->video_usubs); ?>
    </div>
    <br />
    <?php endif ?>

    <?php if (!empty($project->goal)): ?>
    <a name="goal"></a>
    <div class="goal">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-goal'); ?></h<?php echo $level + 1?>>
        <?php echo $project->goal; ?>
    </div>
    <?php echo $putImages($project->secGallery['goal']); ?>
    <?php endif ?>

    <?php if (!empty($project->reward)): ?>
    <a name="reward"></a>
    <div class="reward">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-reward'); ?></h<?php echo $level + 1?>>
        <?php echo $project->reward ?>
    </div>
    <?php echo $putImages($project->secGallery['reward']); ?>
    <?php elseif ((empty($project->reward) && !empty($project->secGallery['reward']))) : ?>
        <a name="reward"></a>
        <h<?php echo $level + 1?> style="margin-bottom: 5px;"><?php echo Text::get('overview-field-reward'); ?></h<?php echo $level + 1?>>
        <?php echo $putImages($project->secGallery['reward']); ?>
    <?php endif; ?>

    <?php if (!empty($project->related)): ?>
    <a name="related"></a>
    <div class="related">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-related'); ?></h<?php echo $level + 1?>>
        <?php echo $project->related ?>
    </div>
    <?php echo $putImages($project->secGallery['related']); ?>
    <?php endif ?>


</div>
