<div class="fluid-container workshops-container">
	<?= $this->related_workshops ? $this->insert('partials/components/workshops_slider', [
			'title' => $this->text('workshop-related'),
			'workshops' => $this->related_workshops
	]) : '' ?> 
</div>
<div class="fluid-container workshops-container">
	<?= $this->channel->getWorkshops() ? $this->insert('partials/components/workshops_slider', [
			'title' => $this->text('node-workshops-title'),
			'workshops' => $this->channel->getWorkshops()
	]) : '' ?>
</div>
