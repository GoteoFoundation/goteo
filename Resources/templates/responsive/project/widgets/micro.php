<div class="project-widget micro" id="project-<?= $this->project->id ?>">
    <a class="img-link" href="/project/<?= $this->project->id ?>">
        <img class="img-project" src="<?= $this->project->image->getLink(240, 240, true); ?>">
    </a>
    <div class="content">
        <?php if($this->admin): ?>
            <span class="label label-danger"><?= $this->project->getTextStatus() ?></span>
            <?php
                if(!$this->project->isApproved()):
                $val = $this->project->getValidation();
                    if($val->global < 100):
            ?>
                <a href="/dashboard/project/<?= $this->project->id ?>/<?= key($val->errors) ?>?validate" title="<?= $this->text('project-validation-errors') ?>"><?= $this->percent_span($val->global) ?></a>
            <?php
                    endif;
                endif
            ?>
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
