<?php
    $projects = $this->projects
?>

<div class="row">
    <?php if (current($projects)): ?>
        <div class="col col-xs-12 col-sm-4">
            <?= $this->insert('impact_discover/partials/project.php', ['project' => current($projects)]) ?>
        </div>
    <?php endif; ?>

    <?php if (next($projects)): ?>
        <div class="col col-xs-12 col-sm-4">
            <?= $this->insert('impact_discover/partials/project.php', ['project' => current($projects)]) ?>
        </div>
    <?php endif; ?>

    <?php if (next($projects)): ?>
        <div class="col col-xs-12 col-sm-4">
            <?= $this->insert('impact_discover/partials/project.php', ['project' => current($projects)]) ?>
        </div>
    <?php endif; ?>
</div>
