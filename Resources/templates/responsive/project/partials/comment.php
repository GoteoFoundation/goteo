<div id="child-msg-<?= $this->comment->id ?>" class="row no-margin normalize-padding message child<?= ($this->comment->getUser()->id == $this->project->owner) ? ' owner' : ' no-owner' ?> no-margin normalize-padding">
    <?php if($this->comment->getUser()->id != $this->project->owner): ?>
    <div class="pull-left">
        <a href="/user/<?= $this->comment->getUser()->id ?>"><img class="avatar" src="<?= $this->comment->getUser()->avatar->getLink(45, 45, true); ?>"></a>
    </div>
    <?php endif; ?>
    <div class="pull-left user-name"><a href="/user/<?= $this->comment->getUser()->id ?>"><?= ucfirst($this->comment->getUser()->name) ?></a></div>
    <div class="pull-right time-ago">
        Hace <?= $this->comment->timeago ?>
    </div>
    <div class="msg-content">
        <?= $this->comment->message ?>
        <?php if ( $this->project && $this->project->userCanEdit($this->get_user())) : ?>
            <div>
                <a class="delete" href="/dashboard/project/<?= $this->project->id ?>/supports#comments-<?php echo $this->comment->thread; ?>"><?= $this->text('regular-manage') ?></a>
            </div>
        <?php endif; ?>
    </div>

</div>
