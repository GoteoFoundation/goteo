<ul class="data-list">
    <li>
        <h5><?= $this->text('project-obtained') ?></h5>
        <p><strong><?= amount_format($this->project->amount) ?></strong></p>
    </li>
    <li class="divider"></li>
    <li>
        <h5><?= $this->project->num_investors . ' ' . $this->text('project-menu-supporters') ?></h5>
    </li>
    <?php
    if($this->project->project_location):
        // TODO: link this to some map?
        ?>
        <li class="divider"></li>
        <li>
            <h6><a><i class="fa fa-map-marker"></i> <?= $this->project->project_location ?></a></h6>
        </li>
    <?php endif ?>
</ul>
