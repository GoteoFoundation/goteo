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

<div class="media media-project">
  <div class="media-body">
    <h4 class="media-heading"><?= $this->project->name ?></h4>
    <?= $this->project->subtitle ?>
  </div>
  <div class="media-right">
    <img class="media-object" src="<?= $this->project->image->getLink(150, 75, true); ?>">
  </div>
</div>

<ul class="nav nav-tabs">
  <li role="presentation"<?= $this->section === 'summary' ? ' class="active"' : '' ?>>
    <a href="/dashboard/projects/summary/select?project=<?= $this->project->id ?>"><?= $this->text('dashboard-menu-activity-summary') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'images' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/images"><?= $this->text('images-main-header') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'analytics' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/analytics"><?= $this->text('dashboard-menu-projects-analytics') ?></a>
  </li>
  <li role="presentation"<?= $this->section === 'materials' ? ' class="active"' : '' ?>>
    <a href="/dashboard/project/<?= $this->project->id ?>/materials"><?= $this->text('project-share-materials') ?></a>
  </li>
</ul>

