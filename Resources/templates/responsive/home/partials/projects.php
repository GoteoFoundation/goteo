<div class="section projects auto-update-projects" >
    <div id="projects-container-title" class="container">
        <h2 class="title text-center">
            <?= $this->text('home-projects-title') ?>
        </h2>

        <?= $this->insertif('home/partials/projects_nav') ?>

    </div>
    <div class="container" id="projects-container">
        <?php if($this->projects): ?>
            <?= $this->insert('home/partials/projects_list', [
                'projects' => $this->projects,
                'total_projects' => $this->total_projects
            ]) ?>
        <?php endif ?>
    </div>
</div>
