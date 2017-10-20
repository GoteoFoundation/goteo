<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>
<div class="dashboard-content">

  <div class="inner-container">
<?php if($this->messages): ?>
    <h2><?= $this->text('regular-messages') ?></h2>
    <?php
    foreach($this->messages as $message):
        $comments = $message->totalResponses($this->get_user());
        $type = $message->getType();
        $icon = '';
        if($type == 'project-private') $icon = 'partners';
        if($type == 'project-support') $icon = 'supports';

    ?>
    <div class="panel section-content">
      <div class="panel-body">
        <h4 class="data-support"><a href="/project/<?= $message->project ?>"><i class="icon icon-2x icon-<?= $icon ?>"></i> <?= $message->getTitle() ?></a> <small class="pull-right"><?= $this->text('message-'. $type ) ?></small></h4>
          <p class="data-description"><?= $this->markdown($message->message) ?></p>
          <p>
              <button class="btn btn-xs btn-<?= $comments ? 'info' : 'default' ?>" data-toggle="collapse"  data-target="#comments-<?= $message->id ?>"><i class="icon-1x icon icon-partners"></i> <?= $this->text('regular-num-comments', $comments) ?></button>

          </p>
          <div class="comments collapse" id="comments-<?= $message->id ?>">
            <?php if($comments): ?>
              <?= $this->insert('dashboard/project/partials/comments/full', [
                    'comments' => $message->getResponses($this->get_user()),
                    'thread' => $message->id,
                    'private' => $message->private,
                    'project' => $message->project,
                    'type' => $type
                    ]) ?>
            <?php else: ?>
                <p class="alert alert-danger"><?= $this->text('dashboard-project-support-no-responses') ?></p>
            <?php endif ?>
          </div>
      </div>
    </div>
  <?php endforeach ?>
<?php else: ?>
    <blockquote><?= $this->text('dashboard-message-no-message') ?></blockquote>
<?php endif ?>
  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

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
