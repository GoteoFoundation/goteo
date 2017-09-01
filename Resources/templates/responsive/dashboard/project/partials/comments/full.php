<div class="comments-list" id="comments-list-<?= $this->thread ?>">
  <?php foreach($this->comments as $comment): ?>
    <?= $this->insert('dashboard/project/partials/comments/item', [
        'name' => $comment->getUser()->name,
        'avatar' => $comment->getUser()->avatar->getLink(60, 60, true),
        'date' => date_formater($comment->date, true),
        'message' => $comment->message
        ]) ?>
  <?php endforeach ?>
</div>

<div class="media">
    <div class="media-left">
        <img title="<?= $this->get_user()->name ?>" src="<?= $this->get_user()->avatar->getLink(60, 60, true) ?>" class="img-circle">
    </div>
    <div class="media-body ajax-comments" data-url="/api/comments" data-thread="<?= $this->thread ?>" data-list="#comments-list-<?= $this->thread ?>" data-project="<?= $this->project ?>">
        <div class="form-group">
            <textarea name="message" class="form-control" placeholder="<?= $this->text('project-messages-answer_it') ?>"></textarea>
        </div>
        <div class="form-group">
            <p class="text-danger hidden error-message"></p>
            <button class="btn btn-cyan send-comment"><i class="fa fa-paper-plane"></i> <?= $this->text('regular-send_message') ?></button>
        </div>
        <input type="hidden" name="thread" value="<?= $this->thread ?>">
    </div>
</div>
