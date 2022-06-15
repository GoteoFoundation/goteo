<div id="impact-discover-mosaic" class="section impact-discover-mosaic" data-view="<?= $this->view ?>">
	<div class="container">
        <div class="row">
        	<?= $this->insert('dashboard/partials/projects_widgets_list', ['projects' => $this->projects ]); ?>
        </div>
    </div>
</div>