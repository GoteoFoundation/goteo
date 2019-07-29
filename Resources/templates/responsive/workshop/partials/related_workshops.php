<div class="fluid-container workshops-container">
	<?= $this->related_workshops ? $this->insert('partials/components/workshops_slider', [
			'title' => $this->text('workshop-related'),
			'workshops' => $this->related_workshops
	]) : '' ?>
	<div class="action">
        <a href="/contact" class="btn btn-pink">
            <?= $this->text('workshop-btn-action') ?>                
        </a>
    </div>
</div>
