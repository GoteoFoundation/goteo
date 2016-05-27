<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageLabel">
  <div class="modal-dialog" role="document">
    <div id="modal-content" class="modal-content">      
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?= $this->text('regular-message_success') ?>
        <div class="row spacer-20">
        	<div class="col-xs-6 col-xs-offset-3 margin-2">
				<a target="_blank" href="<?= SITE_URL.'/project/'.$this->project->id.'/participate/#invest-'.$this->invest->id ?>" class="text-decoration-none" >
					<button type="button" class="btn btn-block green" value=""><?= $this->text('project-see-msg') ?></button>
				</a>
			</div>
      </div>
    </div>
  </div>
</div>