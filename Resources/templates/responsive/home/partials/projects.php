<div class="section projects auto-update-projects" >
    <div id="projects-container-title" class="container">
        <h2 class="title text-center">
            <?= $this->text('home-projects-title') ?>
        </h2>

        <?= $this->insertif('home/partials/projects_nav') ?>

    </div>
    <div class="container" id="projects-container">
        <?php if ($this->response->getProjects()): ?>
            <?= $this->insert('home/partials/projects_list', [
                'projects' => $this->response->getProjects(),
                'total_projects' => $this->response->getTotalProjects()
            ]) ?>
        <?php endif ?>
    </div>
</div>
