<div class="section projects auto-update-projects" >
    <div class="container" id="projects-container">
        <?php if($this->projects): ?>
            <?= $this->insert('channel/partials/projects_list', [
                'projects' => $this->projects,
                'total_projects' => $this->total_projects
            ]) ?>
        <?php endif ?>
    </div>
</div>
