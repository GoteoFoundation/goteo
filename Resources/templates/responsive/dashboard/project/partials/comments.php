<?php foreach($this->comments as $comment): ?>
<div class="media">
    <div class="media-left">
        <img title="<?= $comment->getUser()->name ?>" src="<?= $comment->getUser()->avatar->getLink(60, 60) ?>" class="img-circle">
        <strong><?= $comment->getUser()->name ?></strong>
        <em><?= date_formater($comment->date, true) ?></em>
    </div>
    <div class="media-body">
        <p><?= nl2br($comment->message) ?></p>
    </div>
</div>
<?php endforeach ?>
