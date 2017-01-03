<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$bodyClass = 'project-edit';

$project = $vars['project'];

$status = View::get('project/edit/status.html.php', array('status' => $project->status, 'progress' => $project->progress));
$steps  = View::get('project/edit/steps.html.php', array('steps' => $vars['steps'], 'step' => $vars['step'], 'errors' => $project->errors, 'id_project' => $project->id));

// next step
$keys = array_keys($vars['steps']);
$next_step = $keys[ array_search($vars['step'], $keys) + 1];

if (!empty($vars['success'])) {
    Goteo\Application\Message::info($vars['success']);
} elseif ($project->status == 1) {
    Goteo\Application\Message::info(Text::get('form-ajax-info'));
}

$superform = true;
include __DIR__ . '/../prologue.html.php';

    include __DIR__ . '/../header.html.php'; ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        var hash = document.location.hash;
        if (hash != '') {
            $(hash).focus();
        }
    });

// @license-end
</script>

    <div id="sub-header">
        <div class="project-header">
            <a href="/user/<?php echo $project->owner; ?>" target="_blank"><img src="<?php echo $project->user->avatar->getLink(50, 50, true); ?>" /></a>
            <h2><span><?php echo htmlspecialchars($project->name) ?></span></h2>
            <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>
            <div class="project-by"><a href="/user/<?php echo $project->owner; ?>" target="_blank">Por: <?php echo $project->user->name; ?></a></div>
        </div>
    </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

    <div id="main" class="<?php echo htmlspecialchars($vars['step']) ?>">

        <form method="post" id="proj-superform" action="<?php echo "/project/edit/" . $vars['project']->id ?>" class="project" enctype="multipart/form-data" >

            <input type="hidden" name="view-step-<?php echo $vars['step'] ?>" value="please" />

            <?php
                echo $status;

                if (count($vars['steps']) > 1) echo $steps; // si solo se permite un paso no ponemos la navegación

                if($vars['step']) echo View::get("project/edit/{$vars['step']}.html.php", array_merge($vars, array('level' => 3, 'next' => $next_step)));

                if (count($vars['steps']) > 1) echo $steps; // si solo se permite un paso no ponemos la navegación

            ?>

            <script type="text/javascript">
            // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
            $(function () {
                $('div.superform').bind('superform.ajax.done', function (event, html, new_el) {
                    $('li#errors').superform(html);
                });
            });
            // @license-end
            </script>

        </form>

    </div>

    <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
