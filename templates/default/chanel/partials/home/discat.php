<?php

/// por cada categoria que tengamos en $this->discover
// especial para los byreward, que es una caja por cada icono

foreach ($this->categories as $cat => $catData) : ?>
<div id="node-projects-category-<?php echo $cat ?>" class="content_widget node-projects rounded-corners" style="display: none;">

    <h2><?= $this->text('node-side-searcher-bycategory') . ': ' . $catData['name'] ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($catData['projects'] as $project) {
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo View::get('project/widget/tiny_project.html.php', array('project'=>$project));
        } ?>
    </ul>

    <div class="see_more"><a href="/discover"><?= $this->text('regular-see_more') ?></a></div>
</div>
<?php endforeach; ?>
