<ul class="project-filters list-inline center-block text-center">
  <?php $this->section('project-filters-items') ?>
    <li class="<?= $this->filter === 'promoted' ? 'active' : ''?>" data-status="promoted">
        <a href="/discover/promoted"><?= $this->text('home-projects-team-favourites') ?></a>
    </li>
    <li class="<?= $this->filter === 'outdated' ? 'active' : ''?>" data-status="outdated">
        <a href="/discover/outdated"><?= $this->text('home-projects-outdate') ?></a>
    </li>
    <li class="<?= $this->filter === 'recent' ? 'active' : ''?>" data-status="recent">
        <a href="/discover/recent"><?= $this->text('discover-group-recent-header') ?></a>
    </li>
    <li class="<?= $this->filter === 'popular' ? 'active' : ''?>" data-status="popular">
        <a href="/discover/popular"><?= $this->text('discover-group-popular-header') ?></a>
    </li>
    <li class="<?= $this->filter === 'succeeded' ? 'active' : ''?>" data-status="succeeded">
        <a href="/discover/succeeded"><?= $this->text('discover-group-success-header') ?></a>
    </li>
    <li class="<?= $this->filter === 'fulfilled' ? 'active' : ''?>" data-status="fulfilled">
        <a href="/discover/fulfilled"><?= $this->text('regular-success_mark') ?></a>
    </li>
    <li class="<?= $this->filter === 'archived' ? 'active' : ''?>" data-status="archived">
        <a href="/discover/archived"><?= $this->text('discover-group-archive-header') ?></a>
    </li>
  <?php $this->stop() ?>
</ul>
