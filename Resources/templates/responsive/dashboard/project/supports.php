<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-supports') ?></h2>
    <p><?= $this->text('guide-project-supports') ?></p>

    <?php
    if($this->supports):
        foreach($this->supports as $support):
            $comments = $support->totalThreadResponses();
     ?>
        <div class="panel section-content" data-id="<?= $support->id ?>">
          <div class="panel-body">
            <h3 class="data-support"><?= $support->support ?></h3>
            <p class="data-description"><?= nl2br($support->description) ?></p>
              <p>
                <button class="btn pull-right btn-default" data-toggle="modal" data-target="#edit-modal"><i class="icon icon-1x icon-edit"></i> <?= $this->text('regular-edit') ?></button>
                <button class="btn btn-<?= $comments ? 'lilac' : 'default' ?>" data-toggle="collapse"  data-target="#comments-<?= $support->id ?>"><i class="icon-1x icon icon-partners"></i> <?= $this->text('regular-num-comments', $comments) ?></button>
              </p>
              <div class="comments collapse" id="comments-<?= $support->id ?>">
                <?php if($comments): ?>
                  <?= $this->insert('dashboard/project/partials/comments', ['comments' => $support->getThreadResponses()]) ?>
                <?php else: ?>
                    <p class="alert alert-danger"><?= $this->text('dashboard-project-support-no-responses') ?></p>
                <?php endif ?>
              </div>
          </div>
        </div>
    <?php endforeach ?>

    <?php else: ?>
        <p class="alert alert-danger"><?= $this->text('dashboard-project-support-empty') ?></p>
    <?php endif ?>

    <p>
        <button class="btn btn-lg btn-cyan" data-toggle="modal" data-target="#edit-modal"><i class="fa fa-plus"></i> <?= $this->text('dashboard-project-support-add') ?></button>
    </p>


    <h2><?= $this->text('dashboard-project-support-responses') ?></h2>

  </div>
</div>

<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= $this->text('dashboard-menu-projects-supports') ?></h4>
      </div>
      <div class="modal-body">
        <?php if($this->errors): ?>
            <div class="text-danger"><?= implode("\n<br>\n", $this->errors) ?></div>
        <?php endif ?>

        <?= $this->form_form($this->raw('editForm')) ?>

        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    $('#edit-modal').on('show.bs.modal', function (event) {
        var $modal = $(this)
        var $button = $(event.relatedTarget) // Button that triggered the modal
        var $section = $button.closest('.section-content');
        if($section.length) {
            $modal.find('#autoform_support').val($section.find('.data-support').text());
            $modal.find('#autoform_description').val($section.find('.data-description').text())
            $modal.find('#autoform_id').val($section.data('id'))
        }
    });
    $('#edit-modal').on('hidden.bs.modal', function () {
        $(this).find('input,textarea').val('');
    });

    <?php if($this->editFormSubmitted): ?>
        $('#edit-modal').modal('show');
        $('#edit-modal').modal('show');
    <?php endif ?>
});

// @license-end
</script>

<?php $this->append() ?>
