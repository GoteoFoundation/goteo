<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$worthcracy = Worth::getAll();

$investors = array();

foreach($project->investors as $investor) {
    $investors[$investor->user] = (object) array(
        'user' => $investor->user,
        'name' => $investor->name,
        'avatar' => $investor->avatar,
        'projects' => $investor->projects,
        'worth' => $investor->worth,
        'amount' => $investors[$investor->user]->amount + $investor->amount,
        'date' => !empty($investors[$investor->user]->date) ?$investors[$investor->user]->date : $investor->date,
        'droped' => !empty($investors[$investor->user]->droped) ?$investors[$investor->user]->droped : $investor->droped,
        'campaign' => !empty($investors[$investor->user]->campaign) ?$investors[$investor->user]->campaign : $investor->campaign
    );
}

?>
<div class="widget project-investors collapsable">
    
    <h<?php echo $level+1 ?> class="supertitle"><?php echo Text::get('project-side-investors-header'); ?> (<?php echo $project->num_investors; ?>)</h<?php echo $level+1 ?>>

        <div class="investors">
        <ul>
            <?php $c=1; // limitado a 6 cofinanciadores en el lateral
            foreach ($investors as $user): ?>
            <li><?php echo new View('view/user/widget/supporter.html.php', array('user' => $user, 'worthcracy' => $worthcracy)) ?></li>
            <?php if ($c>5) break; else $c++;
            endforeach ?>
        </ul>

        <a class="more" href="/project/<?php echo $project->id; ?>/supporters"><?php echo Text::get('regular-see_more'); ?></a><br />

        </div>

    <div class="side-worthcracy">
    <?php include 'view/worth/base.html.php' ?>
    </div>
</div>