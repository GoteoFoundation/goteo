<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this->project;
$status = View::get('project/edit/status.html.php', array('status' => $project->status, 'progress' => $project->progress));
$steps  = View::get('project/edit/steps.html.php', array('steps' => $this->steps, 'step' => $this->step, 'errors' => $project->errors, 'id_project' => $project->id));

// next step
$keys = array_keys($this->steps);
$next_step = $keys[ array_search($this->step, $keys) + 1];

if (!empty($this->success)) {
    Goteo\Library\Message::Info($this->success);
} elseif ($project->status == 1) {
    Goteo\Library\Message::Info($this->text('form-ajax-info'));
}

$superform = true;

$this->layout("layout", [
    'bodyClass' => 'project-edit',
    'superform' => true,
    'title' => $this->text('meta-title-edit-project'),
    'meta_description' => $this->text('meta-description-edit-project')
    ]);

$this->section('content');
?>

<script type="text/javascript">
    $(function(){
        var hash = document.location.hash;
        if (hash != '') {
            $(hash).focus();
        }
    });


</script>



    <div id="sub-header">
        <div class="project-header">
            <a href="/user/<?php echo $project->owner; ?>" target="_blank"><img src="<?php echo $project->user->avatar->getLink(50, 50, true); ?>" /></a>
            <h2><span><?php echo htmlspecialchars($project->name) ?></span></h2>
            <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>
            <div class="project-by"><a href="/user/<?php echo $project->owner; ?>" target="_blank">Por: <?php echo $project->user->name; ?></a></div>
        </div>
    </div>

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

    <div id="main" class="<?php echo htmlspecialchars($this->step) ?>">

        <form method="post" id="proj-superform" action="<?php echo "/project/edit/" . $this->project->id ?>" class="project" enctype="multipart/form-data" >

            <input type="hidden" name="view-step-<?= $this->step ?>" value="please" />

            <?php
                echo $status;

                if (count($this->steps) > 1) echo $steps; // si solo se permite un paso no ponemos la navegación

                if($this->step) echo View::get("project/edit/{$this->step}.html.php", $this->vars + array('level' => 3, 'next' => $next_step));

                if (count($this->steps) > 1) echo $steps; // si solo se permite un paso no ponemos la navegación

            ?>

            <script type="text/javascript">
            $(function () {
                $('div.superform').bind('superform.ajax.done', function (event, html, new_el) {
                    $('li#errors').superform(html);
                });
            });
            </script>

        </form>

    </div>

<?php $this->replace() ?>
