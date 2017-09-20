<div class="media">
    <div class="media-left">
        <img title="<?= $this->get_user()->name ?>" src="<?= $this->get_user()->avatar->getLink(60, 60, true) ?>" class="img-circle">
    </div>
    <div class="media-body ajax-message" data-url="/api/messages" data-project="<?= $this->project ?>" data-admin="<?= (bool)$this->admin ?>">
        <div class="form-group">
            <input name="subject" required class="form-control" placeholder="<?= $this->text('contact-subject-field') ?>">
        </div>
        <div class="form-group">
            <textarea name="body" rows="6" required class="form-control" placeholder="<?= $this->text('contact-message-field') ?>"></textarea>
        </div>

        <div class="form-group">
            <p class="text-muted recipients" data-private="<?= $this->text('support-private-message') ?>"><span class="text"><?= $this->text('support-private-message') ?></span></p>
            <p class="text-danger hidden error-message"></p>
            <button class="btn btn-cyan send-message"><i class="fa fa-paper-plane"></i> <?= $this->text('regular-send_message') ?></button>
        </div>

        <input type="hidden" name="reward" value="<?= $this->reward ?>">
        <input type="hidden" name="filter" value="<?= $this->filter ?>">
        <input type="hidden" name="users" value="<?= implode(',', $this->a('users')) ?>">

    </div>
</div>
