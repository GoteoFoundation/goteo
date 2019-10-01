<?php $channel=$this->channel; ?>

<?php if($channel->getResources()): ?>
	<div class="section resources-section">
		<div class="container">
            <h2 class="title"><?= $this->text('node-resources-title') ?></h2>
            <div class="row details">
            	<?php foreach($channel->getResources() as $resource): ?>
        		<div class="col-sm-12 col-md-3 adventage">
        			<div class="title">
        				<?php if($resource->icon): ?>
        					<span class="icon icon-<?= $resource->icon ?>"></span>
        				<?php endif; ?>
        				<span class="text">
        				    <?= $resource->title ?>
        				</span>
        			</div>
        			<div class="description">
        				<?= $resource->description ?>
        			</div>
        			<div class="action">
	        			<a target="_blank" href="<?= $resource->action_url ?>" >
	        				<?= $resource->action ?>
	        				<?php if($resource->action_icon): ?>
        						<span class="icon icon-action icon-<?= $resource->action_icon ?>"></span>
        				<?php endif; ?>
	    				</a>
    				</div>
        		</div>
        		<?php endforeach; ?>		
            </div> <!-- end row -->
		</div>
	</div>
<?php endif; ?>
