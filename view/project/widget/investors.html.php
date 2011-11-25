<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$supporters = count($project->investors);

$worthcracy = Worth::getAll();

?>
<div class="widget project-investors collapsable">
    
    <h<?php echo $level+1 ?> class="supertitle"><?php echo Text::get('project-side-investors-header'); ?> (<?php echo $supporters; ?>)</h<?php echo $level+1 ?>>

        <div class="investors">
        <ul>
            <?php $c=1; // limitado a 6 cofinanciadores en el lateral
            foreach ($project->investors as $investor): ?>
            <li><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
            <?php if ($c>5) break; else $c++;
            endforeach ?>
        </ul>

        <a class="more" href="/project/<?php echo $project->id; ?>/supporters"><?php echo Text::get('regular-see_more'); ?></a><br />

        </div>

    <div class="side-worthcracy">
    <?php include 'view/worth/base.html.php' ?>
    </div>
</div>