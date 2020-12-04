<?php $programs = $this->channel->getPrograms(); ?>

<?php foreach ($programs as $program): ?>

<!-- Modal -->
<div class="modal fade" id="programModal-<?=$program->id ?>" tabindex="-1" role="dialog" aria-labelledby="programModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="programModalLabel"><?= $program->description ?></h4>
      </div>
      <div class="modal-body">
       <?= $program->modal_description ?>
      </div>
    </div>
  </div>
</div>

<?php endforeach; ?>