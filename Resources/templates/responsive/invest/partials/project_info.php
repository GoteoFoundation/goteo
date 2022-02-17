<div class="jumbotron project-info">
    <div class="container">
        <div class="row invest-container">
            <h1 class="project-name">
                <a href="/project/<?= $this->project->id ?>">
                    <?= $this->ee($this->project->name) ?>
                </a>
            </h1>
            <p class="project-subtitle">
                <?= $this->project->subtitle ?>
            </p>

            <div class="project-owner pull-left">
                <a href="/user/profile/<?= $this->project->user->id ?>"><?= $this->text('regular-by')." ". $this->project->user->name ?></a>
            </div>

            <?php if ($this->project_categories) : ?>
                <div class="project-tags pull-left hidden-xs hidden-sm">
                    <ul class="comma-list">
                        <?php foreach($this->project_categories as $key => $value): ?>
                            <li>
                                <a href="/discover?category=<?= $key ?>"><?= $value ?></a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>
      </div>
    </div>
</div>
