<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-support">

    <h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('project-support-supertitle'); ?></h<?php echo $level + 1 ?>>
    
    <?php switch ($project->tagmark) {
        case 'onrun':
            echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
            break;
        case 'gotit':
            echo '<div class="tagmark red">' . Text::get('regular-gotit_mark') . '</div>';
            break;
        case 'success':
            echo '<div class="tagmark red">' . Text::get('regular-success_mark') . '</div>';
            break;
    } ?>

    <?php echo new View('view/project/meter.html.php', array('project' => $project, 'level' => $level) ) ?>
    
    <div class="buttons">
	    <?php if (!empty($project->round)) : ?><h3 class="title ronda"><?php echo $project->round . Text::get('regular-round'); ?></h3><?php endif; ?>
        <?php if ($project->status == 3) : // boton apoyar solo si esta en campaña ?>
        <a class="button violet supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
        <?php else : ?>
        <a class="button view" href="/project/<?php echo $project->id ?>/updates"><?php echo Text::get('regular-see_blog'); ?></a>
        <?php endif; ?>
        <a class="more" href="/project/<?php echo $project->id; ?>/needs"><?php echo Text::get('regular-see_more'); ?></a>
    </div>
    
</div>