<?php

$default_message = 'support-' . ($this->private ? 'private_all' : 'public') . '-message';

?><div class="comments-list" id="comments-list-<?= $this->thread ?>">
  <?php foreach($this->comments as $comment): ?>
    <?= $this->insert('dashboard/project/partials/comments/item', ['comment' => $comment, 'admin' => $this->admin, 'type' => $this->type]) ?>
  <?php endforeach ?>
</div>

<div class="media">
    <div class="media-left">
        <img title="<?= $this->get_user()->name ?>" src="<?= $this->get_user()->avatar->getLink(60, 60, true) ?>" class="img-circle">
    </div>
    <div class="media-body ajax-comments" data-url="/api/comments" data-thread="<?= $this->thread ?>" data-list="#comments-list-<?= $this->thread ?>" data-project="<?= $this->project ?>" data-admin="<?= (bool)$this->admin ?>">
        <div class="form-group">
            <textarea name="message" class="form-control" placeholder="<?= $this->text('project-messages-answer_it') ?>"></textarea>
        </div>
        <?php if(!$this->private): ?>
            <div class="checkbox">
                <label><input type="checkbox" name="private"> <?= $this->text('support-set-private-message') ?></label>
            </div>
        <?php endif ?>
        <div class="form-group">
            <p class="text-muted recipients" data-private="<?= $this->text('support-private-message') ?>" data-public="<?= $this->text($default_message) ?>"><span class="text"><?= $this->text($default_message) ?></span></p>
            <p class="text-danger hidden error-message"></p>
            <button class="btn btn-cyan send-comment"><i class="fa fa-paper-plane"></i> <?= $this->text('regular-send_message') ?></button>
        </div>
        <input type="hidden" name="thread" value="<?= $this->thread ?>">
    </div>
</div>
