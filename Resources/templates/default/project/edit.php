<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this->project;

// next step
$keys = array_keys($this->steps);
$next_step = $keys[ array_search($this->step, $keys) + 1];

if (!empty($this->success)) {
    Goteo\Application\Message::info($this->success);
} elseif ($project->status == 1) {
    Goteo\Application\Message::info($this->text('form-ajax-info'));
}

$this->layout( $this->is_ajax() ? 'content' : 'layout', [
    'bodyClass' => 'project-edit',
    'superform' => true,
    'title' => $this->text('meta-title-create-project'),
    'meta_description' => $this->text('meta-description-create-project')
    ]);

$this->section('sidebar-header');
echo $this->insert(__DIR__ . '/../../responsive/project/widgets/micro.php', ['project' => $project, 'admin' => $project->userCanEdit($this->get_user())]);
$this->replace();

$this->section('content');
?>

    <?php if($this->alert): ?>
        <div style="display:none" class="ajax-alert"><?= $this->alert ?></div>
    <?php endif ?>


    <div id="sub-header">
        <div class="project-header">
            <a href="/user/<?php echo $project->owner; ?>" target="_blank"><img src="<?php echo $project->user->avatar->getLink(50, 50, true); ?>" /></a>
            <h2><span><?php echo $project->name ?></span></h2>
            <div class="project-subtitle"><?php echo $project->subtitle ?></div>
            <div class="project-by"><a href="/user/<?php echo $project->owner; ?>" target="_blank">Por: <?php echo $project->user->name; ?></a></div>
        </div>
    </div>

    <div id="main" class="<?php echo $this->step ?>">

        <form method="post" id="proj-superform" action="<?php echo "/project/edit/" . $this->project->id ?>" class="project" enctype="multipart/form-data" >

            <input type="hidden" name="view-step-<?= $this->step ?>" value="please" />

            <?php
                echo View::get('project/edit/status.html.php', array('status' => $project->status, 'progress' => $project->progress));

                $steps  = View::get('project/edit/steps.html.php', array('steps' => $this->steps, 'step' => $this->step, 'errors' => $project->errors, 'id_project' => $project->id));

                if (count($this->steps) > 1) echo $steps; // si solo se permite un paso no ponemos la navegación

                if($this->step) echo View::get("project/edit/{$this->step}.html.php", $this->vars + array('level' => 3, 'next' => $next_step));

                if (count($this->steps) > 1) echo $steps; // si solo se permite un paso no ponemos la navegación

            ?>

        </form>

    </div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        var hash = document.location.hash;
        if (hash != '') {
            $(hash).focus();
        }

        $('div.superform').bind('superform.ajax.done', function (event, html, new_el) {
            $('li#li-errors').superform(html);
        });

        // focus on the recently opened element when a add button is pressed
        $('div.superform').delegate('li.element.add.submit input[type="submit"].add', 'click', function (event) {
            $('div.superform').unbind('superform.dom.done');
            $('div.superform').bind('superform.dom.done', function (event, html, new_el) {
                var $element = $(event.target).closest('li.element').find('input[type="text"]:first');
                if($element.is('input')) {
                    $element.select();
                    $('div.superform').unbind('superform.dom.done');
                }
            });
        });
    });

// @license-end
</script>
<?php $this->append() ?>
