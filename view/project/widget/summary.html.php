<?php
use Goteo\Library\Text;

$project = $this['project'];

//tratamos los saltos de linea y los links en las descripciones del proyecto
$project->description = nl2br(Text::urlink($project->description));
$project->about       = nl2br(Text::urlink($project->about));
$project->goal        = nl2br(Text::urlink($project->goal));
$project->related     = nl2br(Text::urlink($project->related));

$level = (int) $this['level'] ?: 3;

// este javascript no tendria que estar aqui
?>
    <script type="text/javascript">

    jQuery(document).ready(function ($) {

        $(".gallery-image").first().show();
        $(".navi-gallery-image").first().addClass('active');

        $(".navi-gallery-image").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-gallery-image").removeClass('active');
            $(".gallery-image").hide();
            /* Poner acctive a este, mostrar este*/
            $(this).addClass('active');
            $("#"+this.rel).show();
        });

    });
    </script>
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
        <?php foreach ($project->gallery as $image) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo $image->id; ?>">
            <img src="/image/<?php echo $image->id; ?>/580/580" alt="<?php echo $project->name; ?>" />
        </div>
        <?php endforeach; ?>
        
        <!-- carrusel de imagenes -->
        <ul class="navi">
            <?php foreach ($project->gallery as $image) : ?>
            <li><a href="#" rel="gallery-image-<?php echo $image->id ?>" class="navi-gallery-image">
                <?php echo htmlspecialchars($image->name) ?></a>
            </li>
            <?php endforeach ?>
        </ul>
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
        <?php echo htmlspecialchars($project->related) ?>
    </div>
    <?php endif ?>

    
</div>