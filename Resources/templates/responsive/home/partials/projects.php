<div class="section projects auto-update-projects" >
    <h2 class="title text-center">
        <?= $this->text('home-projects-title') ?>
    </h2>
    <ul class="filters list-inline center-block text-center">
        <li data-status="promoted" class="active">
            <?= $this->text('home-projects-team-favourites') ?>
        </li>
        <li data-status="outdated">
            <?= $this->text('home-projects-outdate') ?>
        </li>
        <li data-status="near">
            <?= $this->text('home-projects-near') ?>
        </li>
        <li data-status="matchfunding" class="matchfunding">
            <?= $this->text('home-projects-matchfunding') ?>
        </li>
    </ul>
    <div class="container" id="projects-container">
        <?php if($this->projects): ?>
            <?= $this->insert('home/partials/projects_list', [
                'projects' => $this->projects
            ]) ?>
        <?php endif ?>
    </div>
</div>
