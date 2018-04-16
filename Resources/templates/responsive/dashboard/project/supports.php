<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-supports') ?></h2>
    <div class="auto-hide">
        <div class="inner"><?= $this->text('guide-project-supports') ?></div>
        <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div>
    </div>

    <?php
    if($this->supports):
        foreach($this->supports as $support):
            $comments = $support->totalThreadResponses($this->get_user());
            $data_support = [];
            $data_description = [];
            foreach($support->getAllLangs() as $trans) {
              $data_support[] = 'data-lang-' . $trans->lang . '="' . $this->ee($trans->support) .'"';
              $data_description[] = 'data-lang-' . $trans->lang . '="' . $this->ee($trans->description) .'"';
            }
     ?>
        <div class="panel section-content" data-id="<?= $support->id ?>">
          <div class="panel-body">
            <h3 class="data-support" <?= implode(' ', $data_support) ?>><?= $support->support ?></h3>
            <p class="data-description"  <?= implode(' ', $data_description) ?>><?= nl2br($support->description) ?></p>
            <div class="btn-group pull-right">
                <button class="btn btn-default" data-toggle="modal" data-target="#edit-modal"><i class="icon icon-edit"></i> <?= $this->text('regular-edit') ?></button>
                <button class="btn btn-default delete-support"><i class="icon icon-trash"></i> <?= $this->text('regular-delete') ?></button>
                <?php if($this->languages): ?>
                  <?= $this->insert('dashboard/partials/translate_menu', ['no_title' => true, 'btn_class' => 'btn-default', 'class' =>'edit-translation', 'base_link' => '#trans-', 'skip' => [$this->project->lang], 'translated' => $support->getLangsAvailable(), 'percentModel' => $support]) ?>
                <?php endif ?>
            </div>
            <button class="btn btn-<?= $comments ? 'lilac' : 'default' ?>" data-toggle="collapse"  data-target="#comments-<?= $support->thread ?>"><i class="icon icon-partners"></i> <?= $this->text('regular-num-comments', $comments) ?></button>
            <div class="comments collapse" id="comments-<?= $support->thread ?>">
                <?php if($comments): ?>
                  <?= $this->insert('dashboard/project/partials/comments/full', [
                        'comments' => $support->getThreadResponses($this->get_user()),
                        'thread' => $support->thread,
                        'project' => $support->project,
                        'admin' => true
                        ]) ?>
                <?php else: ?>
                    <p class="spacer-10"><strong><?= $this->text('dashboard-project-support-no-responses') ?></strong></p>
                <?php endif ?>
            </div>
          </div>
        </div>
    <?php endforeach ?>

    <?php else: ?>
        <blockquote><?= $this->text('dashboard-project-support-empty') ?></blockquote>
    <?php endif ?>

    <p>
        <button class="btn btn-lg btn-orange" data-toggle="modal" data-target="#edit-modal"><i class="fa fa-plus"></i> <?= $this->text('dashboard-project-support-add') ?></button>
    </p>

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
    </div>
  </div>
</div>

<div id="trans-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= $this->text('dashboard-menu-projects-supports') ?></h4>

      </div>
      <div class="modal-body">
        <p class="title-desc"><?= $this->text('dashboard-project-support-translating', ['%LANG%' => '<strong><em>%LANG%</em></strong>', '%ORIGINAL%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>']) ?></p>

        <?php if($this->errors): ?>
            <div class="text-danger"><?= implode("\n<br>\n", $this->errors) ?></div>
        <?php endif ?>

        <?= $this->form_form($this->raw('transForm')) ?>

        </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
  // Normal edit
  $('#edit-modal').on('show.bs.modal', function (event) {
    var $modal = $(this)
    var $button = $(event.relatedTarget) // Button that triggered the modal
    var $section = $button.closest('.section-content');
    if($section.length) {
      $('#autoform_support').val($section.find('.data-support').text());
      $('#autoform_description').val($section.find('.data-description').text())
      $('#autoform_id').val($section.data('id'))
    }
  });

  var languages = <?= json_encode($this->languages) ?>;
  var txt_delete = $('#transform_remove > span').text();
  var txt_confirm = $('#transform_remove').data('confirm');

  // Translation edit
  $('.edit-translation a').on('click', function(event) {
    if($(this).closest('li').hasClass('no-bind')) return;
    event.preventDefault();
    var $modal = $('#trans-modal');
    var $section = $(this).closest('.section-content');
    var lang = $(this).attr('href').substr(7);
    var support = $section.find('.data-support').data('lang-' + lang);
    var description = $section.find('.data-description').data('lang-' + lang);
    $modal.modal('show');
    $('#transform_support').val(support);
    $('#transform_description').val(description);
    $('#transform_lang').val(lang);
    $('#transform_id').val($section.data('id'));
    $('#transform_remove>span').text(txt_delete.replace('%s', languages[lang]));
    $('#transform_remove').data('confirm', txt_confirm.replace('%s', languages[lang]));
    $('#transform_support').next('.help-text').html($section.find('.data-support').text());
    $('#transform_description').next('.help-text').html($section.find('.data-description').text());
    // console.log('translate', lang, support, description);
    $modal.find('.title-desc').html($modal.find('.title-desc').html().replace('%LANG%', languages[lang]));
  });

  $('#edit-modal').on('hidden.bs.modal', function () {
    $(this).find('input,textarea').not('[type="hidden"]').val('');
  });

  $('.delete-support').on('click', function (e) {
    e.preventDefault();
    var $form = $('#edit-modal').find('form');
    var $section = $(this).closest('.section-content');
    var id = $section.data('id');
    if(confirm('<?= $this->ee($this->text('support-sure-to-remove'), 'js') ?>')) {
      $form.find('#autoform_delete').val(id);
      $form.submit();
    }
  });

  <?php if($this->editFormSubmitted): ?>
    $('#edit-modal').modal('show');
  <?php endif ?>
  <?php if($this->transFormSubmitted): ?>
    $('#trans-modal').modal('show');
  <?php endif ?>

  // Autoexpand comment-list if in hash
  var $thread = $(location.hash);
  if($thread.length) {
    // console.log('hash',location.hash);
    $thread.collapse('show');
  }
});

// @license-end
</script>

<?php $this->append() ?>
