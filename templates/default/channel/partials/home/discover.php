<?php

/// por cada categoria que tengamos en $this->discover
// especial para los byreward, que es una caja por cada icono

foreach ($this->discover as $cat => $projects) :
    if ($cat == 'byreward') :
        foreach ($projects as $icon=>$projs) : ?>
<div id="channel-projects-<?php echo $cat ?>-<?php echo $icon ?>" class="content_widget channel-projects rounded-corners" style="display: none;">

    <h2><?php echo $this->searcher[$cat] . ': ' . $this->icons[$icon]->name ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($projs as $project) {
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo $this->insert('project/widget/tiny_project', ['project' => $project]);
        } ?>
    </ul>

    <div class="see_more"><a href="/discover"><?= $this->text('regular-see_more') ?></a></div>
</div>
    <?php endforeach;
    else : ?>
<div id="channel-projects-<?php echo $cat ?>" class="content_widget channel-projects rounded-corners" style="display: none;">

    <h2><?php echo $this->searcher[$cat] ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($projects as $project) {
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo $this->insert('project/widget/tiny_project', ['project' => $project]);

        } ?>
    </ul>

    <div class="see_more"><a href="/discover"><?= $this->text('regular-see_more') ?></a></div>
</div>
    <?php endif; ?>
<?php endforeach; ?>
