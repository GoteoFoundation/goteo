<?php
/*

    <div class="container general-dashboard spacer">
            <h2><?= $this->text('project-menu-home') ?></h2>
            <?php if($this->projects): ?>
            <form id="selector-form" name="selector_form" action="<?php echo '/dashboard/projects/'.$this->section.'/select'; ?>" method="post">
                <select id="selector" name="project" onchange="document.getElementById('selector-form').submit();">
                <?php foreach ($this->projects as $this->project) : ?>
                    <option value="<?php echo $this->project->id; ?>"<?php if ($this->project->id == $_SESSION['project']) echo ' selected="selected"'; ?> ><?php echo $this->project->name; ?></option>
                <?php endforeach; ?>
                </select>
            </form>
    <?php else : ?>
    <p><?= $this->text('dashboard-no-projects') ?></p>
    <?php endif; ?>
    </div>

 */

?>

<h4><?= $this->project->name ?></h4>
<img class="img-responsive auto-project-image" src="<?= $this->project->image->getLink(150, 75, true); ?>">
<hr>

<ul class="nav nav-pills nav-stacked">
  <?php $this->section('dashboard-project-menu-list') ?>

  <li role="presentation"<?= $this->section === 'summary' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/summary"><i class="fa fa-eye" title=""></i> <?= $this->text('dashboard-menu-activity-summary') ?></a>
  </li>
  <li role="presentation">
    <a href="/project/<?= $this->project->id ?>" target="_blank"><i class="fa fa-external-link" title="<?= $this->text('regular-preview') ?>"></i> <?= $this->text('regular-preview') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'edit' ? ' class="active"' : '' ?>>
    <a href="/project/edit/<?= $this->project->id ?>" target="_blank"><i class="fa fa-edit" title="<?= $this->text('regular-edit') ?>"></i> <?= $this->text('regular-edit') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'images' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/images"><i class="fa fa-image" title="<?= $this->text('images-main-header') ?>"></i> <?= $this->text('images-main-header') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'updates' ? ' class="active"' : '' ?>>
    <a href="/dashboard/projects/updates/select?project=<?= $this->project->id ?>" target="_blank"><i class="fa fa-edit" title="<?= $this->text('dashboard-menu-projects-updates') ?>"></i> <?= $this->text('dashboard-menu-projects-updates') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'supports' ? ' class="active"' : '' ?>>
    <a href="/dashboard/projects/supports/select?project=<?= $this->project->id ?>" target="_blank"><i class="fa fa-edit" title="<?= $this->text('dashboard-menu-projects-supports') ?>"></i> <?= $this->text('dashboard-menu-projects-supports') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'rewards' ? ' class="active"' : '' ?>>
    <a href="/dashboard/projects/rewards/select?project=<?= $this->project->id ?>" target="_blank"><i class="fa fa-edit" title="<?= $this->text('dashboard-menu-projects-rewards') ?>"></i> <?= $this->text('dashboard-menu-projects-rewards') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'messengers' ? ' class="active"' : '' ?>>
    <a href="/dashboard/projects/messengers/select?project=<?= $this->project->id ?>" target="_blank"><i class="fa fa-edit" title="<?= $this->text('dashboard-menu-projects-messegers') ?>"></i> <?= $this->text('dashboard-menu-projects-messegers') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'analytics' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/analytics"><i class="fa fa-pie-chart" title="<?= $this->text('dashboard-menu-projects-analytics') ?>"></i> <?= $this->text('dashboard-menu-projects-analytics') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'materials' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/materials"><i class="fa fa-beer" title="<?= $this->text('project-share-materials') ?>"></i> <?= $this->text('project-share-materials') ?></a>
  </li>

  <?php $this->stop() ?>
  <hr>

  <li role="presentation">
    <a href="/dashboard/activity"><i class="fa fa-reply" title="<?= $this->text('profile-my_projects-header') ?>"></i> <?= $this->text('profile-my_projects-header') ?></a>
  </li>

</ul>

