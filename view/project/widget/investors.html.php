<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$supporters = count($project->investors);

$worthcracy = Worth::getAll();

?>
<div class="widget project-support collapsable" id="project-collaborations">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-side-investors-header'); ?> (<?php echo $supporters; ?>)</h<?php echo $level ?>>

        <?php foreach ($project->investors as $investor) :
            echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy));
        endforeach; ?>

    <a class="vermas" href="/project/<?php echo $project->id; ?>/supporters"><?php echo Text::get('regular-see_more'); ?></a><br />

    <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'side')); ?>
    
</div>