<?php
$approved = $this->project->isApproved();
$label_color = $approved && !$this->project->isFailed() ? 'cyan' : 'danger';
$validation = null;
if(!$approved) {
    $validation = $this->project->getValidation();
}
?>
<div class="project-widget micro" id="project-<?= $this->project->id ?>">
    <a class="img-link" href="/project/<?= $this->project->id ?>">
        <img class="img-project" src="<?= $this->project->image->getLink(240, 240, true); ?>">
    </a>
    <div class="content">
        <?php if($this->admin): ?>
            <?php if($validation): ?>
                <span class="label label-<?= $label_color ?>"><?= $this->project->getTextStatus() ?></span>
                <a href="/dashboard/project/<?= $this->project->id ?>/<?= key($validation->errors) ?>?validate" title="<?= $this->text('project-validation-errors') ?>"><?= $this->percent_span($validation->global) ?></a>
                <?php if($validation->global < 100): ?>
                    <p class="error"><a href="/dashboard/project/<?= $this->project->id ?>/<?= key($validation->errors) ?>?validate" title="<?= $this->text('project-validation-errors') ?>"><?= $this->text('project-validation-error-' . current($validation->errors)) ?></a></p>
                <?php endif ?>
            <?php else: ?>
                <span class="label label-<?= $label_color ?>"><?= $this->project->getTextStatus() ?></span>
            <?php endif ?>
            <?php if($this->is_admin()): ?>
                <form method="post" action="/admin/users/impersonate/<?= $this->project->owner ?>" class="pull-right">
                    <input type="hidden" name="id" value="<?= $this->project->owner ?>">
                <button type="submit" class="btn btn-xs btn-orange" title="<?= $this->text('admin-impersonate-user', $this->project->getOwner()->name) ?>"><i class="fa fa-user-md"></i></button>
                </form>
            <?php endif ?>
        <?php endif ?>
        <div class="title"><a href="/project/<?= $this->project->id ?>"><?= $this->text_truncate($this->project->name, 80); ?></a></div>
    </div>
</div>
