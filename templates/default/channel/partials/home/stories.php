<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$stories = $vars['stories'];
?>
<div id="node-projects-story" class="content_widget node-projects rounded-corners" <?php if ($vars['hide_stories']) : ?>style="display:none;"<?php endif; ?>>

    <h2>
    AQUI VAN HISTORIAS
    <span class="line"></span>
    </h2>

    <ul>
        <?php foreach ($stories as $story) {
            $project = $story->projectData;
            $project->per_amount = round(($project->amount / $project->mincost) * 100);
            echo $story->title;
            echo \trace($story);
        }?>
    </ul>

    <div class="see_more"><a href="/discover"><?php echo Text::get('regular-see_more') ?></a></div>
</div>
