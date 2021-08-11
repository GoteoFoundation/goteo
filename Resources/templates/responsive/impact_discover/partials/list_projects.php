<?php
  $projects = $this->projects;
 ?>

<div id="impact-discover-projects" class="section impact-discover-projects active">
    <div class="container">
      <?= $this->insert('impact_discover/partials/list_project_rows', ['project' => $projects]) ?>
    </div>
</div>
