<div id="modal-content-reset" class="hidden" >
	<div class="modal-header">
	    <?= $this->error ?>
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    <h4 class="modal-title" id="UrlModalLabel"><?= $this->text('dashboard-modal-edit-url') ?></h4>
	</div>
	<div class="modal-body">
	    <form name="save-url-form" id="save-url-form" action="">
	    	<input type="url" class="form-control" placeholder="<?= $this->text('dashboard-modal-placeholder-edit-url') ?>" id="edit-url" name="edit-url" value="" required>
	    	<input type="hidden" value="" id="reward-id" >
	    </form>
	</div>
	<div class="modal-footer">
	    <button type="button" id="btn-save-url" class="btn btn-success"><?= $this->text('regular-save') ?></button>
	</div>
</div>