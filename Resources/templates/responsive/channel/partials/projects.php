<div class="section projects auto-update-projects" >
    <div id="projects-container-title" class="container">
        <!-- <h2 class="title text-center">
            <?= $this->text('home-projects-title') ?>
        </h2> -->

        <!-- <?= $this->insertif('channel/partials/projects_nav') ?> -->

    </div>
    <div class="container" id="projects-container">
        <?php if($this->projects): ?>
            <?= $this->insert('channel/partials/projects_list', [
                'projects' => $this->projects,
                'total_projects' => $this->total_projects
            ]) ?>
        <?php endif ?>
    </div>
</div>
