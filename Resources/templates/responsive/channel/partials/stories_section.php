<?php $channel=$this->channel; ?>

<?php if($channel->getStories()): ?>
<div class="section stories-section">
	<div class="container">
	    <?= $this->insert('partials/components/stories_slider', [
	        'stories' => $channel->getStories()
	    ]) ?>
    </div>
</div>

<?php endif; ?>
