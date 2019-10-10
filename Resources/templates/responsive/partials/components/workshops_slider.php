<div class="container workshops">
    <h2 class="title"><?= $this->title ?></h2>
    <div class="slider slider-workshops" id="slider-workshops">
    <?php foreach($this->workshops as $workshop): ?>
    	<?php $date=new Datetime($workshop->date_in); ?>
    	<?php $month=strtolower(strftime("%B",$date->getTimestamp())); ?>

        <div class="workshop-item col-md-3">
            <div class="date">
                <a class="a-unstyled" href="<?= '/workshop/'.$workshop->id ?>" >          
            		<div class="day">
            			<?= $date->format('d'); ?>
            		</div>
    	        	<div class="month">
    	        		<?= $this->text('date-'.$month); ?>
    	        	</div>
    	        	<div class="year">
    	        		<?= $date->format('Y'); ?>
    	        	</div>
                </a>
            </div>
            	<div class="title">
                    <a class="a-unstyled" href="<?= '/workshop/'.$workshop->id ?>" >
                	<?= $workshop->title ?>
                    </a>
            	</div>
            	<div class="subtitle">
                	<?= $workshop->subtitle ?>
            	</div>         
        </div>

    <?php endforeach; ?>

    </div>
</div>