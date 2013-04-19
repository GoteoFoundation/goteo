<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Project,
    Goteo\Model\Image;

$bodyClass = 'projects';

$call = $this['call'];
// reordenar proyectos: random pero si ya no está en campaña sale al final
$final = array();


foreach ($call->projects as $key => $proj) {

    if ($proj->status < 3) {
        unset($call->projects[$key]);
    } elseif ($proj->status > 3) {
        $final[$proj->id] = $proj;
        unset($call->projects[$key]);
    }
}

shuffle($call->projects);
shuffle($final);

$call->projects = array_merge($call->projects, $final);

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>

<div id="main">
    <?php echo new View('view/call/widget/title.html.php', $this); ?>
    <div id="banners-social">
        <?php echo new View('view/call/widget/banners.html.php', $this) ?>
        <?php echo new View('view/call/widget/social.html.php', $this) ?>
    </div>

    <div id="info">
        <div id="content">
			
            <h2 class="title"><?php echo Text::get('call-splash-selected_projects-header') ?></h2>

            <p class="subtitle">
                <a href="/call/<?php echo $call->id ?>/info"><?php
            if ($call->status == 3 || $_GET['preview'] == 'apply') {
                // inscripción
                echo Text::get('call-splash-searching_projects', $call->user->name);
            } elseif (!empty($call->amount) || $_GET['preview'] == 'campaign') {
                //en campaña con dinero
                echo Text::html('call-splash-invest_explain', $call->user->name);
                if (!empty($call->maxdrop)) {
                    echo Text::html('call-splash-drop_limit', $call->maxdrop);
                }
            } else {
                //en campaña sin dinero, con recursos
                echo Text::recorta($call->resources, 200);
            } ?></a>
            </p>

            <ul id="project-list">
                <?php
                foreach ($call->projects as $proj) {
                    $project = Project::getMedium($proj->id);
                    $project->per_amount = round(($project->amount / $project->mincost) * 100);
                    echo new View('view/project/widget/tiny_project.html.php', array('project' => $project));
                }
                ?>
            </ul>

            <p style="padding-left: 15px;">
                <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button info"><?php echo Text::get('call-splash-more_info-button') ?></a>
            </p>
            
        </div>
<?php echo new View('view/call/side.html.php', $this); ?>
    </div>

    <?php if ($call->status > 3) : ?>
    <div id="supporters-sponsors">
        <?php echo new View('view/call/widget/supporters.html.php', $this); ?>
        <?php echo new View('view/call/widget/sponsors.html.php', $this); ?>
    </div>
    <?php endif; ?>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>