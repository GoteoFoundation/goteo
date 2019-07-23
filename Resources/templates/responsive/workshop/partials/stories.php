<?php $workshop=$this->workshop; ?>

<?php if($workshop->getStories()): ?>
<div class="section stories">
	<div class="container">
	    <?= $this->insert('partials/components/stories_slider', [
	        'stories' => $workshop->getStories()
	    ]) ?>
    </div>
</div>

<?php endif; ?>