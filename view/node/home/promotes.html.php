<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$promotes = $this['promotes'];
?>
<div id="node-projects-promote" class="content_widget node-projects rounded-corners" <?php if ($this['hide_promotes']) : ?>style="display:none;"<?php endif; ?>>

    <h2><?php echo Text::get('home-promotes-header'); ?>
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($promotes as $promo) {
            $project = $promo->projectData;
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo new View('view/project/widget/tiny_project.html.php', array('project'=>$project));
        }?>
    </ul>

</div>