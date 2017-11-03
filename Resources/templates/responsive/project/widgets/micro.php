<?php

$label_color = 'danger';
if($this->project->isAlive()) $label_color = 'cyan';
elseif($this->project->inReview()) $label_color = 'orange';
$validation = null;
if($this->project->inEdition()) {
    $validation = $this->project->getValidation();
    foreach($validation->errors as $key => $val) {
        if($val && is_array($val) && $val[0]) {
            $error = $this->text('project-validation-error-' . $val[0]);
            break;
        }
    }
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
                <a href="/dashboard/project/<?= $this->project->id ?>/<?= $key ?>?validate" title="<?= $this->text('project-validation-errors') ?>"><?= $this->percent_span($validation->global) ?></a>
                <?php if($validation->global < 100 && $error): ?>
                    <p class="error"><a href="/dashboard/project/<?= $this->project->id ?>/<?= $key ?>?validate" title="<?= $this->text('project-validation-errors') ?>"><?= $error ?></a></p>
                <?php endif ?>
            <?php else: ?>
                <span class="label label-<?= $label_color ?>"><?= $this->project->getTextStatus() ?></span>
            <?php endif ?>

            <?php if($this->is_admin()): ?>
                <div class="btn-group pull-right">
                  <button type="button" class="btn btn-xs btn-orange dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="fa fa-cogs"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><form method="post" action="/admin/users/impersonate/<?= $this->project->owner ?>">
                    <input type="hidden" name="id" value="<?= $this->project->owner ?>">
                <button type="submit"><i class="fa fa-user-md"></i> <?= $this->text('admin-impersonate-user', $this->project->getOwner()->name) ?></button>
                </form></li>
                    <?php if (!$this->project->isApproved()) : ?>
                        <li><a href="<?php echo "/admin/projects/reject/{$this->project->id}" ?>" title="<?= $this->text('admin-project-express-discard-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-express-discard-sure'),'js') ?>');"><i class="fa fa-ban"></i> <?= $this->text('admin-project-express-discard') ?></a></li>
                    <?php endif ?>
                    <?php if ($this->project->inEdition()): ?>
                        <li><a href="<?php echo "/admin/projects/review/{$this->project->id}" ?>" title="<?= $this->text('admin-project-to-review-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-to-review-sure'), 'js') ?>');"><i class="fa fa-paper-plane-o"></i> <?= $this->text('admin-project-to-review') ?></a></li>
                        <?php endif ?>

                    <li role="separator" class="divider"></li>
                    <li><a href="/admin/projects?filtered=yes&name=&category=&node=&proj_name=&status=-3&called=&proj_id=<?= $this->project->id ?>&consultant=-1"><i class="fa fa-stethoscope"></i> <?= $this->text('admin-project-view-in-admin') ?></a></li>
                  </ul>
                </div>
            <?php endif ?>

        <?php endif ?>
        <div class="title"><a href="/project/<?= $this->project->id ?>"><?= $this->text_truncate($this->project->name, 80); ?></a></div>
    </div>
</div>
