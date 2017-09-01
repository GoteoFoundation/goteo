<div class="media">
    <div class="media-left">
        <img title="<?= $this->name ?>" src="<?= $this->avatar ?>" class="img-circle">
    </div>
    <div class="media-body">
        <p>
            <strong><?= $this->name ?></strong>
            <em><?= $this->date ?></em>
        </p>
        <p><?= nl2br($this->message) ?></p>
    </div>
</div>
