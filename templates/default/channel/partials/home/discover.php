<?php
use Goteo\Core\View,
    Goteo\Library\Text;

/// por cada categoria que tengamos en $vars['discover']
// especial para los byreward, que es una caja por cada icono

foreach ($vars['discover'] as $cat => $projects) :
    if ($cat == 'byreward') :
        foreach ($projects as $icon=>$projs) : ?>
<div id="node-projects-<?php echo $cat ?>-<?php echo $icon ?>" class="content_widget node-projects rounded-corners" style="display: none;">

    <h2><?php echo $vars['searcher'][$cat] . ': ' . $vars['icons'][$icon]->name ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($projs as $project) {
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo View::get('project/widget/tiny_project.html.php', array('project'=>$project));
        } ?>
    </ul>

    <div class="see_more"><a href="/discover"><?php echo Text::get('regular-see_more') ?></a></div>
</div>
    <?php endforeach;
    else : ?>
<div id="node-projects-<?php echo $cat ?>" class="content_widget node-projects rounded-corners" style="display: none;">

    <h2><?php echo $vars['searcher'][$cat] ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($projects as $project) {
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo View::get('project/widget/tiny_project.html.php', array('project'=>$project));
        } ?>
    </ul>

    <div class="see_more"><a href="/discover"><?php echo Text::get('regular-see_more') ?></a></div>
</div>
    <?php endif; ?>
<?php endforeach; ?>
