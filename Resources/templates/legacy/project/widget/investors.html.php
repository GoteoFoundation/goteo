<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$project = $vars['project'];

$level = (int) $vars['level'] ?: 3;

$worthcracy = Worth::getAll();

$investors = $project->agregateInvestors();
?>
<div class="widget project-investors collapsable">

    <h<?php echo $level+1 ?> class="supertitle"><?php echo Text::get('project-side-investors-header'); ?> (<?php echo count($investors); ?>)</h<?php echo $level+1 ?>>

        <div class="investors">
        <ul>
            <?php $c=1; // limitado a 6 cofinanciadores en el lateral
            foreach ($investors as $user): ?>
            <li><?php echo View::get('user/widget/supporter.html.php', array('user' => $user, 'worthcracy' => $worthcracy)) ?></li>
            <?php if ($c>5) break; else $c++;
            endforeach ?>
        </ul>

        <a class="more" href="/project/<?php echo $project->id; ?>/supporters"><?php echo Text::get('regular-see_more'); ?></a><br />

        </div>

    <div class="side-worthcracy">
    <?php include __DIR__ . '/../../worth/base.html.php' ?>
    </div>
</div>
