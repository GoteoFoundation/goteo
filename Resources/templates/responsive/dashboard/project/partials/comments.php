<?php foreach($this->comments as $comment): ?>
<div class="media">
    <div class="media-left">
        <img title="<?= $comment->getUser()->name ?>" src="<?= $comment->getUser()->avatar->getLink(60, 60, true) ?>" class="img-circle">
    </div>
    <div class="media-body">
        <p>
            <strong><?= $comment->getUser()->name ?></strong>
            <em><?= date_formater($comment->date, true) ?></em>
        </p>
        <p><?= nl2br($comment->message) ?></p>
    </div>
</div>
<?php endforeach ?>

<div class="media">
    <div class="media-left">
        <img title="<?= $this->get_user()->name ?>" src="<?= $this->get_user()->avatar->getLink(60, 60, true) ?>" class="img-circle">
    </div>
    <div class="media-body">
        <div class="form-group">
            <textarea name="message" class="form-control" placeholder="<?= $this->text('project-messages-answer_it') ?>"></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-cyan"><i class="fa fa-paper-plane"></i> <?= $this->text('regular-send_message') ?></button>
        </div>
        <input type="hidden" name="thread" value="<?= $this->thread ?>">
    </div>
</div>
