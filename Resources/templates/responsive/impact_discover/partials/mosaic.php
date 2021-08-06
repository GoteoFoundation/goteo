<div id="impact-discover-mosaic" class="section impact-discover-mosaic">
	<div class="container">
		<h1>Busca un proyecto por Huellas o ODS</h1>

    	<div class="row" id="ods-icons">
      		<div class="col col-xs-12 col-sm12">
            </div>
        </div>

        <div class="row">
        	<?php foreach($this->projects as $project): ?>
                    <div class="col-sm-6 col-md-4  col-xs-8 spacer widget-element">

                <?= $this->insert('project/widgets/normal', [
                    'project' => $project,
                    'admin' => (bool)$this->admin
                ]) ?>
                </div>
    		<?php endforeach ?>
        </div>
    </div>
</div>