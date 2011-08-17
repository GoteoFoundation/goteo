<?php
use Goteo\Library\Text;

$project = $this['project'];

//tratamos los saltos de linea y los links en las descripciones del proyecto
$project->description = nl2br(Text::urlink($project->description));
$project->about       = nl2br(Text::urlink($project->about));
$project->goal        = nl2br(Text::urlink($project->goal));
$project->related     = nl2br(Text::urlink($project->related));

$level = (int) $this['level'] ?: 3;
?>
    <?php if (count($project->gallery) > 1) : ?>
    <script type="text/javascript" src="/view/js/inc/navi.js"></script>
    <script type="text/javascript" >
        jQuery(document).ready(function ($) {
                navi('gallery-image', '<?php echo count($project->gallery) ?>');
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

    <?php if (!empty($project->gallery)): ?>
    <div class="gallery">
        <?php $i = 1; foreach ($project->gallery as $image) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo $i ?>"<?php if (count($project->gallery) == 1) echo ' style="display:block;"'; ?>>
            <img src="/image/<?php echo $image->id; ?>/580/580" alt="<?php echo $project->name; ?>" />
        </div>
        <?php $i++; endforeach; ?>
        
        <!-- carrusel de imagenes si hay mas de una -->
        <?php if (count($project->gallery) > 1) : ?>
        <ul class="navi">
            <li class="prev"><a href="#" id="gallery-image-navi-prev" rel="<?php echo count($project->gallery) ?>" class="navi-arrow-gallery-image">Anterior</a></li>
            <?php $i = 1; foreach ($project->gallery as $image) : ?>
            <li><a href="#" id="navi-gallery-image-<?php echo $i ?>" rel="gallery-image-<?php echo $i ?>" class="navi-gallery-image">
                <?php echo htmlspecialchars($image->name) ?></a>
            </li>
            <?php $i++; endforeach ?>
            <li class="next"><a href="#" id="gallery-image-navi-next" rel="2" class="navi-arrow-gallery-image">Siguiente</a></li>
        </ul>
    	<?php endif; ?>
        <!-- carrusel de imagenes -->
    </div>
    <?php endif ?>



    <?php if (!empty($project->about)): ?>
    <div class="about">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-about'); ?></h<?php echo $level + 1?>>
        <?php echo $project->about; ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->motivation)): ?>
    <div class="motivation">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-motivation'); ?></h<?php echo $level + 1?>>
        <?php echo $project->motivation; ?>
    </div>
    <?php endif ?>

    <?php if (!empty($project->goal)): ?>
    <div class="goal">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-goal'); ?></h<?php echo $level + 1?>>
        <?php echo $project->goal; ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->related)): ?>
    <div class="related">
        <h<?php echo $level + 1?>><?php echo Text::get('overview-field-related'); ?></h<?php echo $level + 1?>>
        <?php echo $project->related ?>
    </div>
    <?php endif ?>

    
</div>