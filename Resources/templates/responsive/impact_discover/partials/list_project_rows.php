<?php
    $projects = $this->projects;
?>
<?= $this->insert('impact_discover/partials/projects_first_row', ['projects' => array_slice($projects, 0, 3)]) ?>
<?= $this->insert('impact_discover/partials/projects_second_row', ['projects' => array_slice($projects, 3, 3)]) ?>
<?= $this->insert('impact_discover/partials/projects_third_row', ['projects' => array_slice($projects, 6, 3)]) ?>
