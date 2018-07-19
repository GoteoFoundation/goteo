<div class="slider slider-polaroid" id="slider-polaroid">
<?php foreach($this->stories as $story): ?>
	<?php $rotate=rand(-2,2) ?>
	<?php $title=explode("/", $story->title);
 ?>
	<div class="polaroid-container text-center" style="transform: rotate(<?= $rotate.'deg' ?>)">
	    <div class="polaroid">
	        <img src="<?= $story->getPoolImage()->getlink(360, 355, false) ?>" class="img-responsive">
	        <?php if($story->sphere): ?>
	        	<?php 
					$sphere = $story->getSphere();
				?>
		        <div class="sphere">
			        <?php if($sphere): ?>
			        	<?php $sphere_name=$sphere->name; ?>
			            <img class="center-block" src="<?= $sphere->getImage()->getLink(50, 50, false) ?>">
			        <?php endif; ?>
			    </div>
		    <?php endif; ?>
		    <div class="title">
		    	<?= $title[0] ?>
		    </div>
	    </div>
	    <div class="subtitle">
	    	<?= $title[1].' / '.$sphere_name ?>
	    </div>
	</div>
<?php endforeach; ?>

</div>