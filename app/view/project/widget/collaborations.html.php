<?php
use Goteo\Library\Text;

$level = (int) $vars['level'] ?: 3;

$project = $vars['project'];

?>
<div class="widget project-collaborations collapsable" id="project-collaborations">

    <h<?php echo $level + 1?> class="supertitle"><?php echo Text::get('project-collaborations-supertitle'); ?></h<?php echo $level + 1 ?>>

    <h<?php echo $level ?> class="title"><?php echo Text::get('project-collaborations-title'); ?></h<?php echo $level ?>>

    <ul>
        <?php foreach ($project->supports as $support) : ?>

        <li class="support <?php echo htmlspecialchars($support->type) ?>">
            <strong><?php echo htmlspecialchars($support->support) ?></strong>
            <p><?php echo htmlspecialchars($support->description) ?></p>
            <a class="button green" href="/project/<?php echo $project->id; ?>/messages?msgto=<?php echo $support->thread ?>"><?php echo Text::get('regular-collaborate'); ?></a>
        </li>
        <?php endforeach ?>
    </ul>

    <a class="more" href="/project/<?php echo $project->id; ?>/needs-non"><?php echo Text::get('regular-see_more'); ?></a>

</div>
