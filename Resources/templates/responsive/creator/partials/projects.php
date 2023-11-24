<?php
$projects = $this->projects;
if (empty($projects))
    return;
?>

<section class="projects" >
    <h2><?= $this->t('regular-projects') ?></h2>

    <div class="project-grid">
        <?php foreach ($this->listOfProjects as $project) : ?>
            <?= $this->insert('project/widgets/normal', ['project' => $project]) ?>
        <?php endforeach ?>
    </div>
</section>
