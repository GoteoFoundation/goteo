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

        $("#gallery-image-1").show();
        $("#navi-gallery-image-1").addClass('active');

        $(".navi-arrow").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-gallery-image").removeClass('active');
            $(".gallery-image").hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-gallery-image-"+this.rel).addClass('active');
            $("#gallery-image-"+this.rel).show();

            var prev;
            var next;

            if (this.id == 'gallery-navi-next') {
                prev = parseFloat($("#gallery-navi-prev").attr('rel')) - 1;
                next = parseFloat($("#gallery-navi-next").attr('rel')) + 1;
            } else {
                prev = parseFloat(this.rel) - 1;
                next = parseFloat(this.rel);
            }

            if (prev < 1) {
                prev = <?php echo count($project->gallery) ?>;
            }

            if (next > <?php echo count($project->gallery) ?>) {
                next = 1;
            }

            if (next < 1) {
                next = <?php echo count($project->gallery) ?>;
            }

            if (prev > <?php echo count($project->gallery) ?>) {
                prev = 1;
            }

            $("#gallery-navi-prev").attr('rel', prev);
            $("#gallery-navi-next").attr('rel', next);
        });

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
        <?php $i = 1; foreach ($project->gallery as $image) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo $i ?>">
            <img src="/image/<?php echo $image->id; ?>/580/580" alt="<?php echo $project->name; ?>" />
        </div>
        <?php $i++; endforeach; ?>
        
        <!-- carrusel de imagenes -->
        <ul class="navi">
            <li class="prev"><a href="#" id="gallery-navi-prev" rel="<?php echo count($project->gallery) ?>" class="navi-arrow">Anterior</a></li>
            <?php $i = 1; foreach ($project->gallery as $image) : ?>
            <li><a href="#" id="navi-gallery-image-<?php echo $i ?>" rel="gallery-image-<?php echo $i ?>" class="navi-gallery-image">
                <?php echo htmlspecialchars($image->name) ?></a>
            </li>
            <?php $i++; endforeach ?>
            <li class="next"><a href="#" id="gallery-navi-next" rel="2" class="navi-arrow">Siguiente</a></li>
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
        <?php echo $project->related ?>
    </div>
    <?php endif ?>

    
</div>