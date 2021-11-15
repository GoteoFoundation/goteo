<?php
    $projects = $this->projects
?>
<div class="row">
    <div class="col col-xs-12 col-sm-4">
        <div class="row">
            <?php if(current($projects)): ?>
                <div href="/project/<?= current($projects)->id ?>" class="col col-xs-12 col-sm-12">
                    <?= $this->insert('impact_discover/partials/project.php', ['project' => current($projects)]) ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <?php if(next($projects)): ?>
                <div href="/project/<?= current($projects)->id ?>" class="col col-xs-12 col-sm-12">
                    <?= $this->insert('impact_discover/partials/project.php', ['project' => current($projects)]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div href="/project/<?= current($projects)->id ?>" class="col col-xs-12 col-sm-8">
        <?php if(next($projects)): ?>
            <?= $this->insert('impact_discover/partials/project.php', ['project' => current($projects)]) ?>
        <?php endif; ?>
    </div>
</div>