<?php

$this->layout('admin/communication/layout');

$this->section('admin-container-head');

?>

<?php $this->append() ?>


<?php $this->section('admin-container-body') ?>


<div class="panel section-content spacer">
  <div class="panel-body">

        <h5><?= $this->text('admin-communications-id') ?></h5>
        <p id = "communication-id"><?= $this->communication->id ?></p>

        <h5><?= $this->text('admin-mailing-subject') ?></h5>
        <p><?= $this->communication->subject ?></p>

        <h5 class="spacer-20"><?= $this->text('regular-date') ?></h5>
        <p><?= $this->communication->date ?></p>

        <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->mails, ['id', 'subject', 'lang', 'receivers', 'sent', 'failed', 'pending', 'success', 'status', 'percent'])]) ?>


    <div class="spacer-20 forms">
        <p class="buttons">
            <?php if (!$this->communication->isSent() && $this->communication->isActive()): ?>
              <a class="show-form btn btn-danger btn-lg" href="/admin/communication/cancel/<?= $this->communication->id?>"><i class="fa fa-ban"></i> <?= $this->text('admin-communications-cancel') ?></a>
            <?php elseif (!$this->communication->isActive() && !$this->communication->isSent()): ?>
              <a class="show-form btn btn-cyan btn-lg" href="/admin/communication/send/<?= $this->communication->id?>"><i class="fa fa-send"></i> <?= $this->text('admin-communications-send') ?></a>
              <a class="show-form btn btn-cyan btn-lg" href="/admin/communication/edit/<?= $this->communication->id?>"><i class="fa fa-pencil"></i> <?= $this->text('regular-edit') ?></a>
            <?php endif ?>

            <a class="show-form btn btn-cyan btn-lg" href="/admin/communication/preview/<?= $this->communication->id?>"><i class="fa fa-eye"></i> <?= $this->text('regular-preview') ?></a>
          </p>

    </div>

  </div>
</div>

  </div>
</div>

<?php $this->replace() ?>