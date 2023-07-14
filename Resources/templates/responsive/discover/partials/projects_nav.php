<ul class="project-filters list-inline center-block text-center">
  <?php $this->section('project-filters-item-0') ?>
  <li class="<?= $this->filter === 'promoted' ? 'active' : ''?>" data-status="promoted">
        <a href="<?= $this->link ?>/discover/promoted?<?= $this->get_querystring() ?>" ><?= $this->text('home-projects-team-favourites') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-1') ?>
    <li class="<?= $this->filter === 'outdated' ? 'active' : ''?>" data-status="outdated">
        <a href="<?= $this->link ?>/discover/outdated?<?= $this->get_querystring() ?>" ><?= $this->text('home-projects-outdate') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-2') ?>
    <li class="<?= $this->filter === 'recent' ? 'active' : ''?>" data-status="recent">
        <a href="<?= $this->link ?>/discover/recent?<?= $this->get_querystring() ?>" ><?= $this->text('discover-group-recent-header') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-3') ?>
    <li class="<?= $this->filter === 'popular' ? 'active' : ''?>" data-status="popular">
        <a href="<?= $this->link ?>/discover/popular?<?= $this->get_querystring() ?>" ><?= $this->text('discover-group-popular-header') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-4') ?>
    <li class="<?= $this->filter === 'succeeded' ? 'active' : ''?>" data-status="succeeded">
        <a href="<?= $this->link ?>/discover/succeeded?<?= $this->get_querystring() ?>" ><?= $this->text('discover-group-success-header') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-5') ?>
    <li class="<?= $this->filter === 'fulfilled' ? 'active' : ''?>" data-status="fulfilled">
        <a href="<?= $this->link ?>/discover/fulfilled?<?= $this->get_querystring() ?>" ><?= $this->text('regular-success_mark') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-6') ?>
    <li class="<?= $this->filter === 'archived' ? 'active' : ''?>" data-status="archived">
        <a href="<?= $this->link ?>/discover/archived?<?= $this->get_querystring() ?>" ><?= $this->text('discover-group-archive-header') ?></a>
    </li>
  <?php $this->stop() ?>
  <?php $this->section('project-filters-item-7') ?>
    <li class="<?= $this->filter === 'permanent' ? 'active' : ''?>" data-status="permanent">
        <a href="<?= $this->link ?>/discover/permanent?<?= $this->get_querystring() ?>" ><?= $this->text('discover-group-permanent-header') ?></a>
    </li>
  <?php $this->stop() ?>
</ul>
