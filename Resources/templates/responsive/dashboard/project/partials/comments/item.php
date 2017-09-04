<?php
if(is_object($this->comment)) {
    $user = $this->comment->getUser()->id;
    $name = $this->comment->getUser()->name;
    $avatar = $this->comment->getUser()->avatar->getLink(60, 60, true);
    $date = date_formater($this->comment->date, true);
    $message = $this->comment->message;
    $id = $this->comment->id;
} else {
    $user = $this->user;
    $name = $this->name;
    $avatar = $this->avatar;
    $date = $this->date;
    $message = $this->message;
    $id = $this->id;
}
?><div class="media comment-item">
    <div class="media-left">
        <img title="<?= $name ?>" src="<?= $avatar ?>" class="img-circle">
    </div>
    <div class="media-body">
        <p>
            <strong><?= $name ?></strong>
            <em><?= $date ?></em>
        </p>
        <p><?= nl2br($message) ?></p>
        <p class="text-danger hidden error-message"></p>
    </div>
    <div class="media-right">
        <button class="btn btn-default delete-comment" data-url="/api/comments/<?= $id ?>" data-confirm="<?= $this->text('support-sure-to-delete') ?>" title="<?= $this->text('regular-delete') ?>"><i class="fa fa-fw fa-trash"></i></button>
        <button class="btn btn-default send-private" data-user="<?= $user ?>" data-name="<?= $name ?>" title="<?= $this->text('support-send-private-message') ?>"><i class="fa fa-fw fa-paper-plane"></i></button>
    </div>
</div>
