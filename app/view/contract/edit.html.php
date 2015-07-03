<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Application\Message;

$bodyClass = 'project-edit';

$contract = $vars['contract'];

$steps  = View::get('contract/edit/steps.html.php', array('steps' => $vars['steps'], 'step' => $vars['step'], 'errors' => $contract->errors));

if (!$contract->status->owner)
    Message::info(Text::get('form-ajax-info'));

$superform = true;
include __DIR__ . '/../prologue.html.php';

    include __DIR__ . '/../header.html.php'; ?>

    <div id="sub-header">
        <div class="project-header">
            <h2><span><?php echo htmlspecialchars($contract->project_name) ?></span></h2>
        </div>
    </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

    <div id="main" class="<?php echo htmlspecialchars($vars['step']) ?>">

        <form method="post" action="<?php echo "/contract/edit/" . $contract->project ?>" class="project" enctype="multipart/form-data" >

            <input type="hidden" name="view-step-<?php echo $vars['step'] ?>" value="please" />

            <?php echo $steps ?>

            <?php if($vars['step']) echo View::get("contract/edit/{$vars['step']}.html.php", array_merge($vars, array('level' => 3))); ?>

            <?php echo $steps ?>

            <script type="text/javascript">
            $(function () {
                $('div.superform').bind('superform.ajax.done', function (event, html, new_el) {
                    $('li#errors').superform(html);
                });
            });
            </script>


        </form>

    </div>

    <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
