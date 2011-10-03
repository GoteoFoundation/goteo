<?php
use Goteo\Library\Text;

$project = $this['project'];

//tratamos los saltos de linea y los links en las descripciones del proyecto
$project->description = nl2br(Text::urlink($project->description));
$project->about       = nl2br(Text::urlink($project->about));
$project->motivation  = nl2br(Text::urlink($project->motivation));
$project->goal        = nl2br(Text::urlink($project->goal));
$project->related     = nl2br(Text::urlink($project->related));

$level = (int) $this['level'] ?: 3;
?>
    <?php  if (count($project->gallery) > 1) : ?>
		<script>
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

    <?php if (!empty($project->gallery)): ?>
	<div id="prjct-gallery">
		<div class="prjct-gallery-container">
			<?php $i = 1; foreach ($project->gallery as $image) : ?>
			<div class="gallery-image" id="gallery-image-<?php echo $i ?>"<?php if (count($project->gallery) == 1) echo ' style="display:block;"'; ?>>
				<img src="/image/<?php echo $image->id; ?>/580/580" alt="<?php echo $project->name; ?>" />
			</div>
			<?php $i++; endforeach; ?>
		</div>
		<!-- carrusel de imagenes si hay mas de una -->
		<?php if (count($project->gallery) > 1) : ?>
			<a class="prev">prev</a>
				<ul class="slderpag">
					<?php $i = 1; foreach ($project->gallery as $image) : ?>
					<li><a href="#" id="navi-gallery-image-<?php echo $i ?>" rel="gallery-image-<?php echo $i ?>" class="navi-gallery-image">
					<?php echo htmlspecialchars($image->name) ?></a>
					</li>
					<?php $i++; endforeach ?>
				</ul>
			<a class="next">next</a>
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